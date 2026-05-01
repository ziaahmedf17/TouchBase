<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Event;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    /**
     * Scan all events and create notifications where today matches a trigger date.
     * Returns the count of new notifications created.
     */
    public function checkAndCreateNotifications(): int
    {
        $today   = now()->startOfDay();
        $created = 0;

        // Load all events with their client in one query
        $events = Event::with('client')->get();

        foreach ($events as $event) {
            // Skip events belonging to deleted clients
            if (! $event->client) {
                continue;
            }

            $reminderDays = $event->reminder_days ?? [];

            if (empty($reminderDays)) {
                continue;
            }

            $nextOccurrence = $event->nextOccurrence();

            foreach ($reminderDays as $days) {
                $triggerDate = $nextOccurrence->copy()->subDays((int) $days);

                // Only act if trigger date is today
                if (! $triggerDate->startOfDay()->eq($today)) {
                    continue;
                }

                $triggered = $triggerDate->toDateString();

                // Duplicate check — skip if already fired
                $exists = Notification::where('event_id', $event->id)
                    ->where('triggered_date', $triggered)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $message = $this->buildMessage($event, $nextOccurrence, (int) $days);

                try {
                    DB::transaction(function () use ($event, $message, $triggered) {
                        Notification::create([
                            'client_id'      => $event->client_id,
                            'event_id'       => $event->id,
                            'message'        => $message,
                            'is_read'        => false,
                            'triggered_date' => $triggered,
                        ]);
                    });
                    $created++;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Race condition — already inserted by a concurrent request, safe to ignore
                }
            }
        }

        return $created;
    }

    private function buildMessage(Event $event, Carbon $occurrence, int $days): string
    {
        $clientName = $event->client->name;
        $typeLabel  = $event->typeLabel();
        $dateStr    = $occurrence->format('d M Y');

        if ($days === 0) {
            return "{$clientName}'s {$typeLabel} is TODAY ({$dateStr})";
        }

        if ($days === 1) {
            return "{$clientName}'s {$typeLabel} is TOMORROW ({$dateStr})";
        }

        return "{$clientName}'s {$typeLabel} is in {$days} days ({$dateStr})";
    }
}

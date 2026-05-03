<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteToSuperAdmin extends Command
{
    protected $signature   = 'admin:promote {email}';
    protected $description = 'Promote a user to super_admin by email';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error("No user found with email: {$this->argument('email')}");
            return 1;
        }

        $user->assignRole('super_admin');
        $user->update(['account_status' => 'active']);

        $this->info("✓ {$user->name} ({$user->email}) promoted to super_admin.");
        return 0;
    }
}

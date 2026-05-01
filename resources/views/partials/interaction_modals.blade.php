{{-- ── Log Interaction Modal ─────────────────────────────────────── --}}
<div id="log-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="log-modal-title">
  <div class="modal-box">
    <div class="modal-header">
      <span id="log-modal-title">Log Interaction</span>
      <button class="modal-close" data-close-modal="log-modal" aria-label="Close">&times;</button>
    </div>
    <form method="POST" action="{{ route('interactions.store') }}">
      @csrf
      <input type="hidden" name="client_id"       id="log-client-id">
      <input type="hidden" name="notification_id"  id="log-notification-id">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="log-type">How did you reach out?</label>
            <select class="form-control" name="type" id="log-type" required>
              <option value="call">Call</option>
              <option value="whatsapp">WhatsApp</option>
              <option value="email">Email</option>
              <option value="visit">Visit</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="log-status">Outcome</label>
            <select class="form-control" name="status" id="log-status" required>
              <option value="reached_out">Reached Out</option>
              <option value="responded">Responded</option>
              <option value="no_response">No Response</option>
              <option value="follow_up_needed">Follow-up Needed</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="log-contacted-at">Date &amp; Time</label>
          <input type="datetime-local" class="form-control" name="contacted_at" id="log-contacted-at" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="log-notes">Notes <span class="text-muted">(optional)</span></label>
          <textarea class="form-control" name="notes" id="log-notes" rows="3"
                    placeholder="What did you discuss or what message did you send?"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-close-modal="log-modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Interaction</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Update Response Modal ─────────────────────────────────────── --}}
<div id="response-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="response-modal-title">
  <div class="modal-box">
    <div class="modal-header">
      <span id="response-modal-title">Update Client Response</span>
      <button class="modal-close" data-close-modal="response-modal" aria-label="Close">&times;</button>
    </div>
    <form method="POST" id="response-form">
      @csrf
      @method('PUT')
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="resp-status">Status</label>
          <select class="form-control" name="status" id="resp-status" required>
            <option value="reached_out">Reached Out</option>
            <option value="responded">Responded</option>
            <option value="no_response">No Response</option>
            <option value="follow_up_needed">Follow-up Needed</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="resp-notes">Client's Response <span class="text-muted">(optional)</span></label>
          <textarea class="form-control" name="response_notes" id="resp-notes" rows="3"
                    placeholder="What did the client say or do?"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label" for="resp-at">Response Date &amp; Time <span class="text-muted">(optional)</span></label>
          <input type="datetime-local" class="form-control" name="response_at" id="resp-at">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-close-modal="response-modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Response</button>
      </div>
    </form>
  </div>
</div>

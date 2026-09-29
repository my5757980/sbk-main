{{-- The live chat's "Show in the chat" (admins.chat_visible; see ChatService::personOf). --}}
<div class="col-sm-6">
    <div class="form-group">
        <label for="chat_visible">Show in the chat</label>
        <select class="form-control" name="chat_visible" id="chat_visible">
            <option value="" {{ ($value ?? '') === '' || $value === null ? 'selected' : '' }}>Decide from their role</option>
            <option value="yes" {{ ($value ?? '') === 'yes' ? 'selected' : '' }}>Always show</option>
            <option value="no" {{ ($value ?? '') === 'no' ? 'selected' : '' }}>Never show</option>
        </select>
        <small class="text-muted">Whether customers see this person in the chat's team list. Customers never appear there.</small>
    </div>
</div>

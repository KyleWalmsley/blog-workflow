<div class="modal-overlay" x-show="showBlockedModal" x-cloak @click.self="showBlockedModal = false">
    <div class="modal">
        <h3>Cannot Submit</h3>
        <p x-text="blockedMessage"></p>
        <div class="modal-actions">
            <button type="button" class="btn-decline" @click="showBlockedModal = false">OK</button>
        </div>
    </div>
</div>

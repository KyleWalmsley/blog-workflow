<div class="modal-overlay" x-show="showFinalizeModal" x-cloak>
    <div class="modal">
        <h3>Finalise Job?</h3>
        <p>All articles have been approved. Confirm to complete this job and notify your account manager.</p>
        <div class="modal-actions">
            <button type="button" class="btn-decline" @click="showFinalizeModal = false" :disabled="submitting">Cancel</button>
            <button type="button" class="btn-approve" @click="finalizeJob()" :disabled="submitting">
                <span x-show="!submitting">Yes, Finalise</span>
                <span x-show="submitting">Finalising...</span>
            </button>
        </div>
    </div>
</div>

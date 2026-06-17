<div class="progress-bar-wrap">
    <div class="progress-label" x-text="reviewedCount + ' of ' + totalCount + ' articles reviewed'"></div>
    <div class="progress-track">
        <div class="progress-fill" :style="'width: ' + progressPercent + '%'"></div>
    </div>
</div>

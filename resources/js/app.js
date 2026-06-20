import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('reviewPortal', (config) => ({
    jobType: config.jobType,
    blogs: (config.blogs || []).map(b => ({ ...b, declineError: false })),
    sections: (config.sections || []).map(s => ({ ...s, declineError: false })),
    token: config.token,
    csrf: config.csrf,
    reviewedCount: config.reviewedCount,
    totalCount: config.totalCount,
    showFinalizeModal: false,
    showBlockedModal: false,
    blockedMessage: '',
    submitting: false,
    completed: false,
    thankYouMessage: '',

    get isCopywriting() {
        return this.jobType === 'website_copywriting';
    },

    get allReviewed() {
        return this.reviewedCount >= this.totalCount && this.totalCount > 0;
    },

    get progressPercent() {
        if (this.totalCount === 0) return 0;
        return Math.round((this.reviewedCount / this.totalCount) * 100);
    },

    toggleBlog(index) {
        const isOpen = this.blogs[index].open;
        this.blogs.forEach(b => b.open = false);
        this.blogs[index].open = !isOpen;
    },

    toggleSection(index) {
        const isOpen = this.sections[index].open;
        this.sections.forEach(s => s.open = false);
        this.sections[index].open = !isOpen;
    },

    async setStatus(blog, status) {
        if (status === 'declined' && !blog.client_notes?.trim()) {
            blog.declineError = true;
            return;
        }
        blog.declineError = false;
        blog.status = status;
        if (status !== 'declined') {
            blog.client_notes = '';
        }

        try {
            const response = await fetch(`/review/${this.token}/blogs/${blog.id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    status: status,
                    client_notes: blog.client_notes || null,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                console.warn('Review save failed:', data.message);
                return;
            }

            this.reviewedCount = data.reviewed_count;
            blog.status = data.blog.status;
            blog.client_notes = data.blog.client_notes;
        } catch (e) {
            console.warn('Network error saving review.');
        }
    },

    async setSectionStatus(section, status) {
        if (status === 'declined' && !section.client_notes?.trim()) {
            section.declineError = true;
            return;
        }
        section.declineError = false;
        section.status = status;
        if (status !== 'declined') {
            section.client_notes = '';
        }

        try {
            const response = await fetch(`/review/${this.token}/sections/${section.id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    status: status,
                    client_notes: section.client_notes || null,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                console.warn('Review save failed:', data.message);
                return;
            }

            this.reviewedCount = data.reviewed_count;
            section.status = data.section.status;
            section.client_notes = data.section.client_notes;
        } catch (e) {
            console.warn('Network error saving review.');
        }
    },

    async saveNotes(blog) {
        if (blog.status === 'declined') {
            blog.declineError = false;
            await this.setStatus(blog, 'declined');
        }
    },

    async saveSectionNotes(section) {
        if (section.status === 'declined') {
            section.declineError = false;
            await this.setSectionStatus(section, 'declined');
        }
    },

    async submitReview() {
        if (!this.allReviewed || this.submitting) return;

        this.submitting = true;

        try {
            const response = await fetch(`/review/${this.token}/submit`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                this.blockedMessage = data.message || 'Unable to submit review.';
                this.showBlockedModal = true;
                this.submitting = false;
                return;
            }

            if (data.needs_finalization) {
                this.showFinalizeModal = true;
                this.submitting = false;
                return;
            }

            this.completed = true;
            this.thankYouMessage = data.message;
            this.submitting = false;
        } catch (e) {
            alert('Network error. Please try again.');
            this.submitting = false;
        }
    },

    async finalizeJob() {
        this.submitting = true;

        try {
            const response = await fetch(`/review/${this.token}/finalize`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrf,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.message || 'Unable to finalise job.');
                this.submitting = false;
                return;
            }

            this.showFinalizeModal = false;
            this.completed = true;
            this.thankYouMessage = data.message;
            this.submitting = false;
        } catch (e) {
            alert('Network error. Please try again.');
            this.submitting = false;
        }
    },
}));

Alpine.start();

<script>
(function () {
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean'],
            ],
            clipboard: { matchVisual: false },
        },
    });

    const initial = {!! json_encode($initialContent) !!};
    if (initial) {
        quill.root.innerHTML = initial;
    }

    const hidden = document.getElementById('content');
    const form = hidden.closest('form');

    form.addEventListener('submit', function () {
        hidden.value = quill.root.innerHTML;
    });

    // Style the Quill container to match admin theme
    const style = document.createElement('style');
    style.textContent = `
        #quill-editor .ql-toolbar { border-color: var(--border); border-radius: 8px 8px 0 0; background: var(--bg3); }
        #quill-editor .ql-container { border-color: var(--border); border-radius: 0 0 8px 8px; font-family: 'Inter', sans-serif; font-size: 14px; }
        #quill-editor .ql-editor { min-height: 340px; }
        #quill-editor .ql-editor p { margin-bottom: 0.75em; }
    `;
    document.head.appendChild(style);
})();
</script>

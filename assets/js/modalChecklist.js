document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('alpine:init', () => {
        Alpine.data('checklistModal', () => ({
            open: false,
            openAddModal: false
        }));
    });
});
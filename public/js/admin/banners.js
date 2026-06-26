function openAddModal() {
    document.getElementById('add-modal').classList.remove('display-none');
}
function closeAddModal() {
    document.getElementById('add-modal').classList.add('display-none');
}
function openEditModal(banner) {
    const editModal = document.getElementById('edit-modal');
    const editForm = document.getElementById('edit-form');
    
    // Set Action URL
    editForm.action = `/admin/banners/${banner.id}`;
    
    // Fill Values
    document.getElementById('edit-title').value = banner.title || '';
    document.getElementById('edit-content').value = banner.content || '';
    document.getElementById('edit-order').value = banner.order;
    document.getElementById('edit-start-date').value = banner.start_date || '';
    document.getElementById('edit-end-date').value = banner.end_date || '';
    
    // Show Modal
    editModal.classList.remove('display-none');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('display-none');
}

// Close modals when clicking outside modal card
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('display-none');
        }
    });
});


let deleteTarget = null;

function showDeletePopup(iconElement) {
    deleteTarget = iconElement.closest('.data-item');
    document.getElementById('delete-popup').style.display = 'block';
}

function closeDeletePopup() {
    document.getElementById('delete-popup').style.display = 'none';
    deleteTarget = null;
}

function confirmDelete() {
    if (deleteTarget) {
        deleteTarget.remove();
    }
    closeDeletePopup();
}

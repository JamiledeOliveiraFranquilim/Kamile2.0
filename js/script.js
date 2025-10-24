function openModal() {
    document.getElementById('authModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('authModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('authModal');
    if (event.target === modal) closeModal();
};

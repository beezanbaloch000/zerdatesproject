const flash = document.querySelector('.flash');
if (flash) setTimeout(() => flash.classList.add('fade'), 4200);
document.querySelectorAll('[data-confirm-delete]').forEach((form) => form.addEventListener('submit', (event) => { if (!window.confirm(form.dataset.confirmDelete)) event.preventDefault(); }));
document.querySelectorAll('.needs-validation').forEach((form) => form.addEventListener('submit', (event) => { if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); } form.classList.add('was-validated'); }));

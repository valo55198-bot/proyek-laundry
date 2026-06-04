// Modal functionality
const modal = document.getElementById('editModal');
const btnEdit = document.getElementById('btnEdit');
const btnClose = document.getElementById('modalClose');
const btnCancel = document.getElementById('modalCancel');

function openModal() { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeModal() { modal.classList.remove('active'); document.body.style.overflow = ''; }

btnEdit.addEventListener('click', openModal);
btnClose.addEventListener('click', closeModal);
btnCancel.addEventListener('click', closeModal);
modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

// File name display
document.getElementById('edit_foto').addEventListener('change', function() {
  const name = this.files[0]?.name || '';
  if (name) {
    document.getElementById('uploadText').innerHTML = '<strong>' + name + '</strong><br>Siap diupload';
  }
});

// Order count already rendered server-side in PHP; no JS fetch needed.

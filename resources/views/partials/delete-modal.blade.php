{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-danger">
                <h5 class="modal-title text-white" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Apakah Anda yakin?</h5>
                    <p class="text-muted mb-0" id="deleteMessage">
                        Data yang dihapus tidak dapat dikembalikan!
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteForm = null;

    /**
     * Menampilkan modal konfirmasi hapus
     * @param {HTMLFormElement} form - Form yang akan disubmit
     * @param {string} message - Pesan konfirmasi custom (opsional)
     */
    function showDeleteModal(form, message = 'Data yang dihapus tidak dapat dikembalikan!') {
        deleteForm = form;
        document.getElementById('deleteMessage').textContent = message;

        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Event listener untuk tombol konfirmasi
    document.addEventListener('DOMContentLoaded', function () {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                if (deleteForm) {
                    deleteForm.submit();
                }
            });
        }
    });
</script>
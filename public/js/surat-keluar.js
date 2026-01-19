/**
 * Surat Keluar Main Module
 * Initializes all modules for surat keluar create/edit pages
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Disposisi Manager
    if (document.getElementById('search-tujuan')) {
        const disposisiManager = new DisposisiManager();
        window.disposisiManager = disposisiManager; // Make it globally accessible
    }

    // Initialize Perusahaan Autocomplete
    if (document.getElementById('perusahaan_search')) {
        const perusahaanAutocomplete = new PerusahaanAutocomplete();
        window.perusahaanAutocomplete = perusahaanAutocomplete;
    }

    // Initialize File Upload Handler
    if (document.getElementById('file-input')) {
        const fileUploadHandler = new FileUploadHandler();
        window.fileUploadHandler = fileUploadHandler;
    }

    // Role-based UI adjustments
    const userRole = window.userRole || null;
    handleRoleBasedUI(userRole);

    // Toggle handlers for sekretaris
    setupToggleHandlers();

    // Generate nomor surat handlers
    setupGenerateNomorHandlers();

    // Success message handler
    handleSuccessMessage();
});

/**
 * Handle role-based UI adjustments
 */
function handleRoleBasedUI(userRole) {
    const jenisSuratContainer = document.querySelector('select[name="jenis_surat"]')?.closest('.space-y-2');
    const jenisSuratInput = document.querySelector('select[name="jenis_surat"]');
    const tujuanDisposisiSection = document.getElementById('tujuan-disposisi-section');

    // For Staff (0), Admin (3), Manager (4)
    if (userRole === 0 || userRole === 3 || userRole === 4) {
        // Hide jenis surat selector
        if (jenisSuratContainer) {
            jenisSuratContainer.style.display = 'none';
        }

        // Set default to internal
        if (jenisSuratInput) {
            jenisSuratInput.value = 'internal';
        }

        // Auto-select direktur for disposisi
        autoSelectDirektur();
    }
}

/**
 * Auto-select direktur for disposisi
 */
function autoSelectDirektur() {
    const checkboxes = document.querySelectorAll('.tujuan-checkbox');
    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.user-selection-item')?.querySelector('label');
        if (label && label.textContent.toLowerCase().includes('direktur')) {
            checkbox.checked = true;
        }
    });

    // Update count and badges if disposisi manager exists
    if (window.disposisiManager) {
        window.disposisiManager.updateCount();
        window.disposisiManager.updateBadges();
    }
}

/**
 * Setup toggle handlers for sekretaris roles
 */
function setupToggleHandlers() {
    const asDirutToggle = document.getElementById('toggle-as-dirut');
    const asDirutAspToggle = document.getElementById('toggle-as-dirut-asp');
    const asManagerKeuanganToggle = document.getElementById('toggle-as-manager-keuangan');

    if (asDirutToggle) {
        asDirutToggle.addEventListener('change', function () {
            handleToggleChange(this, 'dirut');
        });
    }

    if (asDirutAspToggle) {
        asDirutAspToggle.addEventListener('change', function () {
            handleToggleChange(this, 'dirut-asp');
        });
    }

    if (asManagerKeuanganToggle) {
        asManagerKeuanganToggle.addEventListener('change', function () {
            handleToggleChange(this, 'manager-keuangan');
        });
    }
}

/**
 * Handle toggle change events
 */
function handleToggleChange(toggle, type) {
    const isChecked = toggle.checked;
    const dotElement = toggle.closest('label')?.querySelector('.dot');

    if (dotElement) {
        dotElement.classList.toggle('translate-x-5', isChecked);
    }

    // Update button visibility based on toggle
    updateGenerateNomorButtons(type, isChecked);

    // Visual feedback
    const toggleContainer = toggle.closest('div.bg-indigo-50, div.bg-blue-50');
    if (toggleContainer) {
        if (isChecked) {
            toggleContainer.classList.add('ring-2', 'ring-green-500');
        } else {
            toggleContainer.classList.remove('ring-2', 'ring-green-500');
        }
    }
}

/**
 * Update generate nomor button visibility
 */
function updateGenerateNomorButtons(type, isActive) {
    const generateNomorBtn = document.getElementById('generateNomorBtn');
    const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
    const generateNomorManagerKeuanganBtn = document.getElementById('generateNomorManagerKeuanganBtn');
    const generateNomorDirutAspBtn = document.getElementById('generateNomorDirutAspBtn');

    if (type === 'manager-keuangan') {
        if (generateNomorManagerKeuanganBtn) {
            generateNomorManagerKeuanganBtn.style.display = isActive ? 'inline-flex' : 'none';
        }
        if (generateNomorAspBtn) {
            generateNomorAspBtn.style.display = isActive ? 'none' : 'inline-flex';
        }
    } else if (type === 'dirut-asp') {
        if (generateNomorDirutAspBtn) {
            generateNomorDirutAspBtn.style.display = isActive ? 'inline-flex' : 'none';
        }
        if (generateNomorAspBtn) {
            generateNomorAspBtn.style.display = isActive ? 'none' : 'inline-flex';
        }
    }
}

/**
 * Setup generate nomor handlers
 */
function setupGenerateNomorHandlers() {
    const buttons = {
        'generateNomorBtn': generateNomorSurat,
        'generateNomorAspBtn': generateNomorAsp,
        'generateNomorManagerKeuanganBtn': generateNomorManagerKeuangan,
        'generateNomorDirutAspBtn': generateNomorDirutAsp,
        'generateNomorAzraBtn': generateNomorAzra
    };

    Object.entries(buttons).forEach(([id, handler]) => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', handler);
        }
    });
}

/**
 * Generate nomor surat functions
 */
async function generateNomorSurat() {
    await generateNomor('/suratkeluar/get-last-number', { type: 'internal' });
}

async function generateNomorAsp() {
    await generateNomor('/suratkeluar/get-last-number', { type: 'asp' });
}

async function generateNomorManagerKeuangan() {
    await generateNomor('/suratkeluar/get-last-number', { type: 'manager-keuangan' });
}

async function generateNomorDirutAsp() {
    await generateNomor('/suratkeluar/get-last-number', { type: 'dirut-asp' });
}

async function generateNomorAzra() {
    await generateNomor('/suratkeluar/get-last-number', { type: 'azra' });
}

/**
 * Generic generate nomor function
 */
async function generateNomor(url, params = {}) {
    const nomorSuratInput = document.querySelector('input[name="nomor_surat"]');
    if (!nomorSuratInput) return;

    // Show loading state
    const originalValue = nomorSuratInput.value;
    nomorSuratInput.value = 'Generating...';
    nomorSuratInput.disabled = true;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(params)
        });

        const data = await response.json();

        if (data.success) {
            nomorSuratInput.value = data.nomor_surat;
            showSuccessToast('Nomor surat berhasil digenerate');
        } else {
            throw new Error(data.message || 'Gagal generate nomor surat');
        }
    } catch (error) {
        console.error('Error generating nomor:', error);
        nomorSuratInput.value = originalValue;
        showErrorToast(error.message || 'Gagal generate nomor surat');
    } finally {
        nomorSuratInput.disabled = false;
    }
}

/**
 * Handle success message from session
 */
function handleSuccessMessage() {
    const successMessage = document.querySelector('[data-success-message]');
    if (successMessage) {
        const message = successMessage.dataset.successMessage;
        if (message && typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#10B981'
            });
        }
    }
}

/**
 * Utility functions for notifications
 */
function showSuccessToast(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}

function showErrorToast(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}

/**
 * Delete file function (for edit page)
 */
window.deleteFile = async function (suratId, fileId) {
    if (typeof Swal === 'undefined') {
        if (!confirm('Hapus file ini?')) return;
    } else {
        const result = await Swal.fire({
            title: 'Hapus File?',
            text: "File yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) return;
    }

    try {
        const response = await fetch(`/suratkeluar/${suratId}/file/${fileId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showSuccessToast(data.message || 'File berhasil dihapus');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Gagal menghapus file');
        }
    } catch (error) {
        console.error('Error deleting file:', error);
        showErrorToast(error.message || 'Terjadi kesalahan saat menghapus file');
    }
};

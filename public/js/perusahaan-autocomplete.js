/**
 * Perusahaan Autocomplete Module
 * Handles company autocomplete and quick add functionality
 */

class PerusahaanAutocomplete {
    constructor(options = {}) {
        this.searchInput = options.searchInput || document.getElementById('perusahaan_search');
        this.hiddenInput = options.hiddenInput || document.getElementById('perusahaan_id');
        this.suggestionsContainer = options.suggestionsContainer || document.getElementById('perusahaan-suggestions');
        this.container = options.container || document.getElementById('perusahaan-container');
        this.jenisSuratSelect = options.jenisSuratSelect || document.querySelector('select[name="jenis_surat"]');

        this.searchTimeout = null;
        this.minSearchLength = 2;
        this.searchDelay = 300; // ms

        this.init();
    }

    init() {
        if (!this.searchInput || !this.suggestionsContainer) {
            console.warn('PerusahaanAutocomplete: Required elements not found');
            return;
        }

        this.setupEventListeners();
        this.setupJenisSuratToggle();
    }

    setupEventListeners() {
        // Search input
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));

        // Click on suggestions
        this.suggestionsContainer.addEventListener('click', (e) => this.handleSuggestionClick(e));

        // Click outside to hide suggestions
        document.addEventListener('click', (e) => {
            if (!this.searchInput.contains(e.target) && !this.suggestionsContainer.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }

    setupJenisSuratToggle() {
        if (!this.jenisSuratSelect || !this.container) return;

        const toggleVisibility = () => {
            const isEksternal = this.jenisSuratSelect.value === 'eksternal';
            this.container.style.display = isEksternal ? 'block' : 'none';

            if (!isEksternal) {
                // Auto-set perusahaan for internal letters
                const userRole = window.userRole || null;
                if (userRole === 5 || userRole === 8) {
                    this.hiddenInput.value = 'ASP';
                } else {
                    this.hiddenInput.value = 'RSAZRA';
                }
                this.searchInput.value = '';
            }
        };

        this.jenisSuratSelect.addEventListener('change', toggleVisibility);
        toggleVisibility(); // Initial state
    }

    handleSearch(query) {
        clearTimeout(this.searchTimeout);

        const searchTerm = query.trim();

        if (searchTerm.length < this.minSearchLength) {
            this.hideSuggestions();
            return;
        }

        this.searchTimeout = setTimeout(() => {
            this.fetchSuggestions(searchTerm);
        }, this.searchDelay);
    }

    async fetchSuggestions(query) {
        try {
            const response = await fetch(`/api/perusahaan/search?query=${encodeURIComponent(query)}`);
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.displaySuggestions(data.data);
            } else {
                this.displayAddNewOption(query);
            }
        } catch (error) {
            console.error('Error fetching perusahaan suggestions:', error);
            this.displayAddNewOption(query);
        }
    }

    displaySuggestions(companies) {
        const html = companies.map(company => `
            <div class="suggestion-item p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150" 
                 data-kode="${this.escapeHtml(company.kode)}"
                 data-nama="${this.escapeHtml(company.nama_perusahaan)}">
                <div class="font-medium text-gray-900">${this.escapeHtml(company.nama_perusahaan)}</div>
                <div class="text-xs text-gray-500">${this.escapeHtml(company.kode)}</div>
            </div>
        `).join('');

        this.suggestionsContainer.innerHTML = html;
        this.showSuggestions();
    }

    displayAddNewOption(query) {
        this.suggestionsContainer.innerHTML = `
            <div class="suggestion-item p-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 transition-colors duration-150" 
                 id="add-new-company"
                 data-nama="${this.escapeHtml(query)}">
                <div class="flex items-center text-green-600">
                    <i class="ri-add-circle-line mr-2"></i>
                    <span class="font-medium">Tambah "${this.escapeHtml(query)}" sebagai perusahaan baru</span>
                </div>
            </div>
        `;
        this.showSuggestions();
    }

    handleSuggestionClick(e) {
        const item = e.target.closest('.suggestion-item');
        if (!item) return;

        if (item.id === 'add-new-company') {
            this.addNewCompany(item.dataset.nama);
        } else {
            this.selectCompany(item.dataset.kode, item.dataset.nama);
        }
    }

    selectCompany(kode, nama) {
        this.searchInput.value = nama;
        this.hiddenInput.value = kode;
        this.hideSuggestions();
    }

    async addNewCompany(nama) {
        try {
            const response = await fetch('/api/perusahaan/quick-store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ nama_perusahaan: nama })
            });

            const data = await response.json();

            if (data.success) {
                this.selectCompany(data.data.kode, data.data.nama_perusahaan);
                this.showSuccessMessage('Perusahaan baru berhasil ditambahkan');
            } else {
                throw new Error(data.message || 'Gagal menambahkan perusahaan');
            }
        } catch (error) {
            console.error('Error adding new company:', error);
            this.showErrorMessage(error.message || 'Gagal menambahkan perusahaan baru');
        }
    }

    showSuggestions() {
        this.suggestionsContainer.style.display = 'block';
    }

    hideSuggestions() {
        this.suggestionsContainer.style.display = 'none';
    }

    showSuccessMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            alert(message);
        }
    }

    showErrorMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message
            });
        } else {
            alert(message);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerusahaanAutocomplete;
}

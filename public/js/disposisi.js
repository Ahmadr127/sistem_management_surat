/**
 * Disposisi Management Module
 * Handles disposisi (distribution) functionality for surat keluar
 */

class DisposisiManager {
    constructor(options = {}) {
        this.searchInput = options.searchInput || document.getElementById('search-tujuan');
        this.listContainer = options.listContainer || document.getElementById('tujuan-disposisi-list');
        this.checkboxes = options.checkboxes || document.querySelectorAll('.tujuan-checkbox');
        this.countDisplay = options.countDisplay || document.getElementById('selected-tujuan-count');
        this.selectAllBtn = options.selectAllBtn || document.getElementById('select-all-btn');
        this.clearAllBtn = options.clearAllBtn || document.getElementById('clear-all-btn');
        this.selectionContainer = options.selectionContainer || document.getElementById('tujuan-selection-container');
        this.badgeContainer = options.badgeContainer || document.getElementById('selected-users-badge');

        this.init();
    }

    init() {
        if (!this.searchInput || !this.listContainer) {
            console.warn('Disposisi: Required elements not found');
            return;
        }

        this.setupEventListeners();
        this.updateCount();
        this.updateBadges();
    }

    setupEventListeners() {
        // Search functionality
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
            this.searchInput.addEventListener('focus', () => this.showSelectionContainer());
        }

        // Checkbox change events
        this.checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateCount();
                this.updateBadges();
            });
        });

        // Select all button
        if (this.selectAllBtn) {
            this.selectAllBtn.addEventListener('click', () => this.selectAll());
        }

        // Clear all button
        if (this.clearAllBtn) {
            this.clearAllBtn.addEventListener('click', () => this.clearAll());
        }

        // Click outside to hide
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#tujuan-selection-container') &&
                !e.target.closest('#search-tujuan') &&
                !e.target.closest('#selected-users-badge')) {
                this.hideSelectionContainer();
            }
        });
    }

    handleSearch(query) {
        const searchTerm = query.toLowerCase().trim();
        const items = this.listContainer.querySelectorAll('.user-selection-item');
        let visibleCount = 0;

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            item.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Show/hide no results message
        const noResultsMsg = document.getElementById('no-user-found');
        if (noResultsMsg) {
            noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        this.showSelectionContainer();
    }

    showSelectionContainer() {
        if (this.selectionContainer) {
            this.selectionContainer.style.display = 'block';
        }
    }

    hideSelectionContainer() {
        if (this.selectionContainer) {
            this.selectionContainer.style.display = 'none';
        }
    }

    updateCount() {
        if (!this.countDisplay) return;

        const checkedCount = Array.from(this.checkboxes).filter(cb => cb.checked).length;
        this.countDisplay.textContent = `${checkedCount} dipilih`;
    }

    updateBadges() {
        if (!this.badgeContainer) return;

        this.badgeContainer.innerHTML = '';
        const checkedBoxes = Array.from(this.checkboxes).filter(cb => cb.checked);

        checkedBoxes.forEach(checkbox => {
            const label = checkbox.closest('.user-selection-item')?.querySelector('label');
            if (!label) return;

            const userName = label.textContent.trim();
            const badge = this.createBadge(userName, checkbox.value);
            this.badgeContainer.appendChild(badge);
        });
    }

    createBadge(name, userId) {
        const badge = document.createElement('div');
        badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800';
        badge.innerHTML = `
            <span class="mr-2">${this.truncateName(name)}</span>
            <button type="button" class="hover:text-green-900 focus:outline-none" data-user-id="${userId}">
                <i class="ri-close-line"></i>
            </button>
        `;

        // Add remove functionality
        badge.querySelector('button').addEventListener('click', () => {
            const checkbox = document.querySelector(`input[value="${userId}"].tujuan-checkbox`);
            if (checkbox) {
                checkbox.checked = false;
                this.updateCount();
                this.updateBadges();
            }
        });

        return badge;
    }

    truncateName(name, maxLength = 30) {
        if (name.length <= maxLength) return name;
        return name.substring(0, maxLength) + '...';
    }

    selectAll() {
        const visibleCheckboxes = Array.from(this.checkboxes).filter(cb => {
            const item = cb.closest('.user-selection-item');
            return item && item.style.display !== 'none';
        });

        visibleCheckboxes.forEach(cb => cb.checked = true);
        this.updateCount();
        this.updateBadges();
    }

    clearAll() {
        this.checkboxes.forEach(cb => cb.checked = false);
        this.updateCount();
        this.updateBadges();
    }

    getSelectedUsers() {
        return Array.from(this.checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DisposisiManager;
}

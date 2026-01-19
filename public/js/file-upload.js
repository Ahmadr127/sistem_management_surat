/**
 * File Upload Handler Module
 * Handles multiple file uploads with preview and validation
 */

class FileUploadHandler {
    constructor(options = {}) {
        this.fileInput = options.fileInput || document.getElementById('file-input');
        this.uploadText = options.uploadText || document.getElementById('upload-text');
        this.fileSelected = options.fileSelected || document.getElementById('file-selected');
        this.selectedFilesList = options.selectedFilesList || document.getElementById('selected-files-list');
        this.removeAllBtn = options.removeAllBtn || document.getElementById('remove-all-files');
        this.dropZone = options.dropZone || document.querySelector('label[for="file-input"]');

        this.maxFileSize = options.maxFileSize || 2 * 1024 * 1024; // 2MB default
        this.allowedExtensions = options.allowedExtensions || ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        this.init();
    }

    init() {
        if (!this.fileInput) {
            console.warn('FileUploadHandler: File input not found');
            return;
        }

        this.setupEventListeners();
    }

    setupEventListeners() {
        // File input change
        this.fileInput.addEventListener('change', (e) => this.handleFileSelect(e));

        // Remove all files button
        if (this.removeAllBtn) {
            this.removeAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.resetFiles();
            });
        }

        // Drag and drop
        if (this.dropZone) {
            this.dropZone.addEventListener('dragover', (e) => this.handleDragOver(e));
            this.dropZone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
            this.dropZone.addEventListener('drop', (e) => this.handleDrop(e));
        }
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);

        if (files.length === 0) {
            this.resetFiles();
            return;
        }

        // Validate files
        const validFiles = files.filter(file => this.validateFile(file));

        if (validFiles.length === 0) {
            this.resetFiles();
            return;
        }

        this.displayFiles(validFiles);
    }

    validateFile(file) {
        // Check file size
        if (file.size > this.maxFileSize) {
            this.showError(`File ${file.name} terlalu besar. Maksimal ${this.formatFileSize(this.maxFileSize)}`);
            return false;
        }

        // Check file extension
        const extension = file.name.split('.').pop().toLowerCase();
        if (!this.allowedExtensions.includes(extension)) {
            this.showError(`File ${file.name} memiliki ekstensi yang tidak diizinkan`);
            return false;
        }

        return true;
    }

    displayFiles(files) {
        this.selectedFilesList.innerHTML = '';

        files.forEach((file, index) => {
            const fileItem = this.createFileItem(file, index);
            this.selectedFilesList.appendChild(fileItem);
        });

        this.uploadText.classList.add('hidden');
        this.fileSelected.classList.remove('hidden');
        this.fileSelected.classList.add('flex');
    }

    createFileItem(file, index) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200';

        const icon = this.getFileIcon(file.name);

        div.innerHTML = `
            <div class="flex items-center flex-1 min-w-0">
                <i class="${icon} text-2xl text-gray-500 mr-3 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate">${this.escapeHtml(file.name)}</p>
                    <p class="text-xs text-gray-500">${this.formatFileSize(file.size)}</p>
                </div>
            </div>
            <button type="button" 
                    class="ml-2 text-red-500 hover:text-red-700 focus:outline-none flex-shrink-0"
                    data-index="${index}">
                <i class="ri-close-circle-line text-xl"></i>
            </button>
        `;

        // Add remove individual file functionality
        div.querySelector('button').addEventListener('click', () => {
            this.removeFile(index);
        });

        return div;
    }

    removeFile(index) {
        const dt = new DataTransfer();
        const files = Array.from(this.fileInput.files);

        files.forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });

        this.fileInput.files = dt.files;

        if (dt.files.length === 0) {
            this.resetFiles();
        } else {
            this.displayFiles(Array.from(dt.files));
        }
    }

    resetFiles() {
        this.fileInput.value = '';
        this.selectedFilesList.innerHTML = '';
        this.uploadText.classList.remove('hidden');
        this.fileSelected.classList.add('hidden');
        this.fileSelected.classList.remove('flex');
    }

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dropZone.classList.add('bg-gray-100');
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dropZone.classList.remove('bg-gray-100');
    }

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dropZone.classList.remove('bg-gray-100');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            this.fileInput.files = files;
            const event = new Event('change');
            this.fileInput.dispatchEvent(event);
        }
    }

    getFileIcon(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'ri-file-pdf-line',
            'doc': 'ri-file-word-line',
            'docx': 'ri-file-word-line',
            'jpg': 'ri-image-line',
            'jpeg': 'ri-image-line',
            'png': 'ri-image-line',
        };
        return iconMap[extension] || 'ri-file-text-line';
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#EF4444'
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
    module.exports = FileUploadHandler;
}

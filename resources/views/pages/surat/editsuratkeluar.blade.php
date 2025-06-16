@extends('home')

@section('title', 'Edit Surat Keluar - SISM Azra')

@section('content')
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Edit Surat Keluar</h2>
                <p class="text-xs text-gray-500 mt-1">Perbarui data surat keluar</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('suratkeluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data"
            class="p-8">
            @csrf
            @method('PUT')
            <!-- Card untuk Informasi Surat -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-800">
                        <i class="ri-mail-line mr-2 text-gray-600"></i>
                        Informasi Surat
                    </h3>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Grid Layout untuk Nomor dan Tanggal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Nomor Surat</label>
                            <div class="relative">
                                <input type="text" name="nomor_surat"
                                    value="{{ old('nomor_surat', $surat->nomor_surat) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                                    placeholder="Masukkan nomor surat">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="ri-information-line mr-1"></i>
                                Gunakan tanda strip (-) jika ingin menggunakan nomor surat yang sama dengan surat lain.
                            </p>
                            @error('nomor_surat')
                                <p class="text-red-500 text-xs">
                                    <i class="ri-error-warning-line mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Tanggal Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Tanggal Surat</label>
                            <div class="relative">
                                <input type="date" name="tanggal_surat" required
                                    value="{{ old('tanggal_surat', $surat->tanggal_surat ? date('Y-m-d', strtotime($surat->tanggal_surat)) : date('Y-m-d')) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="ri-calendar-line"></i>
                                </div>
                            </div>
                            @error('tanggal_surat')
                                <p class="text-red-500 text-xs">
                                    <i class="ri-error-warning-line mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Jenis dan Sifat Surat -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jenis Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Jenis Surat</label>
                            <div class="relative">
                                <select name="jenis_surat" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 appearance-none bg-white">
                                    <option value="internal"
                                        {{ old('jenis_surat', $surat->jenis_surat) == 'internal' ? 'selected' : '' }}>
                                        Internal
                                    </option>
                                    <option value="eksternal"
                                        {{ old('jenis_surat', $surat->jenis_surat) == 'eksternal' ? 'selected' : '' }}>
                                        Eksternal
                                    </option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Sifat Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Sifat Surat</label>
                            <div class="relative">
                                <select name="sifat_surat" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 appearance-none bg-white">
                                    <option value="normal"
                                        {{ old('sifat_surat', $surat->sifat_surat) == 'normal' ? 'selected' : '' }}>
                                        Normal
                                    </option>
                                    <option value="urgent"
                                        {{ old('sifat_surat', $surat->sifat_surat) == 'urgent' ? 'selected' : '' }}>
                                        Urgent
                                    </option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Perusahaan -->
                    <div class="space-y-2 perusahaan-container" id="perusahaan-container">
                        <label class="text-sm font-semibold text-gray-800">Perusahaan</label>
                        <div class="suggestions-wrapper relative">
                            <input type="text" 
                                id="perusahaan_search" 
                                name="perusahaan_search"
                                value="{{ $surat->perusahaanData->nama_perusahaan ?? '' }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                                placeholder="Cari atau tambah perusahaan baru..."
                                autocomplete="off">
                            <input type="hidden" name="perusahaan" id="perusahaan_id" value="{{ $surat->perusahaan }}">
                            <div id="perusahaan-suggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-md shadow-lg z-50 max-h-60 overflow-y-auto mt-1" style="display:none;">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Perihal -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800">Perihal</label>
                        <textarea name="perihal" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                            rows="3" placeholder="Masukkan perihal surat">{{ old('perihal', $surat->perihal) }}</textarea>
                        @error('perihal')
                            <p class="text-red-500 text-xs">
                                <i class="ri-error-warning-line mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Card untuk Upload File -->
            <div class="mt-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-800">
                        <i class="ri-file-upload-line mr-2 text-gray-600"></i>
                        File Surat
                    </h3>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Current File Info -->
                        @if ($surat->file_path)
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <i class="ri-file-text-line text-2xl text-gray-500 mr-3"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700">File Saat Ini</p>
                                        <p class="text-xs text-gray-500">{{ basename($surat->file_path) }}</p>
                                    </div>
                                    <a href="{{ route('suratkeluar.download', $surat->id) }}"
                                        class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors duration-200">
                                        <i class="ri-download-line mr-1"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Daftar file tambahan -->
                        @if ($surat->files && $surat->files->count() > 0)
                            <div class="space-y-3">
                                <h4 class="text-sm font-medium text-gray-700">File Terlampir</h4>
                                    @foreach ($surat->files as $file)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center">
                                            <i class="ri-file-text-line text-2xl text-gray-500 mr-3"></i>
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">{{ $file->original_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $file->file_type }}</p>
                                            </div>
                                                </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('suratkeluar.download', $surat->id) }}?file_id={{ $file->id }}"
                                                    class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors duration-200">
                                                    <i class="ri-download-line mr-1"></i>
                                                    Download
                                                </a>
                                            <button type="button" 
                                                onclick="deleteFile({{ $surat->id }}, {{ $file->id }})"
                                                class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                                <i class="ri-delete-bin-line mr-1"></i>
                                                Hapus
                                            </button>
                                            </div>
                                        </div>
                                    @endforeach
                            </div>
                        @endif

                        <!-- File Upload Area -->
                        <div class="w-full">
                            <input type="file" name="file[]" id="file-input" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                            <label for="file-input" class="w-full">
                                <div class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-200 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200">
                                    <!-- Upload Text -->
                                    <div id="upload-text" class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="ri-upload-cloud-line text-3xl text-gray-400 mb-3"></i>
                                        <p class="text-sm text-gray-600 font-medium">Klik untuk upload atau drag and drop file</p>
                                        <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 2MB per file, bisa lebih dari satu file)</p>
                                    </div>
                                    <!-- Selected File Info (Multiple Preview) -->
                                    <div id="file-selected" class="hidden flex-col items-center justify-center pt-5 pb-6 w-full">
                                        <div id="selected-files-list" class="w-full space-y-2"></div>
                                        <button type="button" id="remove-all-files"
                                                class="mt-3 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                                <i class="ri-delete-bin-line mr-1"></i>
                                            Hapus semua file
                                            </button>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">
                            <i class="ri-information-line mr-1"></i>
                            Biarkan kosong jika tidak ingin mengubah file. Ukuran file tidak dibatasi, pastikan sesuai kebutuhan.
                        </p>
                        @error('file')
                            <p class="text-red-500 text-xs">
                                <i class="ri-error-warning-line mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Card untuk Disposisi -->
            <div class="mt-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-800">
                        <i class="ri-share-forward-line mr-2 text-gray-600"></i>
                        Disposisi Surat
                    </h3>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Tujuan Disposisi (Multiple Select) -->
                    <div class="space-y-2" id="tujuan-disposisi-section">
                        <label class="text-sm font-semibold text-gray-800">Tujuan Disposisi</label>
                        <input type="text" id="search-tujuan" class="w-full px-3 py-2 rounded-lg border border-gray-200 mb-2" placeholder="Cari nama atau jabatan...">
                        <div id="tujuan-disposisi-list" class="space-y-2 max-h-60 overflow-y-auto border border-gray-100 rounded p-2 bg-gray-50">
                            @foreach ($users as $user)
                                <div class="flex items-center tujuan-item">
                                    <input type="checkbox" name="tujuan_disposisi[]" value="{{ $user->id }}" id="user-{{ $user->id }}"
                                        {{ in_array($user->id, old('tujuan_disposisi', $selectedUsers ?? [])) ? 'checked' : '' }}
                                        class="mr-2 tujuan-checkbox">
                                    <label for="user-{{ $user->id }}" class="flex-1 cursor-pointer">
                                        <span class="font-medium">{{ $user->name }}</span>
                                        <span class="text-xs text-gray-500 ml-1">({{ $user->jabatan->nama_jabatan ?? 'Tidak ada jabatan' }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-xs text-gray-500 mt-1" id="selected-tujuan-count"></div>
                    </div>

                    <!-- Keterangan Pengirim -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800">Keterangan Pengirim</label>
                        <textarea name="keterangan_pengirim"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                            rows="3" placeholder="Tambahkan keterangan untuk penerima disposisi">{{ old('keterangan_pengirim', $surat->disposisi->keterangan_pengirim ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('suratkeluar.index') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="ri-arrow-left-line mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="ri-save-line mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get DOM elements
        const fileInput = document.getElementById('file-input');
        const uploadText = document.getElementById('upload-text');
        const fileSelected = document.getElementById('file-selected');
        const selectedFilename = document.getElementById('selected-filename');
        const selectedFilesize = document.getElementById('selected-filesize');
        const removeFileBtn = document.getElementById('remove-all-files');

        // Cek role pengguna
        const userRole = {{ auth()->user()->role ?? 'null' }};

        // Sembunyikan dan set default jenis surat untuk role 0 dan 3
        if (userRole === 0 || userRole === 3) {
            // Sembunyikan input jenis surat
            const jenisSuratContainer = document.querySelector('select[name="jenis_surat"]').closest(
                '.space-y-2');
            if (jenisSuratContainer) {
                jenisSuratContainer.style.display = 'none';
            }

            // Set default value ke "internal"
            const jenisSuratInput = document.querySelector('select[name="jenis_surat"]');
            if (jenisSuratInput) {
                jenisSuratInput.value = 'internal';
            }

            // Sembunyikan input tujuan disposisi
            const tujuanDisposisiContainer = document.querySelector('select[name="tujuan_disposisi[]"]')
                .closest('.space-y-2');
            if (tujuanDisposisiContainer) {
                tujuanDisposisiContainer.style.display = 'none';
            }

            // Set default tujuan disposisi ke direktur utama (role 2)
            const tujuanDisposisiSelect = document.querySelector('select[name="tujuan_disposisi[]"]');
            if (tujuanDisposisiSelect) {
                // Cari option dengan jabatan direktur
                let dirOption = Array.from(tujuanDisposisiSelect.options).find(option => {
                    return option.textContent.toLowerCase().includes('direktur');
                });

                if (dirOption) {
                    // Pilih direktur
                    dirOption.selected = true;
                }
            }

            // Tambahkan informasi
            const disposisiSection = document.querySelector('div.p-6.space-y-6');
            if (disposisiSection) {
                const notifHtml = `
                    <div class="bg-blue-50 text-blue-800 p-3 rounded-md mt-2">
                        <i class="ri-information-line mr-1"></i>
                        <span>Tujuan disposisi akan otomatis dikirim ke Direktur Utama</span>
                    </div>
                `;
                disposisiSection.insertAdjacentHTML('beforeend', notifHtml);
            }
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Show file preview
        function showFilePreview(file) {
            selectedFilename.textContent = file.name;
            selectedFilesize.textContent = formatFileSize(file.size);
            uploadText.classList.add('hidden');
            fileSelected.classList.remove('hidden');
        }

        // Reset file input
        function resetFileInput() {
            fileInput.value = '';
            const selectedFilesList = document.getElementById('selected-files-list');
            selectedFilesList.innerHTML = '';
            uploadText.classList.remove('hidden');
            fileSelected.classList.add('hidden');
        }

        // File input change event
        fileInput.addEventListener('change', function(e) {
            const files = this.files;
            if (files.length > 0) {
                // Show selected files list
                const selectedFilesList = document.getElementById('selected-files-list');
                selectedFilesList.innerHTML = '';
                
                Array.from(files).forEach(file => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-white rounded border border-gray-200';
                    fileItem.innerHTML = `
                        <div class="flex items-center">
                            <i class="ri-file-text-line text-xl text-gray-500 mr-2"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-700">${file.name}</p>
                                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            </div>
                        </div>
                    `;
                    selectedFilesList.appendChild(fileItem);
                });

                uploadText.classList.add('hidden');
                fileSelected.classList.remove('hidden');
            } else {
                resetFileInput();
            }
        });

        // Remove file button click event
        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                resetFileInput();
            });
        }

        // Drag and drop functionality
        const dropZone = document.querySelector('label[for="file-input"]');

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('bg-gray-100');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('bg-gray-100');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('bg-gray-100');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });

        // Perusahaan Autocomplete
        const jenisSuratSelect = document.querySelector('select[name="jenis_surat"]');
        const perusahaanContainer = document.getElementById('perusahaan-container');
        const perusahaanHidden = document.getElementById('perusahaan_hidden');
        const perusahaanId = document.getElementById('perusahaan_id');
        const perusahaanSearch = document.getElementById('perusahaan_search');
        const suggestionsContainer = document.getElementById('perusahaan-suggestions');
        let searchTimeout;

        // Toggle perusahaan input sesuai jenis surat
        function togglePerusahaanInput() {
            if (jenisSuratSelect.value === 'eksternal') {
                perusahaanContainer.style.display = 'block';
            } else {
                perusahaanContainer.style.display = 'none';
                if (perusahaanId) perusahaanId.value = 'RSAZRA';
                if (perusahaanSearch) perusahaanSearch.value = '';
            }
        }
        if (jenisSuratSelect && perusahaanContainer) {
            togglePerusahaanInput();
            jenisSuratSelect.addEventListener('change', togglePerusahaanInput);
        }

        // Perusahaan autocomplete (suggestion)
        if (perusahaanSearch) {
            console.log('Perusahaan search input found:', perusahaanSearch);
            
            perusahaanSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                console.log('Input event fired, query:', query);
                
                if (query.length < 2) {
                    suggestionsContainer.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    console.log('Fetching suggestions for:', query);
                    
                    fetch(`/api/perusahaan/search?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('API response:', data);
                            
                            if (data.success && data.data.length > 0) {
                                suggestionsContainer.innerHTML = data.data.map(perusahaan => `
                                    <div class="suggestion-item p-2 hover:bg-gray-100 cursor-pointer" 
                                         data-kode="${perusahaan.kode}"
                                         data-nama="${perusahaan.nama_perusahaan}">${perusahaan.nama_perusahaan}</div>
                                `).join('');
                                suggestionsContainer.style.display = 'block';
                                console.log('Showing suggestions container');
                            } else {
                                suggestionsContainer.innerHTML = `
                                    <div class="suggestion-item p-2 hover:bg-gray-100 cursor-pointer text-green-600" id="add-new-company"><i class="ri-add-line mr-2"></i>Tambah "${query}" sebagai perusahaan baru</div>
                                `;
                                suggestionsContainer.style.display = 'block';
                                console.log('Showing add new company option');
                            }
                        })
                        .catch(error => {
                            console.error('API Error:', error);
                        });
                }, 300);
            });
        }

        // Handle click pada suggestion
        if (suggestionsContainer) {
            suggestionsContainer.addEventListener('click', function(e) {
                const target = e.target.closest('.suggestion-item');
                if (!target) return;
                
                console.log('Suggestion item clicked', target);
                
                if (target.id === 'add-new-company') {
                    const newCompanyName = perusahaanSearch.value.trim();
                    if (newCompanyName) {
                        fetch('/api/perusahaan/quick-store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ nama_perusahaan: newCompanyName })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                perusahaanSearch.value = data.data.nama_perusahaan;
                                perusahaanId.value = data.data.kode;
                                suggestionsContainer.style.display = 'none';
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Perusahaan baru berhasil ditambahkan',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menambahkan perusahaan baru'
                            });
                        });
                    }
                } else {
                    const kode = target.dataset.kode;
                    const nama = target.dataset.nama;
                    perusahaanSearch.value = nama;
                    perusahaanId.value = kode;
                    suggestionsContainer.style.display = 'none';
                }
            });
        }

        // Hide suggestions saat klik di luar
        document.addEventListener('click', function(e) {
            if (suggestionsContainer && !perusahaanSearch?.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });

        // SweetAlert for success message
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#10B981'
            });
        @endif

        // Function to delete file
        window.deleteFile = function(suratId, fileId) {
            Swal.fire({
                title: 'Hapus File?',
                text: "File yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send delete request
                    fetch(`/suratkeluar/${suratId}/file/${fileId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Reload halaman untuk memperbarui tampilan
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat menghapus file'
                        });
                    });
                }
            });
        }

        // Fitur pencarian dan counter tujuan disposisi
        const searchTujuan = document.getElementById('search-tujuan');
        const tujuanList = document.getElementById('tujuan-disposisi-list');
        const tujuanItems = tujuanList ? tujuanList.querySelectorAll('.tujuan-item') : [];
        const tujuanCheckboxes = () => tujuanList ? tujuanList.querySelectorAll('.tujuan-checkbox') : [];
        const tujuanCount = document.getElementById('selected-tujuan-count');

        function updateSelectedCount() {
            if (!tujuanCount) return;
            const checked = tujuanCheckboxes();
            const total = Array.from(checked).filter(cb => cb.checked).length;
            tujuanCount.textContent = `${total} tujuan dipilih`;
        }
        if (tujuanCheckboxes()) {
            tujuanCheckboxes().forEach(cb => cb.addEventListener('change', updateSelectedCount));
            updateSelectedCount();
        }

        if (searchTujuan) {
            searchTujuan.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                tujuanItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(val) ? '' : 'none';
                });
            });
        }
    });
</script>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Custom styling untuk date input */
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0;
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    /* Custom styling untuk select/combobox */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }

    select:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2310B981' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Styling untuk suggestions container */
    #perusahaan-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        max-height: 200px;
        overflow-y: auto;
        z-index: 99999;
        margin-top: 4px;
        display: none;
    }

    .suggestions-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        max-height: 200px;
        overflow-y: auto;
        z-index: 99999;
        margin-top: 4px;
        display: none;
    }

    .suggestions-wrapper {
        position: relative !important;
        z-index: 50 !important;
    }

    /* Override untuk container form */
    form .p-6.space-y-6 {
        overflow: visible !important;
    }

    /* Override untuk card container */
    .bg-white.rounded-xl.border.border-gray-200.overflow-hidden {
        overflow: visible !important;
    }

    #perusahaan-container { display: block; }

    /* Debugging helper */
    .debug-outline {
        outline: 2px solid red !important;
    }
</style>

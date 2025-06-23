@extends('home')

@section('title', 'Tambah Surat Unit Manager - SISM Azra')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Tambah Surat Unit Manager</h2>
            <p class="text-xs text-gray-500 mt-1">Buat surat baru untuk disetujui manager</p>
        </div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
            <a href="{{ route('surat-unit-manager.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <form id="suratForm" action="{{ route('surat-unit-manager.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nomor Surat -->
            <div class="md:col-span-2">
                <label for="nomor_surat" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Surat <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nomor_surat" name="nomor_surat" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Masukkan nomor surat">
                <div id="nomor_surat_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Tanggal Surat -->
            <div>
                <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Surat <span class="text-red-500">*</span>
                </label>
                <input type="date" id="tanggal_surat" name="tanggal_surat" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="{{ date('Y-m-d') }}">
                <div id="tanggal_surat_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Jenis Surat -->
            <div>
                <label for="jenis_surat" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Surat <span class="text-red-500">*</span>
                </label>
                <select id="jenis_surat" name="jenis_surat" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="internal">Internal</option>
                    <option value="eksternal">Eksternal</option>
                </select>
                <div id="jenis_surat_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Sifat Surat -->
            <div>
                <label for="sifat_surat" class="block text-sm font-medium text-gray-700 mb-2">
                    Sifat Surat <span class="text-red-500">*</span>
                </label>
                <select id="sifat_surat" name="sifat_surat" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                </select>
                <div id="sifat_surat_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Perusahaan -->
            <div>
                <label for="perusahaan" class="block text-sm font-medium text-gray-700 mb-2">
                    Perusahaan <span class="text-red-500">*</span>
                </label>
                <select id="perusahaan" name="perusahaan" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="RSAZRA">RSAZRA</option>
                    @foreach($perusahaans as $perusahaan)
                        <option value="{{ $perusahaan->kode }}">{{ $perusahaan->nama_perusahaan }}</option>
                    @endforeach
                </select>
                <div id="perusahaan_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Perihal -->
            <div class="md:col-span-2">
                <label for="perihal" class="block text-sm font-medium text-gray-700 mb-2">
                    Perihal <span class="text-red-500">*</span>
                </label>
                <input type="text" id="perihal" name="perihal" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Masukkan perihal surat">
                <div id="perihal_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Isi Surat -->
            <div class="md:col-span-2">
                <label for="isi_surat" class="block text-sm font-medium text-gray-700 mb-2">
                    Isi Surat <span class="text-red-500">*</span>
                </label>
                <textarea id="isi_surat" name="isi_surat" rows="6" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan isi surat"></textarea>
                <div id="isi_surat_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- File Upload -->
            <div class="md:col-span-2">
                <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                    File Lampiran
                </label>
                <input type="file" id="files" name="files[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx,.ppt,.pptx,.zip,.rar"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG, XLS, XLSX, PPT, PPTX, ZIP, RAR (Max: 5MB per file)</p>
                <div id="files_error" class="text-red-500 text-sm mt-1 hidden"></div>
                
                <!-- File Preview -->
                <div id="filePreview" class="mt-3 space-y-2 hidden">
                    <h4 class="text-sm font-medium text-gray-700">File yang dipilih:</h4>
                    <div id="fileList" class="space-y-2"></div>
                </div>
            </div>

            <!-- Keterangan Unit -->
            <div class="md:col-span-2">
                <label for="keterangan_unit" class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan Unit
                </label>
                <textarea id="keterangan_unit" name="keterangan_unit" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Masukkan keterangan tambahan (opsional)"></textarea>
                <div id="keterangan_unit_error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Manager Info -->
            @if($manager)
            <div class="md:col-span-2">
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Manager yang akan menyetujui:</h4>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="ri-user-line text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-900">{{ $manager->name }}</p>
                            <p class="text-sm text-blue-700">{{ optional($manager->jabatan)->nama_jabatan }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <button type="button" onclick="window.location.href='{{ route('surat-unit-manager.index') }}'"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                Batal
            </button>
            <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                <i class="ri-save-line"></i>
                <span>Simpan Surat</span>
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('suratForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi file sebelum submit
    const fileInput = document.getElementById('files');
    if (fileInput.files.length > 0) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-rar-compressed'
        ];
        
        for (let i = 0; i < fileInput.files.length; i++) {
            const file = fileInput.files[i];
            
            if (!file || file.size === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'File tidak valid!',
                    text: `File "${file.name}" tidak valid atau kosong`
                });
                return;
            }
            
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File terlalu besar!',
                    text: `File "${file.name}" melebihi ukuran maksimal 5MB`
                });
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tipe file tidak didukung!',
                    text: `File "${file.name}" memiliki tipe yang tidak didukung`
                });
                return;
            }
        }
    }
    
    // Reset error messages
    document.querySelectorAll('[id$="_error"]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    // Disable submit button
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i><span>Menyimpan...</span>';
    
    // Create FormData
    const formData = new FormData(this);
    
    // Send request
    fetch(this.action, {
        method: 'POST',
        body: formData,
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
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect_url;
            });
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(field + '_error');
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan surat'
        });
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Auto-set perusahaan for internal letters
document.getElementById('jenis_surat').addEventListener('change', function() {
    if (this.value === 'internal') {
        document.getElementById('perusahaan').value = 'RSAZRA';
    }
});

// File size validation
document.getElementById('files').addEventListener('change', function() {
    const files = this.files;
    const maxSize = 5 * 1024 * 1024; // 5MB
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    
    // Clear previous preview
    fileList.innerHTML = '';
    
    if (files.length > 0) {
        filePreview.classList.remove('hidden');
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validasi file
            if (!file || file.size === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'File tidak valid!',
                    text: `File "${file.name}" tidak valid atau kosong`
                });
                this.value = '';
                filePreview.classList.add('hidden');
                return;
            }
            
            // Check file size
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File terlalu besar!',
                    text: `File "${file.name}" melebihi ukuran maksimal 5MB`
                });
                this.value = '';
                filePreview.classList.add('hidden');
                return;
            }
            
            // Validasi tipe file
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip',
                'application/x-rar-compressed'
            ];
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tipe file tidak didukung!',
                    text: `File "${file.name}" memiliki tipe yang tidak didukung`
                });
                this.value = '';
                filePreview.classList.add('hidden');
                return;
            }
            
            // Create file preview item
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-md';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex items-center space-x-2';
            
            // File icon based on type
            const icon = document.createElement('i');
            const extension = file.name.split('.').pop().toLowerCase();
            switch (extension) {
                case 'pdf':
                    icon.className = 'ri-file-pdf-line text-red-500';
                    break;
                case 'doc':
                case 'docx':
                    icon.className = 'ri-file-word-line text-blue-500';
                    break;
                case 'xls':
                case 'xlsx':
                    icon.className = 'ri-file-excel-line text-green-500';
                    break;
                case 'ppt':
                case 'pptx':
                    icon.className = 'ri-file-ppt-line text-orange-500';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                    icon.className = 'ri-image-line text-purple-500';
                    break;
                case 'zip':
                case 'rar':
                    icon.className = 'ri-file-zip-line text-yellow-500';
                    break;
                default:
                    icon.className = 'ri-file-line text-gray-500';
            }
            
            const fileName = document.createElement('span');
            fileName.className = 'text-sm text-gray-700';
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('span');
            fileSize.className = 'text-xs text-gray-500';
            fileSize.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            
            fileInfo.appendChild(icon);
            fileInfo.appendChild(fileName);
            fileInfo.appendChild(fileSize);
            
            fileItem.appendChild(fileInfo);
            fileList.appendChild(fileItem);
        }
    } else {
        filePreview.classList.add('hidden');
    }
});
</script>
@endpush 
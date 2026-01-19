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
        <form action="{{ route('suratkeluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')
            
            <!-- Informasi Surat Card -->
            <x-card title="Informasi Surat" icon="ri-mail-line">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Surat -->
                        <x-form-input
                            name="nomor_surat"
                            label="Nomor Surat"
                            :value="$surat->nomor_surat"
                            placeholder="Masukkan nomor surat"
                            hint="Gunakan tanda strip (-) jika ingin menggunakan nomor surat yang sama dengan surat lain."
                        />

                        <!-- Tanggal Surat -->
                        <x-form-input
                            type="date"
                            name="tanggal_surat"
                            label="Tanggal Surat"
                            :value="$surat->tanggal_surat ? date('Y-m-d', strtotime($surat->tanggal_surat)) : date('Y-m-d')"
                            icon="ri-calendar-line"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jenis Surat -->
                        <x-form-select
                            name="jenis_surat"
                            label="Jenis Surat"
                            :options="['internal' => 'Internal', 'eksternal' => 'Eksternal']"
                            :selected="$surat->jenis_surat"
                            required
                        />

                        <!-- Sifat Surat -->
                        <x-form-select
                            name="sifat_surat"
                            label="Sifat Surat"
                            :options="['normal' => 'Normal', 'urgent' => 'Urgent']"
                            :selected="$surat->sifat_surat"
                            required
                        />
                    </div>

                    <!-- Perusahaan (will be shown/hidden by JS based on jenis_surat) -->
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
                    <x-form-input
                        type="textarea"
                        name="perihal"
                        label="Perihal"
                        :value="$surat->perihal"
                        placeholder="Masukkan perihal surat"
                        rows="3"
                        required
                    />
                </div>
            </x-card>

            <!-- Upload File Card -->
            <x-card title="File Surat" icon="ri-file-upload-line" class="mt-6">
                <x-file-upload
                    name="file"
                    label=""
                    :multiple="true"
                    :existingFiles="$surat->files->map(fn($file) => [
                        'id' => $file->id,
                        'name' => $file->original_name,
                        'type' => $file->file_type
                    ])->toArray()"
                    :downloadRoute="route('suratkeluar.download', $surat->id)"
                />
                
                @if($surat->file_path)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
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
            </x-card>

            <!-- Disposisi Card -->
            <x-card title="Disposisi Surat" icon="ri-share-forward-line" class="mt-6">
                <div class="space-y-6">
                    <!-- Tujuan Disposisi -->
                    <x-searchable-dropdown
                        name="tujuan_disposisi"
                        label="Tujuan Disposisi"
                        :options="$users->map(fn($user) => [
                            'id' => $user->id,
                            'name' => $user->name . ' (' . ($user->jabatan->nama_jabatan ?? 'Tidak ada jabatan') . ')',
                            'jabatan' => $user->jabatan->nama_jabatan ?? 'Tidak ada jabatan'
                        ])->toArray()"
                        value-field="id"
                        label-field="name"
                        :selected="old('tujuan_disposisi', $selectedUsers ?? [])"
                        placeholder="Pilih tujuan disposisi"
                        :multiple="true"
                        :required="false"
                    />

                    <!-- Keterangan Pengirim -->
                    <x-form-input
                        type="textarea"
                        name="keterangan_pengirim"
                        label="Keterangan Pengirim"
                        :value="$surat->disposisi->keterangan_pengirim ?? ''"
                        placeholder="Tambahkan keterangan untuk penerima disposisi"
                        rows="3"
                    />
                </div>
            </x-card>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-4">
                <x-button
                    variant="secondary"
                    icon="ri-arrow-left-line"
                    type="button"
                    onclick="window.location.href='{{ route('suratkeluar.index') }}'"
                >
                    Batal
                </x-button>

                <x-button
                    variant="success"
                    icon="ri-save-line"
                    type="submit"
                >
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div data-success-message="{{ session('success') }}"></div>
    @endif
@endsection

@push('scripts')
<script src="{{ asset('js/perusahaan-autocomplete.js') }}"></script>
<script src="{{ asset('js/file-upload.js') }}"></script>
<script src="{{ asset('js/surat-keluar.js') }}"></script>
<script>
    // Set user role for JavaScript
    window.userRole = {{ auth()->user()->role ?? 'null' }};
    
    // Delete file function
    window.deleteFile = async function(suratId, fileId) {
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => window.location.reload());
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat menghapus file'
            });
        }
    };
</script>
@endpush

@push('styles')
<style>
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

    /* Styling untuk suggestions container */
    #perusahaan-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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

    #perusahaan-container { 
        display: block; 
    }
</style>
@endpush

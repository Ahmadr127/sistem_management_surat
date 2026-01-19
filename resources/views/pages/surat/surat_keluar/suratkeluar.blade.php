@extends('home')

@section('title', 'Surat Keluar - SISM Azra')

@section('content')
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Form Surat Keluar</h2>
                <p class="text-xs text-gray-500 mt-1">Silakan isi data surat keluar dengan lengkap</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('suratkeluar.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <!-- Tambahkan hidden input di dalam form -->
            <input type="hidden" name="perusahaan" id="perusahaan_hidden" value="">

            <!-- Hidden input untuk pengirim_id - selalu gunakan user yang login -->
            <input type="hidden" name="pengirim_id" id="pengirim_id" value="{{ auth()->id() }}">

            <!-- Toggle untuk Sekretaris sebagai Dirut -->
            @if (auth()->user()->role === 1)
                <div class="mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="ri-user-settings-line text-indigo-600 text-lg mr-2"></i>
                            <div>
                                <h4 class="text-sm font-medium text-indigo-800">Opsi Pengirim</h4>
                                <p class="text-xs text-indigo-600 mt-0.5">Anda dapat membuat surat atas nama direktur</p>
                            </div>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" id="toggle-as-dirut" name="as_dirut" class="sr-only">
                                <div class="block bg-gray-200 w-10 h-5 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition"></div>
                            </div>
                            <span class="ml-2 text-sm font-medium text-indigo-800">Kirim sebagai Direktur</span>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Toggle untuk Sekretaris ASP sebagai Direktur -->
            @if (auth()->user()->role === 5)
                <div class="mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="ri-user-settings-line text-indigo-600 text-lg mr-2"></i>
                            <div>
                                <h4 class="text-sm font-medium text-indigo-800">Opsi Pengirim</h4>
                                <p class="text-xs text-indigo-600 mt-0.5">Anda dapat membuat surat atas nama direktur</p>
                            </div>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" id="toggle-as-dirut-asp" name="as_dirut" class="sr-only">
                                <div class="block bg-gray-200 w-10 h-5 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition"></div>
                            </div>
                            <span class="ml-2 text-sm font-medium text-indigo-800">Kirim sebagai Direktur</span>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Toggle untuk Sekretaris ASP sebagai Manager Keuangan -->
            @if (auth()->user()->role === 5)
                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="ri-user-settings-line text-blue-600 text-lg mr-2"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Opsi Pengirim</h4>
                                <p class="text-xs text-blue-600 mt-0.5">Anda dapat membuat surat atas nama manager keuangan</p>
                            </div>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" id="toggle-as-manager-keuangan" name="as_manager_keuangan" class="sr-only">
                                <div class="block bg-gray-200 w-10 h-5 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-3 h-3 rounded-full transition"></div>
                            </div>
                            <span class="ml-2 text-sm font-medium text-blue-800">Kirim sebagai Manager Keuangan</span>
                        </label>
                    </div>
                </div>
            @endif

            <!-- Card untuk Informasi Surat -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-800">
                        <i class="ri-mail-line mr-2 text-gray-600"></i>
                        Informasi Surat
                    </h3>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nomor Surat -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="text-sm font-semibold text-gray-800">Nomor Surat</label>
                                <div class="relative flex gap-2">
                                    @if (auth()->user()->role === 1)
                                        <!-- Untuk Sekretaris (role 1): hanya tampilkan Generate Nomor biasa -->
                                        <button type="button" id="generateNomorBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 group">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor
                                        </button>
                                    @elseif (auth()->user()->role === 5)
                                        <!-- Untuk Sekretaris ASP (role 5): tampilkan Generate Nomor ASP dan Manager Keuangan -->
                                        <button type="button" id="generateNomorAspBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 group">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor ASP
                                        </button>
                                        <button type="button" id="generateNomorManagerKeuanganBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-100 rounded-lg hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 group hidden">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor Manager Keuangan
                                        </button>
                                        <button type="button" id="generateNomorDirutAspBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 group hidden">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor Direktur
                                        </button>
                                    @elseif (auth()->user()->role === 8)
                                        <!-- Untuk Direktur ASP (role 8): hanya tampilkan Generate Nomor ASP -->
                                        <button type="button" id="generateNomorAspBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 group">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor ASP
                                        </button>
                                    @elseif (auth()->user()->role === 0 || auth()->user()->role === 3 || auth()->user()->role === 4)
                                        <!-- Untuk Staff (role 0), Admin (role 3), atau Manager (role 4): tampilkan Generate Nomor biasa -->
                                        <button type="button" id="generateNomorBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 group">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor
                                        </button>
                                    @else
                                        <!-- Untuk role lain: tampilkan Generate Nomor biasa -->
                                        <button type="button" id="generateNomorBtn"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 group">
                                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                                            Generate Nomor
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="relative">
                                <input type="text" name="nomor_surat" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                                    placeholder="Nomor surat akan digenerate otomatis">
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                <i class="ri-information-line mr-1"></i>
                                Gunakan tanda strip (-) jika ingin menggunakan nomor surat yang sama dengan surat lain.
                            </p>
                        </div>

                        <!-- Tanggal Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Tanggal Surat</label>
                            <div class="relative">
                                <input type="date" name="tanggal_surat" required
                                    value="{{ old('tanggal_surat', date('Y-m-d')) }}"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                            </div>
                            @error('tanggal_surat')
                                <p class="text-red-500 text-xs">
                                    <i class="ri-error-warning-line mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Jenis Surat -->
                        @if (auth()->user()->role === 0 || auth()->user()->role === 3 || auth()->user()->role === 4)
                            <input type="hidden" name="jenis_surat" id="jenis_surat" value="internal">
                        @elseif (auth()->user()->role === 1 || auth()->user()->role === 5 || auth()->user()->role === 8)
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-800">Jenis Surat</label>
                                <div class="relative">
                                    <select name="jenis_surat" id="jenis_surat" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 appearance-none bg-white">
                                        <option value="internal" {{ old('jenis_surat') == 'internal' ? 'selected' : '' }}>
                                            Internal
                                        </option>
                                        <option value="eksternal" {{ old('jenis_surat') == 'eksternal' ? 'selected' : '' }}>
                                            Eksternal
                                        </option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <i class="ri-arrow-down-s-line"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Sifat Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Sifat Surat</label>
                            <div class="relative">
                                <select name="sifat_surat" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 appearance-none bg-white">
                                    <option value="normal" {{ old('sifat_surat') == 'normal' ? 'selected' : '' }}>
                                        Normal
                                    </option>
                                    <option value="urgent" {{ old('sifat_surat') == 'urgent' ? 'selected' : '' }}>
                                        Urgent
                                    </option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Placeholder agar grid tetap rapi -->
                        <div></div>
                    </div>

                    <!-- Perusahaan -->
                    <div class="space-y-2 perusahaan-container" id="perusahaan-container" style="min-height: 120px; display: none;">
                        <label class="text-sm font-semibold text-gray-800">Perusahaan</label>
                        <div class="suggestions-wrapper">
                            <input type="text" 
                                id="perusahaan_search" 
                                name="perusahaan_search"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                                placeholder="Cari atau tambah perusahaan baru..."
                                autocomplete="off">
                            <div id="perusahaan-suggestions" class="suggestions-list">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Perihal -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800">Perihal</label>
                        <textarea name="perihal" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                            rows="3" placeholder="Masukkan perihal surat">{{ old('perihal') }}</textarea>
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
                        Upload File Surat
                    </h3>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <!-- File Upload Area -->
                        <div class="w-full">
                            <input type="file" name="file[]" id="file-input" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                            <label for="file-input" class="w-full">
                                <div
                                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-200 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200">
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
                    <h3 class="text-sm font-semibold text-gray-800 card-disposisi-title">
                        <i class="ri-share-forward-line mr-2 text-gray-600"></i>
                        Disposisi Surat
                    </h3>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Debug field to track disposisi data -->
                    <input type="hidden" id="debug_disposisi_field" name="debug_disposisi_field" value="disposisi_data_tracking">
                    
                    <!-- Hidden input untuk status default Sekretaris ASP -->
                    @if (auth()->user()->role === 5)
                        <input type="hidden" name="status_sekretaris_default" value="approved">
                    @endif
                    
                    <!-- Hidden input untuk status default Direktur ASP -->
                    @if (auth()->user()->role === 8)
                        <input type="hidden" name="status_sekretaris_default" value="approved">
                    @endif
                    
                    <!-- Tujuan Disposisi (Multiple Select dengan Search) -->
                    <div class="space-y-2 mt-4">
                        <label class="text-sm font-semibold text-gray-800 label-tujuan-disposisi">Tujuan Disposisi</label>
                        <div class="relative">
                            <!-- Search Input -->
                            <div class="mb-2 relative">
                                <input type="text" id="tujuan-search"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200 input-tujuan-disposisi"
                                    placeholder="Cari nama atau jabatan..."
                                    autocomplete="off">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="ri-search-line"></i>
                                </div>
                            </div>
                            <!-- Selected Users Badge -->
                            <div id="selected-users-badge" class="flex flex-wrap gap-2 mb-2"></div>
                            <!-- Selection Box (dynamic show/hide) -->
                            <div class="relative">
                                <div class="flex justify-between mb-2 items-center">
                                    <div class="text-xs text-gray-500" id="selection-counter">0 dipilih</div>
                                    <div class="space-x-2">
                                        <button type="button" id="select-all-btn"
                                            class="px-2 py-1 text-xs bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors duration-200">
                                            Pilih Semua
                                        </button>
                                        <button type="button" id="clear-all-btn"
                                            class="px-2 py-1 text-xs bg-gray-50 text-gray-600 rounded hover:bg-gray-100 transition-colors duration-200">
                                            Hapus Semua
                                        </button>
                                    </div>
                                </div>
                                <div id="tujuan-selection-container"
                                    class="border border-gray-200 rounded-lg p-3 max-h-60 overflow-y-auto" style="display:none;">
                                    <div class="space-y-2">
                                        @php
                                            $allowedRolesAsp = [1, 2, 6, 7, 8]; // Sekretaris, Dirut, GM, Keuangan
                                        @endphp
                                        @foreach ($users as $user)
                                            @if ((auth()->user()->role === 5 && in_array($user->role, $allowedRolesAsp)) || auth()->user()->role !== 5 && in_array($user->role, [1,2,4,5,7,8]))
                                                <div class="flex items-center py-1.5 px-2 hover:bg-gray-50 rounded-md user-selection-item">
                                                    <input type="checkbox" id="user-{{ $user->id }}"
                                                        name="tujuan_disposisi[]" value="{{ $user->id }}"
                                                        class="tujuan-checkbox h-4 w-4 border-gray-300 rounded"
                                                        @if ($user->role == 2) checked @endif>
                                                    <label for="user-{{ $user->id }}"
                                                        class="ml-3 block text-sm text-gray-700 cursor-pointer truncate">
                                                        {{ $user->name }}
                                                        @if ($user->jabatan)
                                                            <span class="text-gray-500">({{ $user->jabatan->nama_jabatan }})</span>
                                                        @else
                                                            <span class="text-gray-500">(Tidak ada jabatan)</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                        {{-- Tambahkan GM ke list jika user login adalah manager dan punya general_manager_id --}}
                                        @php
                                            $authUser = auth()->user();
                                            $gmUser = null;
                                            if ($authUser->role === 4 && $authUser->general_manager_id) {
                                                $gmUser = $users->first(function($u) use ($authUser) {
                                                    return $u->role === 6 && $u->id == $authUser->general_manager_id;
                                                });
                                            }
                                        @endphp
                                        @if ($gmUser)
                                            <div class="flex items-center py-1.5 px-2 hover:bg-gray-50 rounded-md user-selection-item">
                                                <input type="checkbox" id="user-{{ $gmUser->id }}"
                                                    name="tujuan_disposisi[]" value="{{ $gmUser->id }}"
                                                    class="tujuan-checkbox h-4 w-4 border-gray-300 rounded">
                                                <label for="user-{{ $gmUser->id }}"
                                                    class="ml-3 block text-sm text-gray-700 cursor-pointer truncate">
                                                    {{ $gmUser->name }}
                                                    @if ($gmUser->jabatan)
                                                        <span class="text-gray-500">({{ $gmUser->jabatan->nama_jabatan }})</span>
                                                    @else
                                                        <span class="text-gray-500">(General Manager)</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endif
                                        <div id="no-user-found" class="text-center text-gray-400 text-xs py-2" style="display:none;">Tidak ada user ditemukan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Keterangan Pengirim -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800 label-keterangan-pengirim">Keterangan Pengirim</label>
                        <textarea name="keterangan_pengirim"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                            rows="3" placeholder="Tambahkan keterangan untuk penerima disposisi">{{ old('keterangan_pengirim') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('suratkeluar.index') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="ri-arrow-left-line mr-2"></i>
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="ri-save-line mr-2"></i>
                    Simpan Surat
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Preview File -->
    <div id="file-preview-modal" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="flex items-center justify-center min-h-screen p-0 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-90"></div>
            </div>
            <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all my-0 max-w-[50%] sm:max-w-[50%] md:max-w-[50%] lg:max-w-[50%] xl:max-w-[50%] w-full h-[98vh]">
                <div class="bg-white px-3 py-3 flex justify-between items-center border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 truncate max-w-[70%]" id="preview-modal-title">
                        Preview File Surat
                    </h3>
                    <div class="flex space-x-2">
                        <a href="#" id="preview-download-link"
                            class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="ri-download-line mr-1"></i> Download
                        </a>
                        <button type="button" id="close-preview-modal"
                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="ri-close-line mr-1"></i> Tutup
                        </button>
                    </div>
                </div>
                <div class="overflow-hidden h-full w-full bg-gray-100">
                    <div id="loading-preview" class="flex flex-col justify-center items-center h-full w-full">
                        <i class="ri-loader-4-line animate-spin text-5xl text-green-600"></i>
                        <p class="mt-4 text-gray-600">Memuat file...</p>
                    </div>
                    <iframe id="pdf-preview" class="w-full h-full hidden border-0" src="" style="min-height: 700px;"></iframe>
                    <div id="error-preview" class="flex flex-col justify-center items-center h-full w-full hidden">
                        <i class="ri-error-warning-line text-5xl text-red-600"></i>
                        <p class="mt-4 text-gray-600 font-medium">File tidak dapat ditampilkan</p>
                        <p class="mt-1 text-gray-500">Silahkan download file untuk melihatnya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if(session('validationErrors'))
<script>
    window.validationErrors = @json(session('validationErrors'));
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Deklarasi variabel global untuk autocomplete perusahaan
        let suggestionsContainer = document.getElementById('perusahaan-suggestions');
        let searchTimeout;
        // Get DOM elements - declare all variables just once
        const fileInput = document.getElementById('file-input');
        const uploadText = document.getElementById('upload-text');
        const fileSelected = document.getElementById('file-selected');
        const selectedFilename = document.getElementById('selected-filename');
        const selectedFilesize = document.getElementById('selected-filesize');
        const removeFileBtn = document.getElementById('remove-file');
        const generateNomorBtn = document.getElementById('generateNomorBtn');
        const jenisSuratSelect = document.getElementById('jenis_surat');
        const nomorSuratInput = document.querySelector('input[name="nomor_surat"]');
        const asDirutToggle = document.getElementById('toggle-as-dirut');
        const asDirutAspToggle = document.getElementById('toggle-as-dirut-asp');
        const asManagerKeuanganToggle = document.getElementById('toggle-as-manager-keuangan');
        const generateNomorManagerKeuanganBtn = document.getElementById('generateNomorManagerKeuanganBtn');
        const generateNomorDirutAspBtn = document.getElementById('generateNomorDirutAspBtn');
        const pengirimIdInput = document.getElementById('pengirim_id');
        const tujuanDisposisiContainer = document.querySelector('#tujuan-selection-container');
        const tujuanCheckboxes = document.querySelectorAll('.tujuan-checkbox');
        const perusahaanContainer = document.getElementById('perusahaan-container');
        const perusahaanHidden = document.getElementById('perusahaan_hidden');
        const perusahaanSearch = document.getElementById('perusahaan_search');
        const tujuanSearch = document.getElementById('tujuan-search');
        const selectAllBtn = document.getElementById('select-all-btn');
        const clearAllBtn = document.getElementById('clear-all-btn');
        const selectionCounter = document.getElementById('selection-counter');
        const userItems = document.querySelectorAll('.user-selection-item');
        const form = document.querySelector('form');
        
        // Cek role pengguna
        const userRole = {{ auth()->user()->role ?? 'null' }};
        
        // Log role untuk debugging
        console.log("User role:", userRole);

        // Handle perusahaan container visibility based on jenis_surat
        if (jenisSuratSelect) {
            jenisSuratSelect.addEventListener('change', function() {
                console.log("Jenis surat changed to:", this.value);
                const generateNomorBtn = document.getElementById('generateNomorBtn');
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                
                // Set perusahaan otomatis untuk internal
                if (this.value === 'internal') {
                    perusahaanHidden.value = 'RSAZRA';
                } else {
                    perusahaanHidden.value = '';
                }
                
                // Untuk role sekretaris (1)
                if (userRole === 1) {
                    if (this.value === 'eksternal') {
                    if (generateNomorBtn) {
                        generateNomorBtn.style.display = 'inline-flex';
                        generateNomorBtn.innerHTML = `
                            <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                            Generate Nomor AZRA
                        `;
                        generateNomorBtn.onclick = generateNomorAzra;
                    }
                    if (generateNomorAspBtn) {
                        generateNomorAspBtn.style.display = 'inline-flex';
                    }
                    nomorSuratInput.readOnly = false;
                    nomorSuratInput.placeholder = "Masukkan nomor surat eksternal";
                    nomorSuratInput.classList.remove('bg-gray-50');
                    nomorSuratInput.classList.add('bg-white');
                    // Info text
                    const nomorSuratContainer = nomorSuratInput.closest('.space-y-2');
                    let infoText = nomorSuratContainer?.querySelector('.text-xs.text-gray-500');
                    if (infoText) {
                        infoText.innerHTML = `
                            <i class="ri-information-line mr-1"></i>
                            Jangan menekan Generate Nomor Azra jika Perusahaan Bukan RS AZRA
                        `;
                    }
                    } else if (this.value === 'internal') {
                        if (generateNomorBtn) {
                            generateNomorBtn.style.display = 'inline-flex';
                            //generateNomorBtn.onclick = generateNomorSurat;
                        }
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'none';
                        }
                        nomorSuratInput.readOnly = false;
                        nomorSuratInput.placeholder = "Nomor surat akan digenerate otomatis";
                        nomorSuratInput.value = "";
                        nomorSuratInput.classList.remove('bg-gray-50');
                        nomorSuratInput.classList.add('bg-white');
                        // Info text
                        const nomorSuratContainer = nomorSuratInput.closest('.space-y-2');
                        let infoText = nomorSuratContainer?.querySelector('.text-xs.text-gray-500');
                        if (infoText) {
                            infoText.innerHTML = `
                                <i class="ri-information-line mr-1"></i>
                                Gunakan tanda strip (-) jika ingin menggunakan nomor surat yang sama dengan surat lain.
                            `;
                        }
                    }
                } else if (userRole === 5) { // Sekretaris ASP (role 5)
                    // Untuk Sekretaris ASP: selalu tampilkan Generate Nomor ASP
                    if (generateNomorBtn) {
                        generateNomorBtn.style.display = 'none'; // Sembunyikan Generate Nomor biasa
                    }
                    if (generateNomorAspBtn) {
                        generateNomorAspBtn.style.display = 'inline-flex';
                        //generateNomorAspBtn.onclick = generateNomorAsp;
                    }
                    nomorSuratInput.readOnly = false;
                    nomorSuratInput.placeholder = "Nomor surat akan digenerate otomatis";
                    nomorSuratInput.value = "";
                    nomorSuratInput.classList.remove('bg-gray-50');
                    nomorSuratInput.classList.add('bg-white');
                    // Info text
                    const nomorSuratContainer = nomorSuratInput.closest('.space-y-2');
                    let infoText = nomorSuratContainer?.querySelector('.text-xs.text-gray-500');
                    if (infoText) {
                        infoText.innerHTML = `
                            <i class="ri-information-line mr-1"></i>
                            Gunakan Generate Nomor ASP untuk membuat nomor surat ASP.
                        `;
                    }
                    
                    // Set perusahaan otomatis untuk ASP
                    if (this.value === 'internal') {
                        perusahaanHidden.value = 'ASP';
                    } else {
                        perusahaanHidden.value = '';
                    }
                } else if (userRole === 8) { // Direktur ASP (role 8)
                    // Untuk Direktur ASP: selalu tampilkan Generate Nomor ASP
                    if (generateNomorBtn) {
                        generateNomorBtn.style.display = 'none'; // Sembunyikan Generate Nomor biasa
                    }
                    if (generateNomorAspBtn) {
                        generateNomorAspBtn.style.display = 'inline-flex';
                        //generateNomorAspBtn.onclick = generateNomorAsp;
                    }
                    nomorSuratInput.readOnly = false;
                    nomorSuratInput.placeholder = "Nomor surat akan digenerate otomatis";
                    nomorSuratInput.value = "";
                    nomorSuratInput.classList.remove('bg-gray-50');
                    nomorSuratInput.classList.add('bg-white');
                    // Info text
                    const nomorSuratContainer = nomorSuratInput.closest('.space-y-2');
                    let infoText = nomorSuratContainer?.querySelector('.text-xs.text-gray-500');
                    if (infoText) {
                        infoText.innerHTML = `
                            <i class="ri-information-line mr-1"></i>
                            Gunakan Generate Nomor ASP untuk membuat nomor surat ASP.
                        `;
                    }
                    
                    // Set perusahaan otomatis untuk ASP
                    if (this.value === 'internal') {
                        perusahaanHidden.value = 'ASP';
                    } else {
                        perusahaanHidden.value = '';
                    }
                } else if (userRole === 0 || userRole === 3 || userRole === 4) { // Staff/unit, Admin, atau Manager
                    if (this.value === 'internal') {
                        if (generateNomorBtn) {
                            generateNomorBtn.style.display = 'inline-flex';
                            //generateNomorBtn.onclick = generateNomorSurat;
                        }
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'none';
                        }
                        nomorSuratInput.readOnly = false;
                        nomorSuratInput.placeholder = "Nomor surat akan digenerate otomatis";
                        nomorSuratInput.value = "";
                        nomorSuratInput.classList.remove('bg-gray-50');
                        nomorSuratInput.classList.add('bg-white');
                        // Info text
                        const nomorSuratContainer = nomorSuratInput.closest('.space-y-2');
                        let infoText = nomorSuratContainer?.querySelector('.text-xs.text-gray-500');
                        if (infoText) {
                            infoText.innerHTML = `
                                <i class="ri-information-line mr-1"></i>
                                Gunakan tanda strip (-) jika ingin menggunakan nomor surat yang sama dengan surat lain.
                            `;
                        }
                    } else {
                        if (generateNomorBtn) {
                            generateNomorBtn.style.display = 'none';
                        }
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'none';
                        }
                        nomorSuratInput.readOnly = false;
                        nomorSuratInput.placeholder = "Masukkan nomor surat eksternal";
                        nomorSuratInput.value = "";
                        nomorSuratInput.classList.remove('bg-gray-50');
                        nomorSuratInput.classList.add('bg-white');
                    }
                } else {
                    // Untuk role lain, hide tombol (kecuali role 5)
                    if (generateNomorBtn && userRole !== 5) {
                        generateNomorBtn.style.display = 'none';
                    }
                    if (generateNomorAspBtn && userRole !== 5) {
                        generateNomorAspBtn.style.display = 'none';
                    }
                    nomorSuratInput.readOnly = false;
                    nomorSuratInput.placeholder = "Masukkan nomor surat eksternal";
                    nomorSuratInput.value = "";
                    nomorSuratInput.classList.remove('bg-gray-50');
                    nomorSuratInput.classList.add('bg-white');
                }
            });
            // Trigger change event on load to set the initial state
            jenisSuratSelect.dispatchEvent(new Event('change'));
            
            // Set perusahaan otomatis untuk internal saat halaman dimuat
            if (jenisSuratSelect.value === 'internal') {
                if (userRole === 5) {
                    perusahaanHidden.value = 'ASP'; // Untuk Sekretaris ASP
                } else if (userRole === 8) {
                    perusahaanHidden.value = 'ASP'; // Untuk Direktur ASP
                } else {
                    perusahaanHidden.value = 'RSAZRA'; // Untuk role lain
                }
            }
            
            // Logic khusus untuk role 5 (Sekretaris ASP)
            if (userRole === 5) {
                // Pastikan Generate Nomor ASP selalu terlihat untuk Sekretaris ASP
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.style.display = 'inline-flex';
                    //generateNomorAspBtn.onclick = generateNomorAsp;
                }
                
                // Sembunyikan Generate Nomor biasa untuk Sekretaris ASP
                const generateNomorBtn = document.getElementById('generateNomorBtn');
                if (generateNomorBtn) {
                    generateNomorBtn.style.display = 'none';
                }
                
                console.log("Sekretaris ASP configuration applied - Generate Nomor ASP button enabled");
            }
            
            // Logic khusus untuk role 8 (Direktur ASP)
            if (userRole === 8) {
                // Pastikan Generate Nomor ASP selalu terlihat untuk Direktur ASP
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.style.display = 'inline-flex';
                    //generateNomorAspBtn.onclick = generateNomorAsp;
                }
                
                // Sembunyikan Generate Nomor biasa untuk Direktur ASP
                const generateNomorBtn = document.getElementById('generateNomorBtn');
                if (generateNomorBtn) {
                    generateNomorBtn.style.display = 'none';
                }
                
                console.log("Direktur ASP configuration applied - Generate Nomor ASP button enabled");
            }
            
            // Logic khusus untuk role 4 (Manager)
            if (userRole === 4) {
                // Pastikan Generate Nomor selalu terlihat untuk Manager
                const generateNomorBtn = document.getElementById('generateNomorBtn');
                if (generateNomorBtn) {
                    generateNomorBtn.style.display = 'inline-flex';
                    //generateNomorBtn.onclick = generateNomorSurat;
                }
                
                // Sembunyikan Generate Nomor ASP untuk Manager
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.style.display = 'none';
                }
                
                console.log("Manager configuration applied - Generate Nomor button enabled");
            }
        }

        if (asDirutToggle) {
            asDirutToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                const dotElement = document.querySelector('.dot');
                const tujuanDisposisiContainer = document.querySelector('#tujuan-selection-container')
                    ?.closest('.space-y-2');

                console.group('Toggle As Dirut State Change');
                console.log('Toggle checked:', isChecked);

                if (dotElement) {
                    dotElement.classList.toggle('translate-x-5', isChecked);
                }

                // Visual feedback when toggled
                const toggleContainer = this.closest('div.bg-indigo-50');
                if (toggleContainer) {
                    if (isChecked) {
                        console.log('Toggle activated - Setting up for Direktur mode');

                        toggleContainer.classList.remove('bg-indigo-50', 'border-indigo-100');
                        toggleContainer.classList.add('bg-green-50', 'border-green-100');

                                    // Tampilkan SweetAlert sukses
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Mode Surat Direktur',
                                        text: 'Surat akan dikirim atas nama direktur dan didisposisikan ke Direktur Utama',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true
                                    });
                    } else {
                        console.log('Toggle deactivated - Reverting to normal mode');

                        toggleContainer.classList.remove('bg-green-50', 'border-green-100');
                        toggleContainer.classList.add('bg-indigo-50', 'border-indigo-100');
                    }
                }
                console.groupEnd();

                // Reset dan generate ulang nomor surat saat toggle berubah
                if (nomorSuratInput && nomorSuratInput.value) {
                    // Reset nomor surat and generate again
                    nomorSuratInput.value = '';
                    generateNomorSurat();
                }
            });
        }

        // Function to update selection counter
        function updateSelectionCounter() {
            const selectionCounterElem = document.getElementById('selection-counter');
            if (!selectionCounterElem) {
                // If selection counter element doesn't exist, exit function
                console.log('Selection counter element not found');
                return;
            }

            const totalSelected = document.querySelectorAll('.tujuan-checkbox:checked').length;
            const totalItems = document.querySelectorAll('.tujuan-checkbox').length;
            selectionCounterElem.textContent = `${totalSelected} dipilih`;
        }

        // Initialize counter
        updateSelectionCounter();

        // Search functionality
        if (tujuanSearch) {
            tujuanSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                userItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Select all button
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll(
                    '.user-selection-item:not([style*="display: none"]) .tujuan-checkbox');
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectionCounter();
            });
        }

        // Clear all button
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll(
                    '.user-selection-item:not([style*="display: none"]) .tujuan-checkbox');
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectionCounter();
            });
        }

        // Add event listeners to checkboxes
        document.querySelectorAll('.tujuan-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionCounter);
        });

        // Function to generate nomor surat
        async function generateNomorSurat() {
            try {
                const tanggalSurat = document.querySelector('input[name="tanggal_surat"]').value;
                const jenisSurat = jenisSuratSelect?.value || 'internal';
                const isAsDirut = asDirutToggle?.checked || false;
                const isAsManagerKeuangan = asManagerKeuanganToggle?.checked || false;

                console.group('Generate Nomor Surat');
                console.log('Tanggal Surat:', tanggalSurat);
                console.log('Jenis Surat:', jenisSurat);
                console.log('As Dirut:', isAsDirut);
                console.log('As Manager Keuangan:', isAsManagerKeuangan);
                console.log('User Role:', userRole);

                // If external letter and role is 1, don't need to generate number
                if (userRole === 1 && jenisSurat === 'eksternal') {
                    console.log('Nomor surat eksternal, input manual');
                    console.groupEnd();
                    return;
                }

                if (!tanggalSurat) {
                    console.log('Tanggal surat tidak diisi');
                    console.groupEnd();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih tanggal surat terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981'
                    });
                    return;
                }

                // Show loading state
                if (generateNomorBtn) {
                    generateNomorBtn.disabled = true;
                    generateNomorBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Generating...
                `;
                }

                // Get current job title and code
                let namaJabatan = "{{ auth()->user()->jabatan->nama_jabatan ?? 'UMUM' }}";
                let kodeJabatan = "{{ auth()->user()->jabatan->kode_jabatan ?? 'UMUM' }}";
                
                if (isAsDirut) {
                    namaJabatan = "Direktur Utama";
                    kodeJabatan = "DIRRS";
                } else if (isAsManagerKeuangan) {
                    namaJabatan = "Manager Keuangan";
                    kodeJabatan = "Dir.Adm.Keu";
                }

                console.log('Generate nomor surat untuk jabatan:', namaJabatan, 'dengan kode:', kodeJabatan);

                // Create FormData object for request
                const formData = new FormData();
                formData.append('kode_jabatan', kodeJabatan);
                formData.append('is_as_dirut', isAsDirut ? '1' : '0');
                formData.append('is_as_manager_keuangan', isAsManagerKeuangan ? '1' : '0');
                formData.append('_token', '{{ csrf_token() }}');

                // Send request using fetch API
                const response = await fetch("{{ route('suratkeluar.getLastNumber') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Response data:", data);

                // Reset button state
                if (generateNomorBtn) {
                    generateNomorBtn.disabled = false;
                    generateNomorBtn.innerHTML = `
                    <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                    Generate Nomor
                `;
                }

                // Show Sweet Alert for confirmation
                const result = await Swal.fire({
                    title: 'Generate Nomor Surat',
                    html: `
                        <div class="text-left">
                            <p class="mb-2">Informasi nomor surat:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Jabatan: <span class="font-semibold">${namaJabatan}</span></li>
                                <li>Nomor urut terakhir: <span class="font-semibold">${data.last_number}</span></li>
                                <li>Nomor urut berikutnya: <span class="font-semibold">${String(parseInt(data.last_number) + 1).padStart(3, '0')}</span></li>
                            </ul>
                            <p class="mt-4">Apakah Anda ingin menggunakan nomor surat ini?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gunakan',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                });

                if (result.isConfirmed) {
                    // Create letter number
                    const nextNumber = String(parseInt(data.last_number) + 1).padStart(3, '0');

                    // Get month and year from letter date
                    const date = new Date(tanggalSurat);
                    const bulan = date.getMonth() + 1;
                    const tahun = date.getFullYear();

                    // Generate nomor surat with desired format
                    const nomorSurat = `${nextNumber}/${kodeJabatan}/RSAZRA/${convertToRoman(bulan)}/${tahun}`;

                    // Set value to nomor surat input
                    if (nomorSuratInput) {
                    nomorSuratInput.value = nomorSurat;
                    }

                    // Success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Nomor surat berhasil digenerate',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
                console.groupEnd();
            } catch (error) {
                console.error('Error lengkap:', error);
                console.groupEnd();

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menggenerate nomor surat. Silakan coba lagi.',
                    footer: 'Detail error: ' + error.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10B981'
                });
                
                // Reset button state in case of error
                if (generateNomorBtn) {
                    generateNomorBtn.disabled = false;
                    generateNomorBtn.innerHTML = `
                    <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                    Generate Nomor
                    `;
                }
            }
        }

        // Event listener for generate button
        if (generateNomorBtn) {
            generateNomorBtn.addEventListener('click', generateNomorSurat);
        }

        // Tambahkan event listener untuk tombol Generate Nomor ASP
        const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
        if (generateNomorAspBtn) {
            generateNomorAspBtn.addEventListener('click', generateNomorAsp);
            console.log("Event listener attached to Generate Nomor ASP button.");
            
            // Untuk role 5 (Sekretaris ASP), pastikan button selalu terlihat dan berfungsi
            if (userRole === 5) {
                generateNomorAspBtn.style.display = 'inline-flex';
                //generateNomorAspBtn.onclick = generateNomorAsp;
                console.log("Generate Nomor ASP button configured for Sekretaris ASP role.");
            }
            
            // Untuk role 8 (Direktur ASP), pastikan button selalu terlihat dan berfungsi
            if (userRole === 8) {
                generateNomorAspBtn.style.display = 'inline-flex';
                //generateNomorAspBtn.onclick = generateNomorAsp;
                console.log("Generate Nomor ASP button configured for Direktur ASP role.");
            }
        }

        // Tambahkan event listener untuk tombol Generate Nomor Manager Keuangan
        if (generateNomorManagerKeuanganBtn) {
            generateNomorManagerKeuanganBtn.addEventListener('click', generateNomorManagerKeuangan);
            console.log("Event listener attached to Generate Nomor Manager Keuangan button.");
        }

        // Tambahkan event listener untuk tombol Generate Nomor Direktur
        if (generateNomorDirutAspBtn) {
            generateNomorDirutAspBtn.addEventListener('click', generateNomorDirutAsp);
            console.log("Event listener attached to Generate Nomor Direktur button.");
        }

        // Event listener for date change
        const tanggalSuratInput = document.querySelector('input[name="tanggal_surat"]');
        if (tanggalSuratInput && nomorSuratInput) {
            tanggalSuratInput.addEventListener('change', () => {
                if (!nomorSuratInput.value) {
                    generateNomorSurat();
                }
            });
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
            uploadText.classList.remove('hidden');
            fileSelected.classList.add('hidden');
        }

        // File input change event
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(this.files);
                const filesList = document.getElementById('selected-files-list');
                if (files.length > 0) {
                    uploadText.classList.add('hidden');
                    fileSelected.classList.remove('hidden');
                    filesList.innerHTML = '';
                    files.forEach((file, idx) => {
                        const fileDiv = document.createElement('div');
                        fileDiv.className = 'flex items-center justify-between bg-gray-50 rounded p-2 border border-gray-200';
                        fileDiv.innerHTML = `
                            <div class="flex items-center">
                                <i class="ri-file-text-line text-xl text-green-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">${file.name}</span>
                                <span class="text-xs text-gray-500 ml-2">(${formatFileSize(file.size)})</span>
                            </div>
                            <button type="button" class="remove-file-btn text-xs text-red-500 hover:underline ml-4" data-idx="${idx}">Hapus</button>
                        `;
                        filesList.appendChild(fileDiv);
                    });
                } else {
                    resetFileInput();
                }
            });
        }

        // Remove all files
        const removeAllBtn = document.getElementById('remove-all-files');
        if (removeAllBtn) {
            removeAllBtn.addEventListener('click', function() {
                fileInput.value = '';
                uploadText.classList.remove('hidden');
                fileSelected.classList.add('hidden');
                document.getElementById('selected-files-list').innerHTML = '';
            });
        }

        // Remove individual file
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-file-btn')) {
                fileInput.value = '';
                uploadText.classList.remove('hidden');
                fileSelected.classList.add('hidden');
                document.getElementById('selected-files-list').innerHTML = '';
            }
        });

        // Drag and drop functionality
        const dropZone = document.querySelector('label[for="file-input"]');
        if (dropZone) {
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
            if (files.length > 0 && (files[0].type === 'application/pdf' ||
                    files[0].type === 'application/msword' ||
                    files[0].type ===
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document') && fileInput) {
                fileInput.files = files;
                showFilePreview(files[0]);
            }
        });
        }

        // Function to convert number to Roman numeral
        function convertToRoman(num) {
            const romanNumerals = {
                1: 'I',
                2: 'II',
                3: 'III',
                4: 'IV',
                5: 'V',
                6: 'VI',
                7: 'VII',
                8: 'VIII',
                9: 'IX',
                10: 'X',
                11: 'XI',
                12: 'XII'
            };
            return romanNumerals[num];
        }

        // Function to generate nomor surat for external AZRA
        async function generateNomorAzra() {
            console.log("generateNomorAzra function called.");
            try {
                const tanggalSurat = document.querySelector('input[name="tanggal_surat"]').value;
                
                if (!tanggalSurat) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih tanggal surat terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981'
                    });
                    return;
                }

                // Show loading state
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    console.log("Showing loading state for Generate Nomor ASP button.");
                    generateNomorAspBtn.disabled = true;
                    generateNomorAspBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generating...
                    `;
                }

                // Create FormData object for request
                const formData = new FormData();
                formData.append('is_eksternal_azra', '1');
                formData.append('tanggal_surat', tanggalSurat);
                formData.append('_token', '{{ csrf_token() }}');

                // Send request using fetch API
                const response = await fetch("{{ route('suratkeluar.getLastNumber') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    console.error("Fetch failed with status:", response.status);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Response data from getLastNumber:", data);

                // Reset button state
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.disabled = false;
                    generateNomorAspBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor AZRA
                    `;
                }

                // Show Sweet Alert for confirmation
                const result = await Swal.fire({
                    title: 'Generate Nomor Surat AZRA',
                    html: `
                        <div class="text-left">
                            <p class="mb-2">Informasi nomor surat:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Perusahaan: <span class="font-semibold">AZRA</span></li>
                                <li>Nomor urut terakhir: <span class="font-semibold">${data.last_number}</span></li>
                                <li>Nomor urut berikutnya: <span class="font-semibold">${String(parseInt(data.last_number) + 1).padStart(3, '0')}</span></li>
                            </ul>
                            <p class="mt-4">Apakah Anda ingin menggunakan nomor surat ini?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gunakan',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                });

                if (result.isConfirmed) {
                    // Create letter number
                    const nextNumber = String(parseInt(data.last_number) + 1).padStart(3, '0');

                    // Get month and year from letter date
                    const date = new Date(tanggalSurat);
                    const bulan = date.getMonth() + 1;
                    const tahun = date.getFullYear();

                    // Generate nomor surat with desired format
                    const nomorSurat = `${nextNumber}/RSAZRA/${convertToRoman(bulan)}/${tahun}`;

                    // Set value to nomor surat input
                    if (nomorSuratInput) {
                        nomorSuratInput.value = nomorSurat;
                    }

                    // Success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Nomor surat berhasil digenerate',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error in generateNomorAzra:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menggenerate nomor surat. Silakan coba lagi.',
                    footer: 'Detail error: ' + error.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10B981'
                });
                
                // Reset button state in case of error
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.disabled = false;
                    generateNomorAspBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor AZRA
                    `;
                }
            }
        }

        // Function to generate nomor surat ASP
        async function generateNomorAsp() {
            console.log("generateNomorAsp function called.");
            try {
                const tanggalSurat = document.querySelector('input[name="tanggal_surat"]').value;
                const isAsManagerKeuangan = asManagerKeuanganToggle?.checked || false;
                
                console.group('Generate Nomor Surat ASP');
                console.log('Tanggal Surat:', tanggalSurat);
                console.log('As Manager Keuangan:', isAsManagerKeuangan);
                console.log('User Role:', userRole);
                
                if (!tanggalSurat) {
                    console.log("Tanggal surat is empty.");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih tanggal surat terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981'
                    });
                    return;
                }

                // Show loading state
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    console.log("Showing loading state for Generate Nomor ASP button.");
                    generateNomorAspBtn.disabled = true;
                    generateNomorAspBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generating...
                    `;
                }

                // Create FormData object for request
                const formData = new FormData();
                formData.append('is_asp', '1');
                formData.append('tanggal_surat', tanggalSurat);
                formData.append('is_as_manager_keuangan', isAsManagerKeuangan ? '1' : '0');
                formData.append('_token', '{{ csrf_token() }}');

                // Send request using fetch API
                const response = await fetch("{{ route('suratkeluar.getLastNumber') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    console.error("Fetch failed with status:", response.status);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Response data from getLastNumber:", data);

                // Reset button state
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.disabled = false;
                    generateNomorAspBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor ASP
                    `;
                }

                if (!data.success) {
                    throw new Error(data.message || 'Gagal mendapatkan nomor surat');
                }

                // Parse last number and ensure it's a number
                const lastNumber = parseInt(data.last_number) || 0;
                const nextNumber = lastNumber + 1;
                console.log("Last number:", lastNumber, "Next number:", nextNumber);

                // Show Sweet Alert for confirmation
                const result = await Swal.fire({
                    title: 'Generate Nomor Surat ASP',
                    html: `
                        <div class="text-left">
                            <p class="mb-2">Informasi nomor surat:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Perusahaan: <span class="font-semibold">ASP</span></li>
                                <li>Nomor urut terakhir: <span class="font-semibold">${lastNumber}</span></li>
                                <li>Nomor urut berikutnya: <span class="font-semibold">${String(nextNumber).padStart(3, '0')}</span></li>
                            </ul>
                            <p class="mt-4">Apakah Anda ingin menggunakan nomor surat ini?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gunakan',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                });

                if (result.isConfirmed) {
                    // Get month and year from letter date
                    const date = new Date(tanggalSurat);
                    const bulan = date.getMonth() + 1;
                    const tahun = date.getFullYear();

                    // Generate nomor surat dengan format baru
                    const nomorSurat = `${String(nextNumber).padStart(3, '0')}/ASP/${convertToRoman(bulan)}/${tahun}`;
                    console.log("Generated nomor surat:", nomorSurat);

                    // Set value to nomor surat input
                    if (nomorSuratInput) {
                        nomorSuratInput.value = nomorSurat;
                    }

                    // Success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Nomor surat berhasil digenerate',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error in generateNomorAsp:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Gagal menggenerate nomor surat. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10B981'
                });
                
                // Reset button state in case of error
                const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                if (generateNomorAspBtn) {
                    generateNomorAspBtn.disabled = false;
                    generateNomorAspBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor ASP
                    `;
                }
            }
        }

        // Function to generate nomor surat Manager Keuangan
        async function generateNomorManagerKeuangan() {
            console.log("generateNomorManagerKeuangan function called.");
            try {
                const tanggalSurat = document.querySelector('input[name="tanggal_surat"]').value;
                
                console.group('Generate Nomor Surat Manager Keuangan');
                console.log('Tanggal Surat:', tanggalSurat);
                console.log('User Role:', userRole);
                
                if (!tanggalSurat) {
                    console.log("Tanggal surat is empty.");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih tanggal surat terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981'
                    });
                    return;
                }

                // Show loading state
                if (generateNomorManagerKeuanganBtn) {
                    console.log("Showing loading state for Generate Nomor Manager Keuangan button.");
                    generateNomorManagerKeuanganBtn.disabled = true;
                    generateNomorManagerKeuanganBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-purple-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generating...
                    `;
                }

                // Create FormData object for request
                const formData = new FormData();
                formData.append('kode_jabatan', 'Dir.Adm.Keu');
                formData.append('is_as_manager_keuangan', '1');
                formData.append('tanggal_surat', tanggalSurat);
                formData.append('_token', '{{ csrf_token() }}');

                // Send request using fetch API
                const response = await fetch("{{ route('suratkeluar.getLastNumber') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    console.error("Fetch failed with status:", response.status);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Response data from getLastNumber:", data);

                // Reset button state
                if (generateNomorManagerKeuanganBtn) {
                    generateNomorManagerKeuanganBtn.disabled = false;
                    generateNomorManagerKeuanganBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor Manager Keuangan
                    `;
                }

                if (!data.success) {
                    throw new Error(data.message || 'Gagal mendapatkan nomor surat');
                }

                // Parse last number and ensure it's a number
                const lastNumber = parseInt(data.last_number) || 0;
                const nextNumber = lastNumber + 1;
                console.log("Last number:", lastNumber, "Next number:", nextNumber);

                // Show Sweet Alert for confirmation
                const result = await Swal.fire({
                    title: 'Generate Nomor Surat Manager Keuangan',
                    html: `
                        <div class="text-left">
                            <p class="mb-2">Informasi nomor surat:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Jabatan: <span class="font-semibold">Manager Keuangan</span></li>
                                <li>Nomor urut terakhir: <span class="font-semibold">${lastNumber}</span></li>
                                <li>Nomor urut berikutnya: <span class="font-semibold">${String(nextNumber).padStart(3, '0')}</span></li>
                            </ul>
                            <p class="mt-4">Apakah Anda ingin menggunakan nomor surat ini?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gunakan',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                });

                if (result.isConfirmed) {
                    // Get month and year from letter date
                    const date = new Date(tanggalSurat);
                    const bulan = date.getMonth() + 1;
                    const tahun = date.getFullYear();

                    // Generate nomor surat dengan format Manager Keuangan
                    const nomorSurat = `${String(nextNumber).padStart(3, '0')}/Dir.Adm.Keu/RSAZRA/${convertToRoman(bulan)}/${tahun}`;
                    console.log("Generated nomor surat:", nomorSurat);

                    // Set value to nomor surat input
                    if (nomorSuratInput) {
                        nomorSuratInput.value = nomorSurat;
                    }

                    // Success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Nomor surat berhasil digenerate',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
                console.groupEnd();
            } catch (error) {
                console.error('Error in generateNomorManagerKeuangan:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Gagal menggenerate nomor surat. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10B981'
                });
                
                // Reset button state in case of error
                if (generateNomorManagerKeuanganBtn) {
                    generateNomorManagerKeuanganBtn.disabled = false;
                    generateNomorManagerKeuanganBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor Manager Keuangan
                    `;
                }
            }
        }

        // Function to generate nomor surat Direktur ASP
        async function generateNomorDirutAsp() {
            console.log("generateNomorDirutAsp function called.");
            try {
                const tanggalSurat = document.querySelector('input[name="tanggal_surat"]').value;
                
                console.group('Generate Nomor Surat Direktur ASP');
                console.log('Tanggal Surat:', tanggalSurat);
                console.log('User Role:', userRole);
                
                if (!tanggalSurat) {
                    console.log("Tanggal surat is empty.");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih tanggal surat terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981'
                    });
                    return;
                }

                // Show loading state
                if (generateNomorDirutAspBtn) {
                    console.log("Showing loading state for Generate Nomor Direktur button.");
                    generateNomorDirutAspBtn.disabled = true;
                    generateNomorDirutAspBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generating...
                    `;
                }

                // Create FormData object for request
                const formData = new FormData();
                formData.append('kode_jabatan', 'DIRRS');
                formData.append('is_as_dirut', '1');
                formData.append('tanggal_surat', tanggalSurat);
                formData.append('_token', '{{ csrf_token() }}');

                // Send request using fetch API
                const response = await fetch("{{ route('suratkeluar.getLastNumber') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    console.error("Fetch failed with status:", response.status);
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Response data from getLastNumber:", data);

                // Reset button state
                if (generateNomorDirutAspBtn) {
                    generateNomorDirutAspBtn.disabled = false;
                    generateNomorDirutAspBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor Direktur
                    `;
                }

                if (!data.success) {
                    throw new Error(data.message || 'Gagal mendapatkan nomor surat');
                }

                // Parse last number and ensure it's a number
                const lastNumber = parseInt(data.last_number) || 0;
                const nextNumber = lastNumber + 1;
                console.log("Last number:", lastNumber, "Next number:", nextNumber);

                // Show Sweet Alert for confirmation
                const result = await Swal.fire({
                    title: 'Generate Nomor Surat Direktur',
                    html: `
                        <div class="text-left">
                            <p class="mb-2">Informasi nomor surat:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Jabatan: <span class="font-semibold">Direktur Utama</span></li>
                                <li>Nomor urut terakhir: <span class="font-semibold">${lastNumber}</span></li>
                                <li>Nomor urut berikutnya: <span class="font-semibold">${String(nextNumber).padStart(3, '0')}</span></li>
                            </ul>
                            <p class="mt-4">Apakah Anda ingin menggunakan nomor surat ini?</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gunakan',
                    cancelButtonText: 'Tidak',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280'
                });

                if (result.isConfirmed) {
                    // Get month and year from letter date
                    const date = new Date(tanggalSurat);
                    const bulan = date.getMonth() + 1;
                    const tahun = date.getFullYear();

                    // Generate nomor surat dengan format Direktur
                    const nomorSurat = `${String(nextNumber).padStart(3, '0')}/DIRRS/RSAZRA/${convertToRoman(bulan)}/${tahun}`;
                    console.log("Generated nomor surat:", nomorSurat);

                    // Set value to nomor surat input
                    if (nomorSuratInput) {
                        nomorSuratInput.value = nomorSurat;
                    }

                    // Success notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Nomor surat berhasil digenerate',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
                console.groupEnd();
            } catch (error) {
                console.error('Error in generateNomorDirutAsp:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Gagal menggenerate nomor surat. Silakan coba lagi.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10B981'
                });
                
                // Reset button state in case of error
                if (generateNomorDirutAspBtn) {
                    generateNomorDirutAspBtn.disabled = false;
                    generateNomorDirutAspBtn.innerHTML = `
                        <i class="ri-refresh-line mr-1.5 group-hover:rotate-180 transition-transform duration-500"></i>
                        Generate Nomor Direktur
                    `;
                }
            }
        }

        // --- FIX: Tampilkan input perusahaan saat eksternal untuk role sekretaris ---
        function togglePerusahaanInput() {
            if (jenisSuratSelect && jenisSuratSelect.value === 'eksternal') {
                perusahaanContainer.style.display = 'block';
            } else {
                perusahaanContainer.style.display = 'none';
                if (perusahaanSearch) perusahaanSearch.value = '';
            }
        }
        if (jenisSuratSelect && perusahaanContainer) {
            togglePerusahaanInput();
            jenisSuratSelect.addEventListener('change', togglePerusahaanInput);
        }
        // --- Pastikan autocomplete tetap aktif ---
        if (perusahaanSearch) {
            perusahaanSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                if (query.length < 2) {
                    suggestionsContainer.style.display = 'none';
                    return;
                }
                searchTimeout = setTimeout(() => {
                    fetch(`/api/perusahaan/search?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                suggestionsContainer.innerHTML = data.data.map(perusahaan => `
                                    <div class="suggestion-item p-2 hover:bg-gray-100 cursor-pointer" 
                                         data-kode="${perusahaan.kode}"
                                         data-nama="${perusahaan.nama_perusahaan}">${perusahaan.nama_perusahaan}</div>
                                `).join('');
                                suggestionsContainer.style.display = 'block';
                            } else {
                                suggestionsContainer.innerHTML = `
                                    <div class="suggestion-item p-2 hover:bg-gray-100 cursor-pointer text-green-600" id="add-new-company"><i class="ri-add-line mr-2"></i>Tambah "${query}" sebagai perusahaan baru</div>
                                `;
                                suggestionsContainer.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            suggestionsContainer.style.display = 'none';
                        });
                }, 300);
            });
        }
        // --- Pilih suggestion atau tambah perusahaan baru ---
        if (suggestionsContainer) {
            suggestionsContainer.addEventListener('click', function(e) {
                const target = e.target.closest('.suggestion-item');
                if (!target) return;
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
                                perusahaanHidden.value = data.data.kode;
                                suggestionsContainer.style.display = 'none';
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Perusahaan baru berhasil ditambahkan',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        });
                    }
                } else {
                    const kode = target.dataset.kode;
                    const nama = target.dataset.nama;
                    perusahaanSearch.value = nama;
                    perusahaanHidden.value = kode;
                    suggestionsContainer.style.display = 'none';
                }
            });
        }

        // Validasi frontend sebelum submit
        if (form) {
            form.addEventListener('submit', function(e) {
                let errors = [];
                // Validasi nomor surat
                if (!nomorSuratInput.value.trim()) {
                    errors.push('Silakan isi nomor surat terlebih dahulu.');
                }
                // Validasi tanggal surat
                if (!tanggalSuratInput.value.trim()) {
                    errors.push('Silakan pilih tanggal surat.');
                }
                // Validasi perusahaan (hanya untuk eksternal)
                if (jenisSuratSelect && jenisSuratSelect.value === 'eksternal') {
                    if (!perusahaanHidden.value.trim()) {
                        errors.push('Perusahaan tujuan surat eksternal belum dipilih. Silakan pilih dari daftar.');
                    }
                }
                // Validasi perihal
                const perihalInput = document.querySelector('textarea[name="perihal"]');
                if (!perihalInput.value.trim()) {
                    errors.push('Silakan isi perihal surat.');
                }
                if (errors.length > 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        html: errors.map(e => `<div style=\"text-align:left\">${e}</div>`).join(''),
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#10B981'
                    });
                    return false;
                }
            });
        }

        // Handler error validasi backend (AJAX/JSON)
        if (window.validationErrors) {
            let errorList = Object.values(window.validationErrors).flat();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorList.map(e => `<div style=\"text-align:left\">${e}</div>`).join(''),
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#10B981'
            });
        }

        // Daftar elemen yang perlu diubah labelnya
        const disposisiToTembusan = [
            {
                selector: '.card-disposisi-title', // Judul card
                textDisposisi: 'Disposisi Surat',
                textTembusan: 'Tembusan Surat'
            },
            {
                selector: '.label-tujuan-disposisi', // Label tujuan
                textDisposisi: 'Tujuan Disposisi',
                textTembusan: 'Tujuan Tembusan'
            },
            {
                selector: '.label-keterangan-pengirim', // Label keterangan
                textDisposisi: 'Keterangan Pengirim',
                textTembusan: 'Keterangan Pengirim (untuk tembusan)'
            },
            {
                selector: '.input-tujuan-disposisi', // Placeholder search
                textDisposisi: 'Cari nama atau jabatan...',
                textTembusan: 'Cari nama atau jabatan...'
            }
        ];
        // Helper untuk update label
        function updateDisposisiLabels(isTembusan) {
            disposisiToTembusan.forEach(item => {
                const el = document.querySelector(item.selector);
                if (el) {
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.placeholder = isTembusan ? item.textTembusan : item.textDisposisi;
                    } else {
                        el.textContent = isTembusan ? item.textTembusan : item.textDisposisi;
                    }
                }
            });
        }
        // Inisialisasi label (untuk SSR)
        updateDisposisiLabels(asDirutToggle && asDirutToggle.checked);
        // Event listener toggle
        if (asDirutToggle) {
            asDirutToggle.addEventListener('change', function() {
                updateDisposisiLabels(this.checked);
            });
        }

        // Fungsi untuk update badge user terpilih
        function updateSelectedUsersBadge() {
            const badgeContainer = document.getElementById('selected-users-badge');
            badgeContainer.innerHTML = '';
            const checked = document.querySelectorAll('.tujuan-checkbox:checked');
            checked.forEach(cb => {
                const label = document.querySelector('label[for="' + cb.id + '"]');
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1 mb-1 cursor-pointer selected-user-badge';
                badge.innerHTML = `<i class='ri-user-line mr-1'></i> ${label ? label.textContent.trim() : cb.value} <i class='ri-close-line ml-1 text-red-500'></i>`;
                badge.dataset.userid = cb.value;
                badge.title = 'Klik untuk hapus';
                badgeContainer.appendChild(badge);
            });
        }
        // Inisialisasi badge saat load
        updateSelectedUsersBadge();
        // Update badge setiap kali checkbox berubah
        document.querySelectorAll('.tujuan-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedUsersBadge);
        });
        // Event: klik badge untuk uncheck user
        document.getElementById('selected-users-badge').addEventListener('click', function(e) {
            const badge = e.target.closest('.selected-user-badge');
            if (badge) {
                const userId = badge.dataset.userid;
                const cb = document.getElementById('user-' + userId);
                if (cb) {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                }
            }
        });

        // --- Perbaikan pencarian user disposisi agar saran tetap tampil ---
        if (tujuanSearch) {
            tujuanSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const container = document.getElementById('tujuan-selection-container');
                const userItems = document.querySelectorAll('.user-selection-item');
                let found = 0;
                if (searchTerm.length > 0) {
                    container.style.display = 'block';
                    userItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            item.style.display = 'flex';
                            found++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    // Tampilkan/hide pesan tidak ada user
                    document.getElementById('no-user-found').style.display = found === 0 ? 'block' : 'none';
                } else {
                    container.style.display = 'none';
                    userItems.forEach(item => { item.style.display = 'none'; });
                    document.getElementById('no-user-found').style.display = 'none';
                }
            });
        }
        // --- Tampilkan hasil pencarian jika sudah ada input saat load (misal dari autofill browser) ---
        if (tujuanSearch && tujuanSearch.value.length > 0) {
            tujuanSearch.dispatchEvent(new Event('input'));
        }

        // Toggle untuk Manager Keuangan (Sekretaris ASP)
        if (asManagerKeuanganToggle) {
            asManagerKeuanganToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                const dotElement = this.parentElement.querySelector('.dot');

                console.group('Toggle As Manager Keuangan State Change');
                console.log('Toggle checked:', isChecked);

                if (dotElement) {
                    dotElement.classList.toggle('translate-x-5', isChecked);
                }

                // Visual feedback when toggled
                const toggleContainer = this.closest('div.bg-blue-50');
                if (toggleContainer) {
                    if (isChecked) {
                        console.log('Toggle activated - Setting up for Manager Keuangan mode');

                        toggleContainer.classList.remove('bg-blue-50', 'border-blue-100');
                        toggleContainer.classList.add('bg-green-50', 'border-green-100');

                        // Tampilkan button Generate Nomor Manager Keuangan
                        if (generateNomorManagerKeuanganBtn) {
                            generateNomorManagerKeuanganBtn.classList.remove('hidden');
                        }

                        // Sembunyikan button Generate Nomor ASP
                        const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'none';
                        }

                        // Tampilkan SweetAlert sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Mode Surat Manager Keuangan',
                            text: 'Surat akan dikirim atas nama manager keuangan',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        console.log('Toggle deactivated - Reverting to normal mode');

                        toggleContainer.classList.remove('bg-green-50', 'border-green-100');
                        toggleContainer.classList.add('bg-blue-50', 'border-blue-100');

                        // Sembunyikan button Generate Nomor Manager Keuangan
                        if (generateNomorManagerKeuanganBtn) {
                            generateNomorManagerKeuanganBtn.classList.add('hidden');
                        }

                        // Tampilkan kembali button Generate Nomor ASP
                        const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'inline-flex';
                        }
                    }
                }
                console.groupEnd();

                // Reset dan generate ulang nomor surat saat toggle berubah
                if (nomorSuratInput && nomorSuratInput.value) {
                    // Reset nomor surat
                    nomorSuratInput.value = '';
                }
            });
        }

        // Function to update selection counter
        function updateSelectionCounter() {
            const selectionCounterElem = document.getElementById('selection-counter');
            if (!selectionCounterElem) {
                // If selection counter element doesn't exist, exit function
                console.log('Selection counter element not found');
                return;
            }

            const totalSelected = document.querySelectorAll('.tujuan-checkbox:checked').length;
            const totalItems = document.querySelectorAll('.tujuan-checkbox').length;
            selectionCounterElem.textContent = `${totalSelected} dipilih `;
        }

        // Initialize counter
        updateSelectionCounter();

        // Toggle untuk Direktur ASP (Sekretaris ASP)
        if (asDirutAspToggle) {
            asDirutAspToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                const dotElement = this.parentElement.querySelector('.dot');

                console.group('Toggle As Dirut ASP State Change');
                console.log('Toggle checked:', isChecked);

                if (dotElement) {
                    dotElement.classList.toggle('translate-x-5', isChecked);
                }

                // Visual feedback when toggled
                const toggleContainer = this.closest('div.bg-indigo-50');
                if (toggleContainer) {
                    if (isChecked) {
                        console.log('Toggle activated - Setting up for Direktur ASP mode');

                        toggleContainer.classList.remove('bg-indigo-50', 'border-indigo-100');
                        toggleContainer.classList.add('bg-green-50', 'border-green-100');

                        // Tampilkan button Generate Nomor Direktur
                        if (generateNomorDirutAspBtn) {
                            generateNomorDirutAspBtn.classList.remove('hidden');
                        }

                        // Sembunyikan button Generate Nomor ASP dan Manager Keuangan
                        const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'none';
                        }
                        if (generateNomorManagerKeuanganBtn) {
                            generateNomorManagerKeuanganBtn.classList.add('hidden');
                        }

                        // Tampilkan SweetAlert sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Mode Surat Direktur',
                            text: 'Surat akan dikirim atas nama direktur',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        console.log('Toggle deactivated - Reverting to normal mode');

                        toggleContainer.classList.remove('bg-green-50', 'border-green-100');
                        toggleContainer.classList.add('bg-indigo-50', 'border-indigo-100');

                        // Sembunyikan button Generate Nomor Direktur
                        if (generateNomorDirutAspBtn) {
                            generateNomorDirutAspBtn.classList.add('hidden');
                        }

                        // Tampilkan kembali button Generate Nomor ASP
                        const generateNomorAspBtn = document.getElementById('generateNomorAspBtn');
                        if (generateNomorAspBtn) {
                            generateNomorAspBtn.style.display = 'inline-flex';
                        }
                    }
                }
                console.groupEnd();

                // Reset dan generate ulang nomor surat saat toggle berubah
                if (nomorSuratInput && nomorSuratInput.value) {
                    // Reset nomor surat
                    nomorSuratInput.value = '';
                }
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

    /* Efek hover untuk generate button */
    #generateNomorBtn:hover i {
        transform: rotate(180deg);
    }

    /* Efek highlight untuk nomor surat */
    .highlight-input {
        animation: highlight 1s ease-in-out;
    }

    @keyframes highlight {
        0% {
            background-color: #ecfdf5;
            border-color: #10B981;
        }

        100% {
            background-color: #F9FAFB;
            border-color: #E5E7EB;
        }
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

    /* Toggle Switch Styling */
    .dot {
        transition: transform 0.3s ease-in-out;
    }

    input:checked~.dot {
        transform: translateX(100%);
    }

    input:checked~.block {
        background-color: #10B981;
    }

    /* Custom scrollbar for selection container */
    #tujuan-selection-container::-webkit-scrollbar {
        width: 6px;
    }

    #tujuan-selection-container::-webkit-scrollbar-track {
        background-color: #f1f1f1;
        border-radius: 10px;
    }

    #tujuan-selection-container::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 10px;
    }

    #tujuan-selection-container::-webkit-scrollbar-thumb:hover {
        background-color: #9ca3af;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;

        /* Card hover effects */
        .hover-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }
    }

    /* Input focus styles */
    .focus-within\:ring:focus-within {
        --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
        --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
        box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
    }

    /* User selection item hover effect */
    .user-selection-item {
        transition: all 0.2s ease;
        background-color: unset !important;
    }

    .user-selection-item:hover {
        background-color: #f3f4f6;
    }

    .user-selection-item input[type="checkbox"]:checked ~ label,
    .user-selection-item input[type="checkbox"]:checked {
        background-color: unset !important;
        color: unset !important;
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

    #perusahaan-suggestions div {
        padding: 8px 12px;
        cursor: pointer;
        white-space: nowrap;
    }

    #perusahaan-suggestions div:hover {
        background-color: #f3f4f6;
    }

    .perusahaan-container {
        position: relative;
        z-index: 50;
    }

    .suggestions-wrapper {
        position: relative;
    }

    /* Pastikan parent container cukup tinggi */
    .space-y-6 > div {
        min-height: 100px; /* Tambahkan minimum height */
        position: relative;
    }

    /* Override untuk container form */
    form .p-6.space-y-6 {
        overflow: visible !important;
    }

    /* Override untuk card container */
    .bg-white.rounded-xl.border.border-gray.200.overflow-hidden {
        overflow: visible !important;
    }

    #perusahaan-container { display: none; }
</style>

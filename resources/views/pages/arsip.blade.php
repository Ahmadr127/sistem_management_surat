@extends('home')

@section('title', 'Arsip Surat - SISM Azra')

@section('content')
    <div x-data="arsipApp" class="h-full">
        <!-- Header Section -->
        <div class="mb-6 flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Arsip Surat</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dan pantau semua arsip surat</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Search -->
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Cari Surat</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery"
                            class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200"
                            placeholder="Cari berdasarkan nomor surat atau perihal...">
                        <i class="ri-search-line absolute left-4 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filter Tanggal Dari -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Tanggal Mulai</label>
                    <input type="date" x-model="startDate"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                </div>
                
                <!-- Filter Tanggal Sampai -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Tanggal Akhir</label>
                    <input type="date" x-model="endDate"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Filter Perusahaan -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Perusahaan</label>
                    <select x-model="filterPerusahaan"
                        class="w-full py-2.5 pl-4 pr-8 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white appearance-none transition-all duration-200 cursor-pointer">
                        <option value="">Semua Perusahaan</option>
                        @foreach($perusahaans as $perusahaan)
                            <option value="{{ $perusahaan->kode }}">{{ $perusahaan->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Jabatan -->
                <div x-data="{ 
                    open: false, 
                    searchTerm: '', 
                    selectedJabatan: '',
                    filteredJabatans: [],
                    allJabatans: {{ Js::from($jabatanList->pluck('nama_jabatan')) }},
                    
                    init() {
                        this.filteredJabatans = this.allJabatans;
                        this.$watch('searchTerm', (value) => {
                            if (value === '') {
                                this.filteredJabatans = this.allJabatans;
                            } else {
                                this.filteredJabatans = this.allJabatans.filter(
                                    jabatan => jabatan.toLowerCase().includes(value.toLowerCase())
                                );
                            }
                        });
                        
                        this.$watch('selectedJabatan', (value) => {
                            this.filterJabatan = value;
                        });
                    },
                    
                    selectJabatan(jabatan) {
                        this.selectedJabatan = jabatan;
                        this.open = false;
                        this.searchTerm = '';
                    },
                    
                    clearSelection() {
                        this.selectedJabatan = '';
                        this.filterJabatan = '';
                        this.searchTerm = '';
                    }
                }" class="relative">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Jabatan/Unit</label>
                    
                    <!-- Dropdown Trigger -->
                    <div @click="open = !open" 
                        class="flex items-center justify-between w-full py-2.5 pl-4 pr-2 text-sm text-gray-700 bg-gray-50 rounded-lg focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 focus-within:bg-white transition-all duration-200 cursor-pointer">
                        
                        <div class="flex-grow flex items-center space-x-2">
                            <span x-show="!selectedJabatan" class="text-gray-500">Semua Jabatan</span>
                            <div x-show="selectedJabatan" class="flex items-center space-x-1">
                                <span x-text="selectedJabatan" class="text-gray-800"></span>
                                <button @click.stop="clearSelection()" class="text-gray-400 hover:text-gray-600">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-gray-400">
                            <i class="ri-arrow-down-s-line" x-show="!open"></i>
                            <i class="ri-arrow-up-s-line" x-show="open"></i>
                        </div>
                    </div>
                    
                    <!-- Dropdown Content -->
                    <div x-show="open" 
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="absolute z-40 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200">
                        
                        <!-- Search Box -->
                        <div class="p-2 border-b">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="searchTerm"
                                    placeholder="Cari jabatan..."
                                    class="w-full pl-8 pr-4 py-2 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200"
                                >
                                <i class="ri-search-line absolute left-3 top-2.5 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <!-- Dropdown Options -->
                        <div class="max-h-56 overflow-y-auto p-1">
                            <!-- All option -->
                            <div @click="selectJabatan('')"
                                class="px-3 py-2 hover:bg-gray-100 rounded cursor-pointer flex items-center text-sm">
                                <span class="mr-2"><i class="ri-apps-line"></i></span>
                                <span>Semua Jabatan</span>
                            </div>
                            
                            <!-- No results message -->
                            <div x-show="filteredJabatans.length === 0" class="px-3 py-2 text-sm text-gray-500 italic">
                                Tidak ada hasil yang ditemukan
                            </div>
                            
                            <!-- Jabatan options -->
                            <template x-for="jabatan in filteredJabatans" :key="jabatan">
                                <div @click="selectJabatan(jabatan)"
                                    :class="{ 'bg-green-50 text-green-800': selectedJabatan === jabatan }"
                                    class="px-3 py-2 hover:bg-gray-100 rounded cursor-pointer flex items-center text-sm">
                                    <span class="mr-2">
                                        <i class="ri-building-line" x-show="selectedJabatan !== jabatan"></i>
                                        <i class="ri-check-line text-green-600" x-show="selectedJabatan === jabatan"></i>
                                    </span>
                                    <span x-text="jabatan"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Filter Status Disposisi -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Status Disposisi</label>
                    <select x-model="filterStatus"
                        class="w-full py-2.5 pl-4 pr-8 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white appearance-none transition-all duration-200 cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved" selected>Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="text-left text-xs font-semibold text-white uppercase tracking-wider bg-green-600 border-b border-gray-200">
                            <th class="px-6 py-3.5">No. Surat</th>
                            <th class="px-6 py-3.5">Tanggal</th>
                            <th class="px-6 py-3.5">Perihal</th>
                            <th class="px-6 py-3.5">Jenis</th>
                            <th class="px-6 py-3.5">Perusahaan</th>
                            <th class="px-6 py-3.5">Pembuat</th>
                            <th class="px-6 py-3.5">Status Disposisi</th>
                            <th class="px-6 py-3.5">Tujuan Disposisi</th>
                            <th class="px-6 py-3.5">File</th>
                            <th class="px-6 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="surat in filteredSurat" :key="surat.id">
                            <tr class="text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium" x-text="surat.nomor_surat"></td>
                                <td class="px-6 py-4" x-text="formatDate(surat.tanggal_surat)"></td>
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="line-clamp-2" x-text="surat.perihal"></p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{ 
                                        'bg-sky-100 text-sky-800': surat.jenis_surat === 'internal',
                                        'bg-fuchsia-100 text-fuchsia-800': surat.jenis_surat === 'eksternal' || surat.jenis_surat === 'external'
                                    }" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        <span x-text="surat.jenis_surat === 'internal' ? 'Internal' : 'Eksternal'"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4" x-text="surat.perusahaan"></td>
                                <td class="px-6 py-4" x-text="surat.creator ? surat.creator.name : '-'"></td>
                                <td class="px-6 py-4">
                                    <span x-show="!surat.disposisi" class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        Belum Disposisi
                                    </span>
                                    
                                    <!-- Show both statuses when disposisi exists -->
                                    <div x-show="surat.disposisi" class="flex flex-col space-y-1">
                                        <!-- Sekretaris Status -->
                                        <div class="flex items-center">
                                            <span class="text-xs font-medium text-gray-600 mr-1">Sekretaris:</span>
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-800': surat.disposisi?.status_sekretaris === 'pending',
                                                'bg-blue-100 text-blue-800': surat.disposisi?.status_sekretaris === 'review',
                                                'bg-green-100 text-green-800': surat.disposisi?.status_sekretaris === 'approved',
                                                'bg-red-100 text-red-800': surat.disposisi?.status_sekretaris === 'rejected'
                                            }"
                                            class="px-2 py-0.5 text-xs font-medium rounded-full"
                                            x-text="surat.disposisi?.status_sekretaris">
                                            </span>
                                        </div>
                                        
                                        <!-- Dirut Status -->
                                        <div class="flex items-center">
                                            <span class="text-xs font-medium text-gray-600 mr-1">Dirut:</span>
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-800': surat.disposisi?.status_dirut === 'pending',
                                                'bg-blue-100 text-blue-800': surat.disposisi?.status_dirut === 'review',
                                                'bg-green-100 text-green-800': surat.disposisi?.status_dirut === 'approved',
                                                'bg-red-100 text-red-800': surat.disposisi?.status_dirut === 'rejected'
                                            }"
                                            class="px-2 py-0.5 text-xs font-medium rounded-full"
                                            x-text="surat.disposisi?.status_dirut">
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Tujuan Disposisi Column -->
                                    <div x-show="surat.disposisi && surat.disposisi.tujuan" class="flex flex-wrap gap-1">
                                        <template x-for="(tujuan, idx) in surat.disposisi?.tujuan" :key="idx">
                                            <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full" x-text="tujuan.name"></span>
                                        </template>
                                    </div>
                                    <span x-show="!surat.disposisi || !surat.disposisi.tujuan" class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- File Handling - Support multiple files -->
                                    <template x-if="surat.files && surat.files.length > 0">
                                        <div class="space-y-1">
                                            <template x-for="(file, idx) in surat.files" :key="idx">
                                                <div class="flex items-center space-x-1">
                                                    <template x-if="file.file_path.endsWith('.pdf')">
                                                        <i class="ri-file-pdf-line text-red-500 mr-1"></i>
                                                    </template>
                                                    <template x-if="file.file_path.endsWith('.doc') || file.file_path.endsWith('.docx')">
                                                        <i class="ri-file-word-line text-blue-500 mr-1"></i>
                                                    </template>
                                                    <template x-if="file.file_path.endsWith('.jpg') || file.file_path.endsWith('.jpeg') || file.file_path.endsWith('.png')">
                                                        <i class="ri-image-line text-green-500 mr-1"></i>
                                                    </template>
                                                    <a :href="file.file_path.startsWith('/') ? file.file_path : '/' + file.file_path" 
                                                       target="_blank"
                                                       class="text-xs text-blue-600 hover:text-blue-800 hover:underline truncate max-w-[120px]" 
                                                       x-text="file.original_name || file.file_path.split('/').pop()">
                                                    </a>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <!-- Original file path handling for backward compatibility -->
                                    <template x-if="(!surat.files || surat.files.length === 0) && surat.file_path">
                                        <a :href="surat.file_path.startsWith('/') ? surat.file_path : '/' + surat.file_path" target="_blank"
                                            class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                            <i class="ri-download-line mr-1"></i> 
                                            <span x-text="surat.file_path.split('/').pop()"></span>
                                        </a>
                                    </template>
                                    <template x-if="(!surat.files || surat.files.length === 0) && !surat.file_path">
                                        <span class="text-gray-400">Tidak ada file</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <!-- Disposisi button - only visible if no disposisi yet and user can create disposisi -->
                                        <template x-if="!surat.disposisi && ({{ auth()->user()->role }} == 0 || {{ auth()->user()->role }} == 3)">
                                            <a :href="'/disposisi/create?surat_id=' + surat.id" class="text-green-600 hover:text-green-800">
                                                <i class="ri-mail-send-line mr-1"></i> Disposisi
                                            </a>
                                        </template>
                                        
                                        <!-- Preview button - show based on first file or original file_path -->
                                        <template x-if="(surat.files && surat.files.length > 0)">
                                            <a :href="surat.files[0].file_path.startsWith('/') ? surat.files[0].file_path : '/' + surat.files[0].file_path" 
                                               target="_blank" 
                                               class="text-gray-600 hover:text-gray-800">
                                                <i class="ri-file-search-line mr-1"></i> Preview
                                            </a>
                                        </template>
                                        <template x-if="(!surat.files || surat.files.length === 0) && surat.file_path">
                                            <a :href="surat.file_path.startsWith('/') ? surat.file_path : '/' + surat.file_path" 
                                               target="_blank" 
                                               class="text-gray-600 hover:text-gray-800">
                                                <i class="ri-file-search-line mr-1"></i> Preview
                                            </a>
                                        </template>
                                        <template x-if="(!surat.files || surat.files.length === 0) && !surat.file_path">
                                            <span class="text-gray-400">
                                                <i class="ri-file-search-line mr-1"></i> No Preview
                                            </span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div class="p-8 text-center" x-show="filteredSurat.length === 0">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="ri-inbox-line text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada arsip surat yang ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="updateSurat">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Surat</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nomor Surat
                                    </label>
                                    <input type="text" x-model="editingData.nomor_surat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Tanggal Surat
                                    </label>
                                    <input type="date" x-model="editingData.tanggal_surat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Perihal
                                    </label>
                                    <textarea x-model="editingData.perihal"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                        rows="3"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Perusahaan
                                    </label>
                                    <select x-model="editingData.perusahaan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="RSAZRA">RS AZRA</option>
                                        <option value="ASP">ASP</option>
                                        <option value="DIRUT">DIRUT</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jenis Surat
                                    </label>
                                    <select x-model="editingData.jenis_surat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="internal">Internal</option>
                                        <option value="external">External</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Sifat Surat
                                    </label>
                                    <select x-model="editingData.sifat_surat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="normal">Normal</option>
                                        <option value="rahasia">Rahasia</option>
                                        <option value="penting">Penting</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        File Surat
                                    </label>
                                    <input type="file" id="edit-file-upload" name="file"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, JPEG, PNG. Maks. 10MB</p>
                                </div>

                                <div x-show="editingData.files && editingData.files.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        File Saat Ini
                                    </label>
                                    <ul class="text-sm text-gray-600">
                                        <template x-for="file in editingData.files" :key="file.id">
                                            <li class="flex items-center space-x-2 py-1">
                                                <i class="ri-file-line text-gray-500"></i>
                                                <span x-text="file.original_name || file.file_path.split('/').pop()"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" @click="closeEditModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('arsipApp', () => ({
                suratList: {{ Js::from($suratKeluar) }},
                searchQuery: '',
                filterPerusahaan: '',
                filterJabatan: '',
                filterStatus: '',
                startDate: '',
                endDate: '',
                showEditModal: false,
                editingData: {
                    id: null,
                    nomor_surat: '',
                    tanggal_surat: '',
                    perihal: '',
                    perusahaan: '',
                    jenis_surat: '',
                    sifat_surat: ''
                },

                init() {
                    // Set default date range to start of current month to today
                    const today = new Date();
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    this.startDate = firstDay.toISOString().slice(0, 10);
                    this.endDate = today.toISOString().slice(0, 10);
                    
                    // Set approved as the default filter
                    this.filterStatus = 'approved';
                },

                openEditModal(surat) {
                    this.editingData = { ...surat };
                    this.showEditModal = true;
                },

                closeEditModal() {
                    this.showEditModal = false;
                    this.editingData = {
                        id: null,
                        nomor_surat: '',
                        tanggal_surat: '',
                        perihal: '',
                        perusahaan: '',
                        jenis_surat: '',
                        sifat_surat: ''
                    };
                },

                async updateSurat() {
                    try {
                        const formData = new FormData();
                        
                        // Menambahkan semua field data dari editingData
                        Object.keys(this.editingData).forEach(key => {
                            if (key !== 'files') { // Skip the files array
                                formData.append(key, this.editingData[key]);
                            }
                        });
                        
                        // Menambahkan file jika ada
                        const fileInput = document.getElementById('edit-file-upload');
                        if (fileInput.files.length > 0) {
                            formData.append('file', fileInput.files[0]);
                        }
                        
                        const response = await fetch(`/api/surat-keluar/${this.editingData.id}`, {
                            method: 'POST', // Menggunakan POST untuk FormData
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-HTTP-Method-Override': 'PUT' // Simulasi method PUT
                            },
                            body: formData
                        });

                        if (!response.ok) throw new Error('Gagal mengupdate data');

                        const result = await response.json();

                        // Update data di list jika sukses
                        if (result.success) {
                            const updatedSurat = result.data;
                            
                            // Update data di list
                            const index = this.suratList.findIndex(s => s.id === updatedSurat.id);
                            if (index !== -1) {
                                this.suratList[index] = updatedSurat;
                            }

                            this.closeEditModal();

                            // Tampilkan notifikasi sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data surat berhasil diperbarui',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            throw new Error(result.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengupdate data',
                        });
                    }
                },

                get filteredSurat() {
                    return this.suratList.filter(surat => {
                        // Text search
                        const matchQuery = 
                            this.searchQuery === '' || 
                            surat.nomor_surat.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            surat.perihal.toLowerCase().includes(this.searchQuery.toLowerCase());
                        
                        // Perusahaan filter
                        const matchPerusahaan = 
                            this.filterPerusahaan === '' ||
                            surat.perusahaan === this.filterPerusahaan;
                        
                        // Jabatan filter
                        const matchJabatan = 
                            this.filterJabatan === '' ||
                            (surat.creator && surat.creator.jabatan && 
                             surat.creator.jabatan.nama_jabatan === this.filterJabatan);
                        
                        // Status filter - updated logic
                        let matchStatus = true;
                        if (this.filterStatus) {
                            // Only include surat that have disposisi with matching status
                            matchStatus = surat.disposisi && 
                                (surat.disposisi.status_sekretaris === this.filterStatus || 
                                 surat.disposisi.status_dirut === this.filterStatus);
                        }
                        
                        // Date range filter
                        let matchDates = true;
                        if (this.startDate && this.endDate) {
                            const suratDate = new Date(surat.tanggal_surat);
                            const startDate = new Date(this.startDate);
                            const endDate = new Date(this.endDate);
                            // Set time to end of day for end date
                            endDate.setHours(23, 59, 59, 999);
                            
                            matchDates = suratDate >= startDate && suratDate <= endDate;
                        }

                        return matchQuery && matchPerusahaan && matchJabatan && matchStatus && matchDates;
                    });
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }
            }));
        });
    </script>
@endpush

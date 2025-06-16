@extends('home')

@section('title', 'Laporan - SISM Azra')

@section('content')
    <div x-data="laporanApp" class="h-full">
        <!-- Header Section -->
        <div class="mb-6 flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Laporan</h1>
                <p class="text-sm text-gray-500 mt-1">Buat dan unduh laporan surat dan disposisi</p>
            </div>
            <div class="flex space-x-3">
                <button @click="exportExcel" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="ri-file-excel-line mr-2"></i> Export Excel
                </button>
                <button @click="exportPDF" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="ri-file-pdf-line mr-2"></i> Export PDF
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Period Type -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Periode Laporan</label>
                    <select x-model="periodType" @change="updateDateRange"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="custom">Kustom</option>
                        <option value="weekly">Mingguan (Minggu Ini)</option>
                        <option value="monthly">Bulanan (Bulan Ini)</option>
                    </select>
                </div>

                <!-- Filter Perusahaan -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Perusahaan</label>
                    <select x-model="filterPerusahaan"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="">Semua Perusahaan</option>
                        @foreach($perusahaans as $perusahaan)
                            <option value="{{ $perusahaan->kode }}">{{ $perusahaan->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Status Disposisi</label>
                    <select x-model="statusDisposisi"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="review">Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                <!-- Filter Jenis Surat -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Jenis Surat</label>
                    <select x-model="filterJenis"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="">Semua Jenis</option>
                        <option value="internal">Internal</option>
                        <option value="eksternal">Eksternal</option>
                    </select>
                </div>
                
                <!-- Date Range Picker -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Tanggal Mulai</label>
                    <input type="date" x-model="startDate" :disabled="periodType !== 'custom'"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Tanggal Akhir</label>
                    <input type="date" x-model="endDate" :disabled="periodType !== 'custom'"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                </div>

                <!-- Filter Jabatan/Unit with Search -->
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
            </div>

            <div class="flex justify-end mt-6">
                <button @click="generateReport"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="ri-filter-line mr-2"></i> Tampilkan Laporan
                </button>
            </div>
        </div>

        <!-- Table Section for Surat  -->
        <div x-show="showReport" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-white uppercase tracking-wider bg-green-600 border-b border-gray-200">
                            <th class="px-6 py-3.5">No.</th>
                            <th class="px-6 py-3.5">No. Disposisi</th>
                            <th class="px-6 py-3.5">Tanggal Disposisi</th>
                            <th class="px-6 py-3.5">No. Surat</th>
                            <th class="px-6 py-3.5">Tanggal</th>
                            <th class="px-6 py-3.5">Perihal</th>
                            <th class="px-6 py-3.5">Jenis</th>
                            <th class="px-6 py-3.5">Perusahaan</th>
                            <th class="px-6 py-3.5">Pembuat</th>
                            <th class="px-6 py-3.5">Tujuan Disposisi</th>
                            <th class="px-6 py-3.5">Status Direktur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(item, index) in filteredData" :key="item.id">
                            <tr class="text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4" x-text="index + 1"></td>
                                <td class="px-6 py-4 font-medium" x-text="item.disposisi ? item.disposisi.id : '-'"></td>
                                <td class="px-6 py-4">
                                    <template x-if="item.disposisi && item.disposisi.waktu_review_dirut">
                                        <span x-text="formatDate(item.disposisi.waktu_review_dirut)"></span>
                                    </template>
                                    <template x-if="!item.disposisi || !item.disposisi.waktu_review_dirut">
                                        <span>-</span>
                                    </template>
                                </td>
                                <td class="px-6 py-4 font-medium" x-text="item.nomor_surat"></td>
                                <td class="px-6 py-4" x-text="formatDate(item.tanggal_surat)"></td>
                                <td class="px-6 py-4 max-w-xs">
                                    <p class="line-clamp-2" x-text="item.perihal"></p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{ 
                                        'bg-sky-100 text-sky-800': item.jenis_surat === 'internal',
                                        'bg-fuchsia-100 text-fuchsia-800': item.jenis_surat === 'eksternal' || item.jenis_surat === 'external'
                                    }" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        <span x-text="item.jenis_surat === 'internal' ? 'Internal' : 'Eksternal'"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4" x-text="item.perusahaan"></td>
                                <td class="px-6 py-4" x-text="item.creator ? item.creator.name : '-'"></td>
                                <td class="px-6 py-4">
                                    <div x-show="item.disposisi && item.disposisi.tujuan" class="flex flex-wrap gap-1">
                                        <template x-for="(tujuan, idx) in item.disposisi?.tujuan" :key="idx">
                                            <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full" x-text="tujuan.name"></span>
                                        </template>
                                    </div>
                                    <span x-show="!item.disposisi || !item.disposisi.tujuan" class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span x-show="item.disposisi" 
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': item.disposisi?.status_dirut === 'pending',
                                            'bg-blue-100 text-blue-800': item.disposisi?.status_dirut === 'review',
                                            'bg-green-100 text-green-800': item.disposisi?.status_dirut === 'approved',
                                            'bg-red-100 text-red-800': item.disposisi?.status_dirut === 'rejected'
                                        }"
                                        class="px-2 py-1 text-xs font-medium rounded-full"
                                        x-text="item.disposisi?.status_dirut">
                                    </span>
                                    <span x-show="!item.disposisi" class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        Belum Disposisi
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div class="p-8 text-center" x-show="filteredData.length === 0 && showReport">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="ri-inbox-line text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada data yang ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="isLoading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="inline-flex items-center justify-center">
                <svg class="animate-spin h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="text-gray-500 font-medium mt-2">Memuat data...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('laporanApp', () => ({
                periodType: 'custom',
                startDate: new Date().toISOString().slice(0, 10),
                endDate: new Date().toISOString().slice(0, 10),
                filterPerusahaan: '',
                filterJabatan: '',
                statusDisposisi: '',
                filterJenis: '',
                reportData: [],
                filteredData: [],
                showReport: false,
                isLoading: false,
                userRole: '{{ auth()->user()->role }}',
                availableJabatan: [],

                init() {
                    this.updateDateRange();
                },

                get filteredData() {
                    if (!this.reportData || !Array.isArray(this.reportData)) {
                        return [];
                    }
                    
                    return this.reportData.filter(item => {
                        // Apply any client-side filters here if needed
                        return true;
                    });
                },

                async generateReport() {
                    this.isLoading = true;
                    this.showReport = true;
                    
                    try {
                        const params = new URLSearchParams();
                        params.append('jenis', 'surat_keluar');
                        params.append('start_date', this.startDate);
                        params.append('end_date', this.endDate);
                        params.append('period_type', this.periodType);
                        params.append('with_timestamps', 'true');
                        params.append('use_only_waktu_review_dirut', 'true');
                        params.append('disposisi_date_label', 'Tanggal Disposisi');
                        params.append('use_dirut_status', 'true');
                        
                        if (this.filterPerusahaan) {
                            params.append('perusahaan', this.filterPerusahaan);
                        }
                        
                        if (this.filterJabatan) {
                            params.append('jabatan', this.filterJabatan);
                        }
                        
                        if (this.statusDisposisi) {
                            params.append('status', this.statusDisposisi);
                            params.append('status_field', 'status_dirut');
                        }
                        
                        if (this.filterJenis) {
                            params.append('jenis_surat', this.filterJenis);
                        }
                        
                        const response = await fetch(`/api/laporan?${params.toString()}`);
                        
                        if (!response.ok) {
                            throw new Error('Gagal memuat data laporan');
                        }
                        
                        const responseData = await response.json();
                        this.reportData = responseData.data || [];
                        console.log('Report data received:', this.reportData);
                        
                        // Check for waktu_review_dirut in disposisi objects
                        if (this.reportData && this.reportData.length > 0) {
                            this.reportData.forEach((item, index) => {
                                if (item.disposisi) {
                                    console.log(`Disposisi ${index} (ID: ${item.disposisi.id}):`, {
                                        waktu_review_dirut: item.disposisi.waktu_review_dirut,
                                        created_at: item.disposisi.created_at
                                    });
                                }
                            });
                        }
                        
                        // Extract available jabatan from the data
                        this.calculateAvailableJabatan();
                        
                    } catch (error) {
                        console.error('Error loading report:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memuat data laporan'
                        });
                        this.reportData = [];
                    } finally {
                        this.isLoading = false;
                    }
                },

                calculateAvailableJabatan() {
                    if (!this.reportData || !Array.isArray(this.reportData)) {
                        this.availableJabatan = [];
                        return;
                    }
                    
                    // Extract all unique jabatan from creator data
                    const jabatan = this.reportData
                        .filter(item => {
                            // For different report types, we need to access the creator differently
                            if (item.creator && item.creator.jabatan) {
                                return true;
                            }
                            return false;
                        })
                        .map(item => {
                            return item.creator.jabatan.nama_jabatan;
                        })
                        .filter(Boolean); // Remove any undefined values
                    
                    this.availableJabatan = [...new Set(jabatan)].sort();
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    
                    return new Date(dateString).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                exportExcel() {
                    const params = new URLSearchParams();
                    params.append('format', 'excel');
                    params.append('jenis', 'surat_keluar');
                    params.append('start_date', this.startDate);
                    params.append('end_date', this.endDate);
                    params.append('period_type', this.periodType);
                    params.append('include_disposisi_details', 'true');
                    params.append('disposisi_date_field', 'waktu_review_dirut');
                    params.append('with_timestamps', 'true');
                    params.append('use_only_waktu_review_dirut', 'true');
                    params.append('disposisi_date_label', 'Tanggal Disposisi');
                    params.append('use_dirut_status', 'true');
                    
                    if (this.filterPerusahaan) {
                        params.append('perusahaan', this.filterPerusahaan);
                    }
                    
                    if (this.filterJabatan) {
                        params.append('jabatan', this.filterJabatan);
                    }
                    
                    if (this.statusDisposisi) {
                        params.append('status', this.statusDisposisi);
                    }
                    
                    if (this.filterJenis) {
                        params.append('jenis_surat', this.filterJenis);
                    }
                    
                    window.location.href = `/export-laporan?${params.toString()}`;
                },

                exportPDF() {
                    const params = new URLSearchParams();
                    params.append('format', 'pdf');
                    params.append('jenis', 'surat_keluar');
                    params.append('start_date', this.startDate);
                    params.append('end_date', this.endDate);
                    params.append('period_type', this.periodType);
                    params.append('include_disposisi_details', 'true');
                    params.append('disposisi_date_field', 'waktu_review_dirut');
                    params.append('with_timestamps', 'true');
                    params.append('use_only_waktu_review_dirut', 'true');
                    params.append('disposisi_date_label', 'Tanggal Disposisi');
                    params.append('use_dirut_status', 'true');
                    
                    if (this.filterPerusahaan) {
                        params.append('perusahaan', this.filterPerusahaan);
                    }
                    
                    if (this.filterJabatan) {
                        params.append('jabatan', this.filterJabatan);
                    }
                    
                    if (this.statusDisposisi) {
                        params.append('status', this.statusDisposisi);
                    }
                    
                    if (this.filterJenis) {
                        params.append('jenis_surat', this.filterJenis);
                    }
                    
                    window.location.href = `/export-laporan?${params.toString()}`;
                },

                updateDateRange() {
                    const now = new Date();
                    
                    if (this.periodType === 'weekly') {
                        // Set to current week (Sunday to Saturday)
                        const firstDay = new Date(now);
                        firstDay.setDate(now.getDate() - now.getDay()); // Sunday
                        
                        const lastDay = new Date(now);
                        lastDay.setDate(now.getDate() + (6 - now.getDay())); // Saturday
                        
                        this.startDate = firstDay.toISOString().slice(0, 10);
                        this.endDate = lastDay.toISOString().slice(0, 10);
                    } else if (this.periodType === 'monthly') {
                        // Set to current month
                        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        
                        this.startDate = firstDay.toISOString().slice(0, 10);
                        this.endDate = lastDay.toISOString().slice(0, 10);
                    }
                    // For 'custom', we don't update dates as user will input them
                }
            }));
        });
    </script>
@endpush

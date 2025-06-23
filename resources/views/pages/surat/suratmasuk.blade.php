@extends('home')

@section('title', 'Surat Masuk - SISM Azra')

@section('content')
    <!-- Tambahkan jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Surat Masuk</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola data surat masuk</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" id="search"
                        class="w-full sm:w-64 px-4 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                        placeholder="Cari surat masuk...">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <i class="ri-search-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-md">
            <!-- Filters -->
            <div class="p-5 border-b border-gray-200">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filter Tanggal -->
                    <div class="flex items-center bg-gray-50 rounded-lg p-1">
                        <input type="date" id="start_date"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <span class="text-gray-500 px-2">s/d</span>
                        <input type="date" id="end_date"
                            class="px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                    </div>

                    <!-- Filter Jenis Surat -->
                    <select id="jenis_surat"
                        class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <option value="">Semua Jenis</option>
                        <option value="internal">Internal</option>
                        <option value="eksternal">Eksternal</option>
                    </select>

                    <!-- Filter Sifat Surat -->
                    <select id="sifat_surat"
                        class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <option value="">Semua Sifat</option>
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                    </select>

                    <!-- Filter Status Sekretaris -->
                    <select id="filter_status_sekretaris"
                        class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <option value="">Semua Status Sekretaris</option>
                        <option value="pending">Menunggu</option>
                        <option value="review">Sedang Ditinjau</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>

                    <!-- Filter Status Dirut -->
                    <select id="filter_status_dirut"
                        class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <option value="">Semua Status Direktur</option>
                        <option value="pending">Menunggu</option>
                        <option value="review">Sedang Ditinjau</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>

                    <!-- Filter Surat Saya -->
                    <select id="filter_surat_saya"
                        class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <option value="">Semua Surat</option>
                        <option value="dari_saya">Dari Saya</option>
                        <option value="bukan_saya">Bukan Dari Saya</option>
                    </select>

                    <!-- Tombol Filter -->
                    <button id="btn-filter"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                        <i class="ri-filter-line mr-1.5"></i>
                        Filter
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="p-4">
                <div class="overflow-x-auto relative rounded-lg shadow-sm border border-gray-100 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    <table class="min-w-full divide-y divide-gray-200" id="suratMasukTable">
                        <thead class="bg-green-600">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    No. Disposisi
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Tanggal Surat
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    No. Surat
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Nama Perusahaan
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Perihal
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Jenis
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Pengirim
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Status Sekretaris
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Status Direktur
                                </th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider whitespace-nowrap">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody id="surat-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Data akan diisi melalui JavaScript -->
                        </tbody>
                    </table>
                    <!-- Scroll indicator visible only on small screens -->
                    <div class="md:hidden absolute bottom-0 right-6 bg-gradient-to-l from-white via-white to-transparent px-4 py-1 text-xs text-gray-500 flex items-center pointer-events-none">
                        <span>Geser <i class="ri-arrow-right-line ml-1"></i></span>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <div id="pagination-container" class="w-full flex justify-center"></div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Surat -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm"></div>
            </div>

            <!-- Modal Panel -->
            <div id="detail-modal-panel"
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                            Detail Surat Masuk
                        </h3>
                        <button type="button" id="close-detail-modal" class="text-gray-400 hover:text-gray-500">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-4 max-h-[calc(90vh-120px)] overflow-y-auto">
                    <!-- Informasi Utama -->
                    <div class="space-y-6">
                        <!-- Grid Informasi Dasar -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Nomor Surat</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-nomor"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Tanggal Surat</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-tanggal"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">No. Disposisi</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-disposisi-id">-</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Tanggal Disposisi</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-disposisi-tanggal">-</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Jenis Surat</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-jenis"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Perusahaan</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-perusahaan">-</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-500">Sifat Surat</p>
                                <p class="text-base font-semibold text-gray-900" id="detail-sifat"></p>
                            </div>
                        </div>

                        <!-- Perihal -->
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-gray-500">Perihal</p>
                            <p class="text-base text-gray-900" id="detail-perihal"></p>
                        </div>

                        <!-- Pengirim -->
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-gray-500">Pengirim</p>
                            <p class="text-base font-semibold text-gray-900" id="detail-pengirim"></p>
                        </div>

                        <!-- Status Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-500">Status Sekretaris</p>
                                    <div id="detail-status-sekretaris"></div>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Keterangan Sekretaris</p>
                                    <p class="text-sm text-gray-700" id="detail-keterangan-sekretaris">-</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-500">Status Direktur</p>
                                    <div id="detail-status-dirut"></div>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Keterangan Direktur</p>
                                    <p class="text-sm text-gray-700" id="detail-keterangan-dirut">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tujuan Disposisi -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <p class="text-sm font-medium text-gray-500">Tujuan Disposisi</p>
                            <div id="detail-tujuan-disposisi" class="text-sm text-gray-700">
                                <p>Belum ada tujuan disposisi</p>
                            </div>
                        </div>

                        <!-- Keterangan Pengirim -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <p class="text-sm font-medium text-gray-500">Keterangan Pengirim</p>
                            <p class="text-sm text-gray-700" id="detail-keterangan-pengirim">-</p>
                        </div>

                        <!-- File Attachment -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-500">File Surat</p>
                                <div class="flex space-x-2">
                                    <a id="detail-preview-link" href="#" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 transition-colors duration-200">
                                        <i class="ri-file-search-line mr-1.5"></i> Preview
                                    </a>
                                    <a href="#" id="detail-file-link" class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                                        <i class="ri-download-line mr-1.5"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                            <!-- Container untuk multiple file -->
                            <div id="detail-files-list" class="space-y-2 mt-2"></div>
                            <span id="no-file-text" class="text-gray-500 text-sm">Tidak ada file</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <button type="button"
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:w-auto"
                        onclick="document.getElementById('detail-modal').classList.add('hidden')">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Disposisi -->
    <div id="edit-disposisi-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm"></div>
            </div>

            <!-- Modal Panel -->
            <div
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="disposisi-modal-title">
                            Edit Disposisi Surat
                        </h3>
                        <button type="button" id="close-disposisi-modal" class="text-gray-400 hover:text-gray-500">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Content -->
                <form id="edit-disposisi-form">
                    <input type="hidden" id="edit-disposisi-surat-id" name="surat_id">
                    <input type="hidden" id="edit-disposisi-id" name="disposisi_id">

                    <div class="px-6 py-4">
                        <!-- Loading State -->
                        <div id="disposisi-loading" class="py-10 flex flex-col items-center justify-center">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-green-500"></div>
                            <p class="mt-4 text-gray-500">Memuat data disposisi...</p>
                        </div>

                        <div id="disposisi-form-content" class="hidden space-y-6">
                            <!-- Bagian Sekretaris -->
                            <div id="sekretaris-section" class="space-y-4 border-b border-gray-200 pb-6 hidden">
                                <h4 class="font-medium text-gray-900">Disposisi Sekretaris</h4>

                                <div>
                                    <label for="status_sekretaris"
                                        class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="status_sekretaris" name="status_sekretaris"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                        <option value="pending">Menunggu</option>
                                        <option value="review">Sedang Ditinjau</option>
                                        <option value="approved">Disetujui</option>
                                        <option value="rejected">Ditolak</option>
                                    </select>
                                </div>

                                <!-- Menghilangkan input keterangan dan menggantinya dengan hidden input -->
                                <input type="hidden" id="keterangan_sekretaris" name="keterangan_sekretaris"
                                    value="-">
                            </div>

                            <!-- Bagian Direktur -->
                            <div id="direktur-section" class="space-y-4 hidden">
                                <h4 class="font-medium text-gray-900">Disposisi Direktur</h4>

                                <div>
                                    <label for="status_dirut"
                                        class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="status_dirut" name="status_dirut"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                                        <option value="pending">Menunggu</option>
                                        <option value="review">Sedang Ditinjau</option>
                                        <option value="approved">Disetujui</option>
                                        <option value="rejected">Ditolak</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="keterangan_dirut"
                                        class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea id="keterangan_dirut" name="keterangan_dirut" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"></textarea>
                                </div>

                                <!-- Tujuan Disposisi -->
                                <div id="tujuan-disposisi-section" class="hidden">
                                    <h4 class="font-medium text-gray-900 mb-2">Tujuan Disposisi</h4>
                                    <div class="mb-2">
                                        <div class="relative">
                                            <input type="text" id="search-tujuan"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                                                placeholder="Cari nama atau jabatan...">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                                <i class="ri-search-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between mb-2">
                                        <button type="button" id="select-all-tujuan"
                                            class="px-2 py-1 text-xs bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-all">
                                            Pilih Semua
                                        </button>
                                        <button type="button" id="clear-all-tujuan"
                                            class="px-2 py-1 text-xs bg-gray-50 text-gray-600 rounded hover:bg-gray-100 transition-all">
                                            Hapus Semua
                                        </button>
                                    </div>
                                    <div id="selected-tujuan-count" class="text-xs text-gray-500 mb-2">
                                        0 tujuan dipilih
                                    </div>
                                    <div id="tujuan-disposisi-container" 
                                        class="space-y-2 max-h-[200px] overflow-y-auto p-3 border border-gray-200 rounded-md bg-white shadow-inner scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                                        <!-- Checkbox users akan dirender dinamis -->
                                        <div class="py-4 text-center text-gray-500">Memuat daftar tujuan...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            id="cancel-disposisi-button">
                            Batal
                        </button>
                        <button type="button"
                            class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            id="save-disposisi-button">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk mengelola data dan interaksi -->
    <script>
        // Definisikan suratData sebagai variabel global
        let suratData = [];
        let userRole = {{ auth()->check() ? auth()->user()->role : 'null' }};
        let currentSuratId = null; // Track current surat ID for preview

        // Tambahkan variabel global untuk pagination
        let currentPage = 1;
        const perPage = 10;

        // Fungsi helper untuk status yang digunakan secara global
        function getStatusHTML(status) {
            return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(status)}">
                ${getStatusLabel(status)}
            </span>`;
        }

        function getStatusClass(status) {
            switch (status) {
                case 'pending':
                    return 'bg-orange-100 text-orange-800';
                case 'review':
                    return 'bg-indigo-100 text-indigo-800';
                case 'approved':
                    return 'bg-emerald-100 text-emerald-800';
                case 'rejected':
                    return 'bg-rose-100 text-rose-800';
                default:
                    return 'bg-slate-100 text-slate-800';
            }
        }

        function getStatusLabel(status) {
            switch (status) {
                case 'pending':
                    return 'Menunggu';
                case 'review':
                    return 'Sedang Ditinjau';
                case 'approved':
                    return 'Disetujui';
                case 'rejected':
                    return 'Ditolak';
                default:
                    return 'Tidak Ada Status';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            function loadSuratMasuk() {
                console.log('Loading surat masuk...');
                console.log('User Role:', userRole);
                console.log('User ID:', {{ auth()->id() }});

                const tableBody = document.getElementById('surat-table-body');

                // Tampilkan loading state
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                                <p class="mt-2 text-sm text-gray-500">Memuat data surat masuk...</p>
                            </div>
                        </td>
                    </tr>
                `;

                // Buat URL sesuai dengan role
                let url = '/api/surat-masuk';
                const params = new URLSearchParams();

                if (userRole === 0 || userRole === 3) { // Staff atau Admin
                    console.log('Role Staff/Admin: Mengambil data tujuan disposisi dan surat yang dibuat');
                    params.append('user_id', {{ auth()->id() }});
                    params.append('include_created', 'true');
                    params.append('status_dirut', 'approved');
                } else if (userRole === 1) { // Sekretaris
                    console.log('Role Sekretaris: Mengambil semua data');
                    params.append('all', 'true');
                } else if (userRole === 2) { // Direktur
                    console.log('Role Direktur: Mengambil data dengan status sekretaris approved');
                    params.append('status_sekretaris', 'approved');
                }

                url = `${url}?${params.toString()}`;
                console.log('Request URL:', url);

                fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Response not OK:', response.status, response.statusText);
                            return response.json().then(err => {
                                throw new Error(JSON.stringify(err));
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data received:', data);
                        suratData = data; // Simpan data ke variabel global

                        // Filter data berdasarkan role dan kondisi
                        let filteredData = data;
                        if (userRole === 0 || userRole === 3) { // Staff atau Admin
                            console.log('Filtering data for Staff/Admin');
                            filteredData = data.filter(surat => {
                                // Cek apakah user adalah tujuan disposisi
                                const isTujuanDisposisi = surat.disposisi?.tujuan?.some(
                                    tujuan => tujuan.id === {{ auth()->id() }}
                                );

                                // Cek apakah surat dibuat oleh user dan disetujui direktur
                                const isCreatedAndApproved = surat.created_by === {{ auth()->id() }} &&
                                    surat.disposisi?.status_dirut === 'approved';

                                return isTujuanDisposisi || isCreatedAndApproved;
                            });
                        }

                        console.log('Filtered data:', filteredData);

                        // Terapkan filter tambahan
                        const finalFilteredData = filterSurat(filteredData);
                        console.log('Final filtered data:', finalFilteredData);

                        // Render data yang sudah difilter
                        renderTable(finalFilteredData);
                    })
                    .catch(error => {
                        console.error('Error in fetch:', error);
                        tableBody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ri-error-warning-line text-red-500 text-3xl mb-2"></i>
                                    <p class="text-red-500">Gagal memuat data</p>
                                    <p class="text-gray-500 text-sm mt-1">${error.message}</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    });
            }

            function renderTable(data) {
                const tableBody = document.getElementById('surat-table-body');
                const paginationContainer = document.getElementById('pagination-container');

                if (!Array.isArray(data) || data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center py-10">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ri-inbox-line text-gray-400 text-3xl mb-3"></i>
                                    <p class="text-gray-500 font-medium">Belum ada surat masuk</p>
                                    <p class="text-gray-400 text-sm mt-1">Surat masuk akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    paginationContainer.innerHTML = '';
                    return;
                }

                // Pagination logic
                const total = data.length;
                const totalPages = Math.ceil(total / perPage);
                if (currentPage > totalPages) currentPage = totalPages || 1;
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                const pageData = data.slice(start, end);

                let html = '';
                pageData.forEach(surat => {
                    const tanggal = new Date(surat.tanggal_surat).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const pengirim = surat.creator ?
                        `${surat.creator.name}${surat.creator.jabatan ? ` (${surat.creator.jabatan})` : ''}` :
                        (surat.created_by || '-');

                    const statusSekretaris = surat.disposisi ? surat.disposisi.status_sekretaris :
                        'pending';
                    const statusDirut = surat.disposisi ? surat.disposisi.status_dirut : 'pending';
                    const noDisposisi = surat.disposisi ? surat.disposisi.id : '-';

                    html += `
                        <tr class="hover:bg-gray-50 ${surat.created_by == {{ auth()->id() }} ? 'bg-green-50' : ''} ${surat.nomor_surat?.includes('DIRUT') ? 'bg-yellow-50' : ''}">
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${noDisposisi}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${tanggal}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${surat.nomor_surat}
                                ${surat.created_by == {{ auth()->id() }} ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Anda</span>' : ''}
                                ${surat.nomor_surat?.includes('DIRUT') ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><i class="ri-user-star-line mr-1"></i>Dari Direktur</span>' : ''}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${(surat.perusahaanData && surat.perusahaanData.nama_perusahaan) ? surat.perusahaanData.nama_perusahaan : (surat.nama_perusahaan ? surat.nama_perusahaan : (surat.perusahaan ? surat.perusahaan : '-'))}
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-500 max-w-xs truncate ${surat.nomor_surat?.includes('DIRUT') ? 'font-medium' : ''}">
                                ${surat.perihal}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${surat.jenis_surat === 'internal' ? 'bg-sky-100 text-sky-800' : 'bg-fuchsia-100 text-fuchsia-800'}">
                                    ${surat.jenis_surat === 'internal' ? 'Internal' : 'Eksternal'}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-500">
                                <span class="inline-flex items-center ${surat.nomor_surat?.includes('DIRUT') ? 'text-yellow-700 font-medium' : ''}">
                                    <i class="ri-user-${surat.nomor_surat?.includes('DIRUT') ? 'star' : ''}-line mr-1"></i>
                                    ${pengirim}
                                    ${surat.created_by == {{ auth()->id() }} ? '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Anda</span>' : ''}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(statusSekretaris)}">
                                    ${getStatusLabel(statusSekretaris)}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(statusDirut)}">
                                    ${getStatusLabel(statusDirut)}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap gap-2">
                                    <button onclick="showDetail(${surat.id})" class="inline-flex items-center px-2.5 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-md transition-colors duration-200" title="Lihat Detail">
                                        <i class="ri-eye-line mr-1"></i> Detail
                                    </button>

                                    ${(userRole === 1 || userRole === 2) ? `
                                        <button onclick="editDisposisi(${surat.id})" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md transition-colors duration-200" title="Edit Disposisi">
                                            <i class="ri-file-edit-line mr-1"></i> Disposisi
                                                                                    </button>
                                                                                ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;
                renderPagination(totalPages);
            }

            // Fungsi untuk render pagination
            function renderPagination(totalPages) {
                const paginationContainer = document.getElementById('pagination-container');
                if (totalPages <= 1) {
                    paginationContainer.innerHTML = '';
                    return;
                }
                let html = '';
                html += `<nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">`;
                // Tombol prev
                html += `<button class="px-3 py-1 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-l-md ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}">&laquo;</button>`;
                // Nomor halaman
                for (let i = 1; i <= totalPages; i++) {
                    html += `<button class="px-3 py-1 border-t border-b border-gray-300 bg-white text-gray-700 text-sm font-medium ${currentPage === i ? 'bg-green-100 text-green-700 font-bold' : 'hover:bg-gray-50'}" data-page="${i}">${i}</button>`;
                }
                // Tombol next
                html += `<button class="px-3 py-1 border border-gray-300 bg-white text-gray-700 text-sm font-medium rounded-r-md ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">&raquo;</button>`;
                html += `</nav>`;
                paginationContainer.innerHTML = html;
                // Event listener tombol pagination
                Array.from(paginationContainer.querySelectorAll('button[data-page]')).forEach(btn => {
                    btn.addEventListener('click', function() {
                        const page = parseInt(this.getAttribute('data-page'));
                        if (!isNaN(page) && page >= 1 && page <= totalPages && page !== currentPage) {
                            currentPage = page;
                            // Render ulang tabel dengan data yang sudah difilter
                            const filteredData = filterSurat(suratData);
                            renderTable(filteredData);
                        }
                    });
                });
            }

            // Load data saat halaman dimuat
            loadSuratMasuk();

            // Event listener untuk filter
            document.getElementById('btn-filter').addEventListener('click', function() {
                // Apply filter to the existing data without reloading from server
                const filteredData = filterSurat(suratData);
                renderTable(filteredData);
            });

            // Tambahkan event listener untuk setiap input filter
            ['search', 'start_date', 'end_date', 'jenis_surat', 'sifat_surat',
                'filter_status_sekretaris', 'filter_status_dirut', 'filter_surat_saya'
            ].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', function() {
                        currentPage = 1;
                        const filteredData = filterSurat(suratData);
                        renderTable(filteredData);
                    });

                    // Untuk input pencarian, tambahkan event keyup
                    if (id === 'search') {
                        element.addEventListener('keyup', function() {
                            currentPage = 1;
                            const filteredData = filterSurat(suratData);
                            renderTable(filteredData);
                        });
                    }
                }
            });

            // Fungsi untuk memfilter data surat dengan debug info
            function filterSurat(data) {
                console.log('Filtering surat with parameters:');
                const jenis = document.getElementById('jenis_surat').value;
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const urgency = document.getElementById('sifat_surat').value;
                const statusDispo = document.getElementById('filter_status_sekretaris').value;
                const statusDirut = document.getElementById('filter_status_dirut').value;
                const suratSaya = document.getElementById('filter_surat_saya').value;
                const searchQuery = document.getElementById('search').value.toLowerCase().trim();

                console.log({
                    jenis,
                    startDate,
                    endDate,
                    urgency,
                    statusDispo,
                    statusDirut,
                    suratSaya,
                    searchQuery
                });

                return data.filter(surat => {
                    try {
                        // Filter berdasarkan jenis surat
                        if (jenis && surat.jenis_surat !== jenis) {
                            console.log(`Surat ${surat.id} filtered out by jenis`);
                            return false;
                        }

                        // Filter berdasarkan tanggal
                        if (startDate || endDate) {
                            const tanggalSurat = new Date(surat.tanggal_surat);
                            if (startDate && new Date(startDate) > tanggalSurat) {
                                console.log(`Surat ${surat.id} filtered out by start date`);
                                return false;
                            }
                            if (endDate && new Date(endDate) < tanggalSurat) {
                                console.log(`Surat ${surat.id} filtered out by end date`);
                                return false;
                            }
                        }

                        // Filter berdasarkan urgency
                        if (urgency && surat.sifat_surat !== urgency) {
                            console.log(`Surat ${surat.id} filtered out by urgency`);
                            return false;
                        }

                        // Filter berdasarkan status disposisi sekretaris
                        if (statusDispo && (!surat.disposisi || surat.disposisi.status_sekretaris !==
                                statusDispo)) {
                            console.log(`Surat ${surat.id} filtered out by sekretaris status`);
                            return false;
                        }

                        // Filter berdasarkan status disposisi direktur
                        if (statusDirut && (!surat.disposisi || surat.disposisi.status_dirut !==
                                statusDirut)) {
                            console.log(`Surat ${surat.id} filtered out by dirut status`);
                            return false;
                        }

                        // Filter berdasarkan "surat saya"
                        const currentUserId = {{ auth()->id() }};
                        if (suratSaya === 'dari_saya' && surat.created_by != currentUserId) {
                            console.log(`Surat ${surat.id} filtered out by dari_saya`);
                            return false;
                        }
                        if (suratSaya === 'bukan_saya' && surat.created_by == currentUserId) {
                            console.log(`Surat ${surat.id} filtered out by bukan_saya`);
                            return false;
                        }

                        // Filter berdasarkan pencarian
                        if (searchQuery) {
                            const noDisposisi = (surat.disposisi && surat.disposisi.id ? surat.disposisi.id : '-').toString().toLowerCase();
                            const tanggal = (surat.tanggal_surat ? new Date(surat.tanggal_surat).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '').toLowerCase();
                            const nomorSurat = (surat.nomor_surat || '').toLowerCase();
                            const namaPerusahaan = (surat.perusahaanData && surat.perusahaanData.nama_perusahaan ? surat.perusahaanData.nama_perusahaan : (surat.nama_perusahaan ? surat.nama_perusahaan : (surat.perusahaan ? surat.perusahaan : '-'))).toLowerCase();
                            const perihal = (surat.perihal || '').toLowerCase();
                            const jenis = (surat.jenis_surat === 'internal' ? 'internal' : 'eksternal');
                            const pengirim = (surat.creator ? surat.creator.name : '').toLowerCase();
                            const jabatan = (surat.creator && surat.creator.jabatan ? surat.creator.jabatan : '').toLowerCase();
                            const statusSekretaris = (surat.disposisi && surat.disposisi.status_sekretaris ? surat.disposisi.status_sekretaris : 'pending').toLowerCase();
                            const statusDirut = (surat.disposisi && surat.disposisi.status_dirut ? surat.disposisi.status_dirut : 'pending').toLowerCase();
                            
                            const isMatch =
                                noDisposisi.includes(searchQuery) ||
                                tanggal.includes(searchQuery) ||
                                nomorSurat.includes(searchQuery) ||
                                namaPerusahaan.includes(searchQuery) ||
                                perihal.includes(searchQuery) ||
                                jenis.includes(searchQuery) ||
                                pengirim.includes(searchQuery) ||
                                jabatan.includes(searchQuery) ||
                                statusSekretaris.includes(searchQuery) ||
                                statusDirut.includes(searchQuery);
                            if (!isMatch) {
                                return false;
                            }
                        }

                        return true;
                    } catch (error) {
                        console.error('Error filtering surat:', error, surat);
                        return false;
                    }
                });
            }

            // Fungsi global untuk edit disposisi
            window.editDisposisi = function(id) {
                const surat = suratData.find(s => s.id === id);
                if (!surat) {
                    console.error('Surat tidak ditemukan:', id);
                    return;
                }

                try {
                    // Reset form
                    const form = document.getElementById('edit-disposisi-form');
                    if (form) form.reset();

                    // Set ID surat
                    document.getElementById('edit-disposisi-surat-id').value = surat.id;

                    // Tampilkan loading, sembunyikan content
                    document.getElementById('disposisi-loading').classList.remove('hidden');
                    document.getElementById('disposisi-form-content').classList.add('hidden');

                    // Tampilkan modal
                    document.getElementById('edit-disposisi-modal').classList.remove('hidden');

                    // Ambil data disposisi
                    fetch(`/api/disposisi/surat/${surat.id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Sembunyikan loading, tampilkan content
                            document.getElementById('disposisi-loading').classList.add('hidden');
                            document.getElementById('disposisi-form-content').classList.remove('hidden');

                            if (data.success && data.disposisi) {
                                // Set ID disposisi
                                document.getElementById('edit-disposisi-id').value = data.disposisi.id;

                                // Atur bagian yang ditampilkan berdasarkan role
                                if (userRole === 1) { // Sekretaris
                                    document.getElementById('sekretaris-section').classList.remove(
                                        'hidden');
                                    document.getElementById('direktur-section').classList.add('hidden');

                                    // Isi field sekretaris
                                    document.getElementById('status_sekretaris').value = data.disposisi
                                        .status_sekretaris || 'pending';
                                } else if (userRole === 2) { // Direktur
                                    document.getElementById('sekretaris-section').classList.add('hidden');
                                    document.getElementById('direktur-section').classList.remove('hidden');

                                    // Isi field direktur
                                    const statusDirut = data.disposisi.status_dirut || 'pending';
                                    document.getElementById('status_dirut').value = statusDirut;

                                    // Setup event listener untuk status_dirut
                                    setupStatusDirectorChangeHandler();

                                    // Initial visibility setup based on current status
                                    toggleTujuanDisposisiVisibility(statusDirut);

                                    // Muat tujuan disposisi
                                    loadDisposisiUsers(data.disposisi.id);
                                }
                            } else {
                                alert('Data disposisi tidak ditemukan');
                                document.getElementById('edit-disposisi-modal').classList.add('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('disposisi-loading').classList.add('hidden');
                            alert('Terjadi kesalahan saat memuat data disposisi');
                            document.getElementById('edit-disposisi-modal').classList.add('hidden');
                        });
                } catch (error) {
                    console.error('Error in editDisposisi:', error);
                    alert('Terjadi kesalahan saat membuka form disposisi');
                }
            };

            // Fungsi untuk toggle visibility tujuan disposisi
            function toggleTujuanDisposisiVisibility(status) {
                const tujuanSection = document.getElementById('tujuan-disposisi-section');

                if (status === 'approved') {
                    tujuanSection.classList.remove('hidden');
                } else {
                    tujuanSection.classList.add('hidden');
                }

                console.log('Toggling tujuan disposisi visibility for status:', status,
                    'Is visible:', status === 'approved');
            }

            // Setup event handler untuk perubahan status direktur
            function setupStatusDirectorChangeHandler() {
                const statusDirutSelect = document.getElementById('status_dirut');

                statusDirutSelect.addEventListener('change', function() {
                    const selectedStatus = this.value;
                    toggleTujuanDisposisiVisibility(selectedStatus);

                    // Jika statusnya bukan approved, hapus semua checkboxes
                    if (selectedStatus !== 'approved') {
                        document.querySelectorAll('.tujuan-disposisi-checkbox:checked').forEach(
                            checkbox => {
                                checkbox.checked = false;
                            });
                        updateSelectedCount();
                    }
                });
            }

            // Fungsi untuk memuat daftar user tujuan disposisi
            function loadDisposisiUsers(disposisiId, page = 1) {
                const container = document.getElementById('tujuan-disposisi-container');
                container.innerHTML =
                    '<div class="py-4 text-center text-gray-500"><i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Memuat data...</div>';

                // Debugging info
                console.log('Memuat data tujuan disposisi untuk ID:', disposisiId);

                fetch(`/api/disposisi/${disposisiId}/tujuan?page=${page}&page_size=50`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute(
                                'content')
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        console.log('Status response tujuan:', response.status);
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response error text:', text);
                                throw new Error(`Error status ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data tujuan disposisi:', data);
                        
                        if (data.success && data.available_users && data.available_users.length > 0) {
                            // Store selected users in global variable for later use
                            window.selectedUserIds = data.tujuan ? data.tujuan.map(t => t.id.toString()) : [];
                            
                            // Render the users list with pagination controls
                            renderTujuanDisposisi(data.available_users, window.selectedUserIds, data.pagination);
                            
                            // Setup click handlers for pagination controls
                            setupPaginationHandlers(disposisiId, data.pagination);
                            
                            // Setup search functionality
                            setupTujuanSearch();

                            // Update counter
                            updateSelectedCount();
                        } else {
                            container.innerHTML =
                                '<div class="py-4 text-center text-gray-500">Tidak ada pengguna yang tersedia</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML =
                            '<div class="py-4 text-center text-red-500">Gagal memuat data tujuan disposisi</div>';
                    });
            }

            // Render daftar tujuan disposisi dengan pagination
            function renderTujuanDisposisi(users, selectedUserIds, pagination) {
                const tujuanList = document.getElementById('tujuan-disposisi-container');
                tujuanList.innerHTML = '';

                if (users.length === 0) {
                    tujuanList.innerHTML = '<div class="alert alert-info">Tidak ada user yang tersedia</div>';
                    return;
                }

                // Create the user checkboxes
                const checkboxesContainer = document.createElement('div');
                checkboxesContainer.className = 'space-y-1 mb-4';

                users.forEach(user => {
                    const isSelected = selectedUserIds && selectedUserIds.includes(user.id.toString());

                    const userItem = document.createElement('div');
                    userItem.className = 'form-check mb-2 p-2 hover:bg-gray-50 transition-colors duration-150 rounded cursor-pointer';
                    
                    // Get jabatan text properly handling different formats
                    let jabatanText = '';
                    if (user.jabatan) {
                        if (typeof user.jabatan === 'object' && user.jabatan.nama_jabatan) {
                            jabatanText = user.jabatan.nama_jabatan;
                        } else if (typeof user.jabatan === 'string') {
                            jabatanText = user.jabatan;
                        } else {
                            jabatanText = 'Tanpa Jabatan';
                        }
                    } else {
                        jabatanText = 'Tanpa Jabatan';
                    }
                    
                    // Get role label
                    let roleLabel = '';
                    if (user.role !== undefined) {
                        switch(user.role) {
                            case 0:
                                roleLabel = '<span class="inline-flex px-2 text-xs font-semibold bg-green-100 text-green-800 rounded-full ml-1">Staff</span>';
                                break;
                            case 1:
                                roleLabel = '<span class="inline-flex px-2 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full ml-1">Sekretaris</span>';
                                break;
                            case 2:
                                roleLabel = '<span class="inline-flex px-2 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full ml-1">Direktur</span>';
                                break;
                            case 3:
                                roleLabel = '<span class="inline-flex px-2 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full ml-1">Admin</span>';
                                break;
                        }
                    }
                    
                    userItem.innerHTML = `
                        <div class="flex items-center">
                            <input class="form-check-input tujuan-disposisi-checkbox h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500" 
                                type="checkbox" value="${user.id}" id="user-${user.id}" ${isSelected ? 'checked' : ''}>
                            <label class="form-check-label ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none" for="user-${user.id}">
                                <span>${user.name}</span> ${jabatanText ? `<span class="text-xs text-gray-500 ml-1">(${jabatanText})</span>` : ''}
                            </label>
                        </div>
                    `;
                    
                    // Tambahkan event untuk klik pada seluruh item (bukan hanya checkbox)
                    userItem.addEventListener('click', function(e) {
                        if (e.target.type !== 'checkbox') {
                            const checkbox = this.querySelector('input[type="checkbox"]');
                            checkbox.checked = !checkbox.checked;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                    
                    checkboxesContainer.appendChild(userItem);
                });
                
                tujuanList.appendChild(checkboxesContainer);

                // Add pagination info and controls
                if (pagination && pagination.total > 0) {
                    const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
                    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
                    
                    const paginationContainer = document.createElement('div');
                    paginationContainer.className = 'flex justify-between items-center pt-2 border-t mt-2';
                    
                    // Pagination info
                    const infoSpan = document.createElement('span');
                    infoSpan.className = 'text-xs text-gray-500';
                    infoSpan.textContent = `Showing ${start}-${end} of ${pagination.total}`;
                    paginationContainer.appendChild(infoSpan);
                    
                    // Pagination buttons
                    const buttonsDiv = document.createElement('div');
                    buttonsDiv.className = 'flex space-x-2';
                    
                    // Previous button
                    if (pagination.current_page > 1) {
                        const prevButton = document.createElement('button');
                        prevButton.type = 'button';
                        prevButton.className = 'px-2 py-1 text-xs text-blue-600 hover:text-blue-800 prev-page';
                        prevButton.innerHTML = '<i class="ri-arrow-left-s-line"></i> Prev';
                        buttonsDiv.appendChild(prevButton);
                    }
                    
                    // Next button
                    if (pagination.has_more) {
                        const nextButton = document.createElement('button');
                        nextButton.type = 'button';
                        nextButton.className = 'px-2 py-1 text-xs text-blue-600 hover:text-blue-800 next-page';
                        nextButton.innerHTML = 'Next <i class="ri-arrow-right-s-line"></i>';
                        buttonsDiv.appendChild(nextButton);
                    }
                    
                    paginationContainer.appendChild(buttonsDiv);
                    tujuanList.appendChild(paginationContainer);
                }
            }
            
            // Setup event handlers for pagination buttons
            function setupPaginationHandlers(disposisiId, pagination) {
                // Previous page button
                const prevButton = document.querySelector('.prev-page');
                if (prevButton) {
                    prevButton.addEventListener('click', () => {
                        loadDisposisiUsers(disposisiId, pagination.current_page - 1);
                    });
                }
                
                // Next page button
                const nextButton = document.querySelector('.next-page');
                if (nextButton) {
                    nextButton.addEventListener('click', () => {
                        loadDisposisiUsers(disposisiId, pagination.current_page + 1);
                    });
                }
            }

            // Setup pencarian tujuan disposisi
            function setupTujuanSearch() {
                const searchInput = document.getElementById('search-tujuan');
                const selectAllBtn = document.getElementById('select-all-tujuan');
                const clearAllBtn = document.getElementById('clear-all-tujuan');
                
                if (!searchInput || !selectAllBtn || !clearAllBtn) {
                    console.error('Search elements not found');
                    return;
                }

                // Event listener untuk pencarian
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const userItems = document.querySelectorAll('.form-check');

                    let visible = 0;
                    userItems.forEach(item => {
                        const label = item.querySelector('label');
                        const text = label.textContent.toLowerCase();

                        if (text.includes(searchTerm)) {
                            item.style.display = 'block';
                            visible++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Hide pagination when searching
                    const paginationContainer = document.querySelector('.tujuan-disposisi-container .pagination-container');
                    if (paginationContainer) {
                        paginationContainer.style.display = searchTerm ? 'none' : 'flex';
                    }
                    
                    // Show message if no results
                    if (visible === 0 && userItems.length > 0) {
                        const noResults = document.createElement('div');
                        noResults.className = 'no-results py-2 text-center text-gray-500';
                        noResults.textContent = 'Tidak ada hasil yang cocok';
                        
                        // Remove existing no-results messages
                        document.querySelectorAll('.no-results').forEach(el => el.remove());
                        
                        // Add the no results message after the checkboxes
                        const checkboxesContainer = document.querySelector('.space-y-2.mb-4');
                        if (checkboxesContainer) {
                            checkboxesContainer.appendChild(noResults);
                        }
                    } else {
                        // Remove any existing no-results message
                        document.querySelectorAll('.no-results').forEach(el => el.remove());
                    }
                });

                // Event listener untuk tombol Pilih Semua
                selectAllBtn.addEventListener('click', function() {
                    const visibleCheckboxes = document.querySelectorAll('.form-check:not([style*="display: none"]) .tujuan-disposisi-checkbox');
                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    updateSelectedCount();
                });

                // Event listener untuk tombol Hapus Semua
                clearAllBtn.addEventListener('click', function() {
                    const visibleCheckboxes = document.querySelectorAll('.form-check:not([style*="display: none"]) .tujuan-disposisi-checkbox');
                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    updateSelectedCount();
                });

                // Add event listeners to all checkboxes for update counter
                document.querySelectorAll('.tujuan-disposisi-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCount);
                });
            }

            // Function to update the count of selected targets
            function updateSelectedCount() {
                const checkboxes = document.querySelectorAll('.tujuan-disposisi-checkbox:checked');
                const countElement = document.getElementById('selected-tujuan-count');
                if (countElement) {
                    countElement.textContent = `${checkboxes.length} tujuan dipilih`;
                }
            }

            // Fungsi untuk menyimpan disposisi
            function saveDisposisi() {
                const disposisiId = document.getElementById('edit-disposisi-id').value;
                const suratId = document.getElementById('edit-disposisi-surat-id').value;

                console.log('===== SAVE DISPOSISI DEBUG =====');
                console.log('Disposisi ID:', disposisiId);
                console.log('Surat ID:', suratId);
                console.log('User Role:', userRole);
                console.log('Selected User IDs from global variable:', window.selectedUserIds);

                if (!disposisiId) {
                    alert('ID disposisi tidak valid');
                    return;
                }

                // Kumpulkan data berdasarkan role
                let formData = new FormData();

                if (userRole === 1) { // Sekretaris
                    const sekretarisStatus = document.getElementById('status_sekretaris').value;
                    formData.append('status_sekretaris', sekretarisStatus);
                    console.log('Sekretaris Status:', sekretarisStatus);
                } else if (userRole === 2) { // Direktur
                    const direktorStatus = document.getElementById('status_dirut').value;
                    const direktorKeterangan = document.getElementById('keterangan_dirut').value;

                    formData.append('status_dirut', direktorStatus);
                    formData.append('keterangan_dirut', direktorKeterangan);

                    console.log('Direktor Status:', direktorStatus);
                    console.log('Direktor Keterangan:', direktorKeterangan);

                    // Debug tujuan disposisi container
                    const container = document.getElementById('tujuan-disposisi-container');
                    console.log('Tujuan container exists:', !!container);
                    if (container) {
                        console.log('Tujuan container HTML:', container.innerHTML.substring(0, 100) + '...');
                    }

                    // Ambil tujuan disposisi yang dipilih
                    const allCheckboxes = document.querySelectorAll('.tujuan-disposisi-checkbox');
                    const tujuanCheckboxes = document.querySelectorAll('.tujuan-disposisi-checkbox:checked');

                    console.log('All checkboxes selector:', '.tujuan-disposisi-checkbox');
                    console.log('Total checkboxes found:', allCheckboxes.length);
                    console.log('All checkboxes IDs:', Array.from(allCheckboxes).map(cb => cb.id));
                    console.log('Checked checkboxes found:', tujuanCheckboxes.length);
                    console.log('Checked checkboxes IDs:', Array.from(tujuanCheckboxes).map(cb => cb.id));

                    // Gunakan tujuanCheckboxes untuk disposisi jika ada, jika tidak gunakan window.selectedUserIds
                    let tujuanIds = Array.from(tujuanCheckboxes).map(cb => cb.value);

                    // Fallback ke window.selectedUserIds jika tidak ada checkbox yang dipilih tetapi ada selectedUserIds
                    if (tujuanIds.length === 0 && window.selectedUserIds && window.selectedUserIds.length > 0) {
                        console.log('Menggunakan window.selectedUserIds sebagai fallback:', window.selectedUserIds);
                        tujuanIds = window.selectedUserIds;
                    }

                    // Tambahkan setiap ID tujuan ke formData
                    tujuanIds.forEach(id => {
                        formData.append('tujuan_disposisi[]', id);
                    });

                    console.log('Tujuan disposisi yang dipilih:', tujuanIds);
                }

                // Tambahkan CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', csrfToken);
                console.log('CSRF Token:', csrfToken);

                // Log form data entries
                console.log('Form data entries:');
                for (let pair of formData.entries()) {
                    console.log(' - ' + pair[0] + ': ' + pair[1]);
                }

                // Tampilkan loading state
                const saveButton = document.getElementById('save-disposisi-button');
                const originalText = saveButton.innerHTML;
                saveButton.disabled = true;
                saveButton.innerHTML =
                    '<i class="ri-loader-4-line animate-spin inline-block mr-1"></i> Menyimpan...';

                // Gunakan metode POST langsung (bukan JSON)
                console.log('Sending request to:', `/api/disposisi/${disposisiId}/update`);
                fetch(`/api/disposisi/${disposisiId}/update`, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:',
                            Array.from(response.headers.entries()).map(h => `${h[0]}: ${h[1]}`).join(', '));

                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response error text:', text);
                                try {
                                    // Try to parse as JSON
                                    const jsonData = JSON.parse(text);
                                    throw new Error(jsonData.message ||
                                        `HTTP error! Status: ${response.status}`);
                                } catch (e) {
                                    // If not JSON, return as text error
                                    throw new Error(
                                        `HTTP error! Status: ${response.status}, Response: ${text}`);
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);

                        if (data.success) {
                            alert('Disposisi berhasil disimpan');
                            document.getElementById('edit-disposisi-modal').classList.add('hidden');
                            // Refresh halaman untuk melihat perubahan
                            window.location.reload();
                        } else {
                            alert(data.message || 'Gagal menyimpan disposisi');
                        }
                    })
                    .catch(error => {
                        console.error('Error saat menyimpan disposisi:', error);
                        alert('Terjadi kesalahan saat menyimpan disposisi: ' + error.message);
                    })
                    .finally(() => {
                        // Kembalikan button ke state awal
                        saveButton.disabled = false;
                        saveButton.innerHTML = originalText;
                        console.log('===== END SAVE DISPOSISI DEBUG =====');
                    });
            }

            // Event listener untuk tombol-tombol disposisi
            document.getElementById('save-disposisi-button')?.addEventListener('click', saveDisposisi);

            document.getElementById('close-disposisi-modal')?.addEventListener('click', function() {
                document.getElementById('edit-disposisi-modal').classList.add('hidden');
            });

            document.getElementById('cancel-disposisi-button')?.addEventListener('click', function() {
                document.getElementById('edit-disposisi-modal').classList.add('hidden');
        });

        // Fungsi untuk menampilkan detail surat
        window.showDetail = function(id) {
            const surat = suratData.find(s => s.id === id);
            if (!surat) return;

                // Save current surat ID for preview
                currentSuratId = id;

            // Tampilkan loading state
            document.getElementById('detail-keterangan-pengirim').textContent = 'Memuat...';
            document.getElementById('detail-keterangan-sekretaris').textContent = 'Memuat...';
            document.getElementById('detail-keterangan-dirut').textContent = 'Memuat...';
            document.getElementById('detail-tujuan-disposisi').innerHTML = '<p>Memuat data tujuan disposisi...</p>';
            document.getElementById('detail-pengirim').textContent = 'Memuat...';
            document.getElementById('detail-perusahaan').textContent = 'Memuat...';
                
            // Reset disposisi info
            document.getElementById('detail-disposisi-id').textContent = '-';
            document.getElementById('detail-disposisi-tanggal').textContent = '-';

            // Ambil data disposisi
            fetch(`/api/disposisi/surat/${surat.id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.disposisi) {
                        document.getElementById('detail-keterangan-pengirim').textContent = data.disposisi
                            .keterangan_pengirim || '-';
                        document.getElementById('detail-keterangan-sekretaris').textContent = data.disposisi
                            .keterangan_sekretaris || '-';
                        document.getElementById('detail-keterangan-dirut').textContent = data.disposisi
                            .keterangan_dirut || '-';

                        document.getElementById('detail-status-sekretaris').innerHTML = getStatusHTML(data.disposisi
                            .status_sekretaris);
                        document.getElementById('detail-status-dirut').innerHTML = getStatusHTML(data.disposisi
                            .status_dirut);
                                
                            // Tampilkan ID disposisi
                            document.getElementById('detail-disposisi-id').textContent = data.disposisi.id || '-';
                            
                            // Tampilkan tanggal disposisi (waktu_review_dirut)
                            if (data.disposisi.waktu_review_dirut) {
                                const disposisiDate = new Date(data.disposisi.waktu_review_dirut);
                                document.getElementById('detail-disposisi-tanggal').textContent = disposisiDate.toLocaleDateString(
                                    'id-ID', {
                                        day: '2-digit',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                            } else {
                                document.getElementById('detail-disposisi-tanggal').textContent = '-';
                            }

                        // Jika ada data disposisi, ambil data tujuan disposisi
                        if (data.disposisi.id) {
                            fetchTujuanDisposisi(data.disposisi.id);
                        } else {
                            document.getElementById('detail-tujuan-disposisi').innerHTML =
                                '<p>Belum ada tujuan disposisi</p>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching disposisi:', error);
                    document.getElementById('detail-tujuan-disposisi').innerHTML =
                        '<p class="text-red-500">Gagal memuat data tujuan disposisi</p>';
                });

            // Isi informasi surat lainnya
            const modalTitle = document.getElementById('modal-title');
            modalTitle.innerHTML = 'Detail Surat Masuk' +
                (surat.created_by == {{ auth()->id() }} ?
                    ' <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Dari Anda</span>' :
                    '');

            document.getElementById('detail-nomor').textContent = surat.nomor_surat;
            document.getElementById('detail-tanggal').textContent = new Date(surat.tanggal_surat).toLocaleDateString(
                'id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            document.getElementById('detail-jenis').textContent = surat.jenis_surat === 'internal' ? 'Internal' :
                'Eksternal';
                
            // Display company information
            const perusahaanElement = document.getElementById('detail-perusahaan');
            if (surat.perusahaanData && surat.perusahaanData.nama_perusahaan) {
                // Jika ada nama perusahaan dari relasi
                perusahaanElement.textContent = surat.perusahaanData.nama_perusahaan;
                console.log('Using perusahaanData:', surat.perusahaanData.nama_perusahaan);
            } else if (surat.perusahaan) {
                // Jika hanya ada kode perusahaan
                perusahaanElement.textContent = surat.perusahaan;
                console.log('Using direct perusahaan value:', surat.perusahaan);
            } else {
                // Default untuk surat internal atau data tidak ada
                perusahaanElement.textContent = surat.jenis_surat === 'internal' ? 'RSAZRA' : '-';
            }

            // Tampilkan sifat surat dengan warna merah untuk urgent
            const sifatElement = document.getElementById('detail-sifat');
            if (surat.sifat_surat === 'urgent') {
                sifatElement.textContent = 'Urgent';
                sifatElement.className = 'text-base font-semibold text-red-600';
            } else {
                sifatElement.textContent = 'Normal';
                sifatElement.className = 'text-base font-semibold text-gray-900';
            }

            document.getElementById('detail-perihal').textContent = surat.perihal;

            // Tampilkan informasi pengirim (nama dan jabatan)
            const pengirimElement = document.getElementById('detail-pengirim');
            if (surat.creator) {
                console.log('Creator data:', surat.creator);
                console.log('Creator jabatan:', surat.creator.jabatan, typeof surat.creator.jabatan);
                
                // Check if jabatan is an object with nama_jabatan or a string
                let jabatanText = '';
                if (surat.creator.jabatan) {
                    if (typeof surat.creator.jabatan === 'object' && surat.creator.jabatan.nama_jabatan) {
                        jabatanText = surat.creator.jabatan.nama_jabatan;
                        console.log('Using nama_jabatan:', jabatanText);
                    } else {
                        jabatanText = surat.creator.jabatan;
                        console.log('Using direct jabatan string:', jabatanText);
                    }
                }
                
                pengirimElement.innerHTML = `
                    <span class="font-semibold">${surat.creator.name}</span>
                    ${jabatanText ? `<span class="text-sm font-normal text-gray-600 ml-1">(${jabatanText})</span>` : ''}
                `;
            } else {
                pengirimElement.textContent = surat.created_by || '-';
            }

            // Set download link and preview button visibility
            const previewButton = document.getElementById('detail-preview-link');
            const downloadLink = document.getElementById('detail-file-link');
            
            if (surat.file_path) {
                    downloadLink.href = `/suratkeluar/${id}/download`;
                    downloadLink.style.display = 'inline-flex';
                    previewButton.style.display = 'inline-flex';
            } else {
                    downloadLink.style.display = 'none';
                    previewButton.style.display = 'none';
            }

            // Tampilkan modal
            document.getElementById('detail-modal').classList.remove('hidden');

            // Render daftar file
            renderDetailFiles(surat.files || []);
        };

        // Fungsi untuk mengambil data tujuan disposisi
        function fetchTujuanDisposisi(disposisiId) {
            fetch(`/api/disposisi/${disposisiId}/tujuan`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Tujuan disposisi data:', data);
                    
                    if (data.success && data.tujuan && data.tujuan.length > 0) {
                        console.log('Tujuan users:', data.tujuan);
                        
                        let tujuanHTML = '<ul class="list-disc pl-5 space-y-1">';
                        data.tujuan.forEach(user => {
                            console.log('User data:', user);
                            console.log('User jabatan:', user.jabatan, typeof user.jabatan);
                            
                            // Handle different jabatan formats
                            let jabatanText = '';
                            if (user.jabatan) {
                                if (typeof user.jabatan === 'object' && user.jabatan.nama_jabatan) {
                                    jabatanText = user.jabatan.nama_jabatan;
                                    console.log('Using nama_jabatan:', jabatanText);
                                } else {
                                    jabatanText = user.jabatan;
                                    console.log('Using direct jabatan string:', jabatanText);
                                }
                            }
                            
                            tujuanHTML += `
                                <li>
                                    <span class="font-medium">${user.name}</span>
                                    ${jabatanText ? `<span class="text-sm font-normal text-gray-600 ml-1">(${jabatanText})</span>` : ''}
                                </li>`;
                        });
                        tujuanHTML += '</ul>';
                        document.getElementById('detail-tujuan-disposisi').innerHTML = tujuanHTML;
                    } else {
                        document.getElementById('detail-tujuan-disposisi').innerHTML =
                            '<p>Belum ada tujuan disposisi</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching tujuan disposisi:', error);
                    document.getElementById('detail-tujuan-disposisi').innerHTML =
                        '<p class="text-red-500">Gagal memuat data tujuan disposisi</p>';
                });
        }

            // Tambahkan event listener untuk tombol preview pada detail surat
            document.getElementById('detail-preview-link').addEventListener('click', function(e) {
                e.preventDefault();
                // Ambil data surat
                if (!currentSuratId) return;
                const surat = suratData.find(s => s.id === currentSuratId);
                if (!surat) return;
                let previewUrl = '';
                if (surat.files && surat.files.length > 0) {
                    previewUrl = '/' + surat.files[0].file_path;
                } else if (surat.file_path) {
                    previewUrl = `/suratkeluar/${currentSuratId}/preview`;
                } else {
                    alert('File tidak tersedia');
                    return;
                }
                window.open(previewUrl, '_blank');
            });

            // Tambahkan event listener untuk tombol close preview
            document.getElementById('close-detail-preview')?.addEventListener('click', function() {
                document.getElementById('detail-preview-container')?.classList.add('hidden');
            });

            // Fungsi untuk preview file di dalam modal full screen
            function previewFileInDetail() {
                if (!currentSuratId) return;

                const surat = suratData.find(s => s.id === currentSuratId);
                if (!surat) {
                    alert('Data surat tidak ditemukan');
                    return;
                }

                // Inisialisasi URL preview untuk yang pertama jika banyak
                let previewUrl;
                let fileName;
                
                // Jika surat punya banyak file, pilih yang pertama
                if (surat.files && surat.files.length > 0) {
                    previewUrl = '/' + surat.files[0].file_path;
                    fileName = surat.files[0].original_name || surat.files[0].file_path.split('/').pop();
                } else if (surat.file_path) {
                    // Fallback ke file tunggal (legacy format)
                    previewUrl = `/suratkeluar/${currentSuratId}/preview`;
                    fileName = surat.file_path.split('/').pop();
                        } else {
                    alert('File tidak tersedia');
                    return;
            }

                // Get file extension
                const fileExt = previewUrl.split('.').pop().toLowerCase();
                
                // Gunakan modal fullscreen untuk preview
                const previewModal = document.getElementById('file-preview-modal');
                const pdfPreview = document.getElementById('pdf-preview');
                const imgPreview = document.getElementById('img-preview');
                const loadingPreview = document.getElementById('loading-preview');
                const errorPreview = document.getElementById('error-preview');

                // Reset tampilan awal
                previewModal.classList.remove('hidden');
                document.getElementById('preview-modal-title').textContent = 'Preview: ' + fileName;
                document.getElementById('preview-download-link').href = previewUrl;

                // Sembunyikan semua, tampilkan loading
                pdfPreview.classList.add('hidden');
                imgPreview.classList.add('hidden');
                errorPreview.classList.add('hidden');
                loadingPreview.classList.remove('hidden');
                
                // Atur tampilan berdasarkan jenis file
                if (fileExt === 'pdf') {
                    // Preview PDF
                    pdfPreview.src = previewUrl;
                    pdfPreview.classList.remove('hidden');
                    pdfPreview.style.display = 'block';
                    loadingPreview.classList.add('hidden');
                } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                    // Preview gambar
                    imgPreview.src = previewUrl;
                    imgPreview.onload = function() {
                        // Setelah gambar loaded, sembunyikan loading dan tampilkan gambar
                        loadingPreview.classList.add('hidden');
                        imgPreview.classList.remove('hidden');
                        imgPreview.style.display = 'block';
                    };
                    imgPreview.onerror = function() {
                        loadingPreview.classList.add('hidden');
                        errorPreview.classList.remove('hidden');
                    };
                } else {
                    // Format tidak didukung
                    loadingPreview.classList.add('hidden');
                    errorPreview.classList.remove('hidden');
                }
            }

            // Event handler for close modal to reset modal positioning
        document.getElementById('close-detail-modal').addEventListener('click', function() {
            document.getElementById('detail-modal').classList.add('hidden');
                // Tidak perlu mereset preview container karena sudah dihapus
                
                // Reset modal positioning
                const modalPanel = document.getElementById('detail-modal-panel');
                modalPanel.classList.remove('sm:align-top');
                modalPanel.classList.add('sm:align-middle');
                modalPanel.style.maxHeight = '';
                modalPanel.style.height = '';
                
                // Reset modal wrapper padding
                const modalWrapper = document.querySelector('#detail-modal > div');
                if (modalWrapper) {
                    modalWrapper.classList.add('pt-4', 'pb-20');
                    modalWrapper.classList.remove('pt-2', 'pb-2');
                }
                
                currentSuratId = null;
        });

        document.getElementById('close-preview-modal').addEventListener('click', function() {
            document.getElementById('file-preview-modal').classList.add('hidden');
                
                // Reset source untuk iframe dan img untuk menghindari memory leak
                const pdfPreview = document.getElementById('pdf-preview');
                if (pdfPreview) pdfPreview.src = '';
                
                const imgPreview = document.getElementById('img-preview');
                if (imgPreview) {
                    imgPreview.src = '';
                    imgPreview.style.display = 'none';
                }
            });
            
            // Add event listener for detail preview button
            document.getElementById('detail-preview-link').addEventListener('click', function() {
                previewFileInDetail();
            });
            
            // Add event listener for closing detail preview
            document.getElementById('close-detail-preview')?.addEventListener('click', function() {
                document.getElementById('detail-preview-container')?.classList.add('hidden');
            });
        });

        // Tambahkan fungsi renderDetailFiles yang menangani event preview dengan benar
        function renderDetailFiles(files) {
            const filesList = document.getElementById('detail-files-list');
            const noFileText = document.getElementById('no-file-text');
            
            filesList.innerHTML = '';
            
            if (files && files.length > 0) {
                noFileText.style.display = 'none';
                
                files.forEach((file, idx) => {
                    const ext = file.file_path.split('.').pop().toLowerCase();
                    let previewBtn = '';
                    
                    if(['pdf','jpg','jpeg','png','doc','docx','xls','xlsx','ppt','pptx','txt','zip','rar'].includes(ext)) {
                        previewBtn = `<a href="/${file.file_path}" target="_blank" class="ml-2 text-xs text-blue-600 underline">Preview</a>`;
                    }
                    
                    filesList.innerHTML += `
                        <div class="flex items-center space-x-2">
                            <i class="ri-file-text-line text-lg text-green-500"></i>
                            <span class="text-sm">${file.original_name || file.file_path.split('/').pop()}</span>
                            <a href="/${file.file_path}" target="_blank" class="text-xs text-green-600 underline">Download</a>
                            ${previewBtn}
                        </div>
                    `;
                });
            } else {
                noFileText.style.display = 'inline';
            }
        }
    </script>
@endsection

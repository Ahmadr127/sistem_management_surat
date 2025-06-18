@extends('home')

@section('title', 'Surat Keluar - SISM Azra')

@section('content')
    <!-- Tambahkan jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Surat Keluar</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola data surat keluar</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" id="search"
                        class="w-full sm:w-64 px-4 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200"
                        placeholder="Cari surat keluar...">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <i class="ri-search-line"></i>
                    </div>
                </div>

                <!-- Trash Button -->
                <a href="{{ route('suratkeluar.trashed') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    <i class="ri-delete-bin-line mr-1.5"></i>
                    Sampah
                </a>

                <!-- Tambah Surat Button -->
                <button type="button" onclick="window.location.href='{{ route('suratkeluar.create') }}'"
                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="ri-add-line mr-1.5"></i>
                    Tambah Surat
                </button>
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

                    <!-- Filter Perusahaan -->
                    <select id="perusahaan"
                        class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 transition-all duration-200">
                        <option value="">Semua Perusahaan</option>
                        <option value="RSAZRA">RSAZRA</option>
                        <option value="ASP">ASP</option>
                        <option value="ISP">ISP</option>
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

                    <!-- Tombol Filter -->
                    <button id="btn-filter"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                        <i class="ri-filter-line mr-1.5"></i>
                        Filter
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto border border-gray-100 rounded-lg shadow-sm m-4">
                <table class="w-full" id="suratKeluarTable">
                    <thead class="bg-green-600">
                        <tr>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                No Disposisi & Surat
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Nomor Surat
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Nama Perusahaan
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Perihal
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Jenis
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Pembuat
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Status Sekretaris
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Status Direktur
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Tujuan Disposisi
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="surat-table-body">
                        <!-- Data akan diisi melalui JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prev-page-mobile"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sebelumnya
                    </button>
                    <button id="next-page-mobile"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Selanjutnya
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan <span id="page-start">1</span> sampai <span id="page-end">10</span> dari <span
                                id="total-items">0</span> data
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination"
                            id="pagination-container">
                            <!-- Pagination akan diisi melalui JavaScript -->
                        </nav>
                    </div>
                </div>
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
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                <!-- Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                            Detail Surat Keluar
                        </h3>
                        <button type="button" id="close-detail-modal" class="text-gray-400 hover:text-gray-500">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="bg-gray-50 px-6 py-2 border-b border-gray-200">
                    <div class="flex space-x-4">
                        <button id="detail-tab-btn" class="py-2 px-1 text-sm font-medium text-green-600 border-b-2 border-green-500 hover:text-green-700">
                            <i class="ri-file-info-line mr-1"></i>
                            Detail Surat
                        </button>
                        <button id="preview-tab-btn" class="py-2 px-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300">
                            <i class="ri-file-text-line mr-1"></i>
                            Preview File
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="max-h-[70vh] overflow-y-auto overflow-x-hidden">
                    <!-- Detail Tab Content -->
                    <div id="detail-tab-content" class="px-6 py-4">
                        <!-- Informasi Umum -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h4 class="text-base font-semibold text-gray-800 mb-3">
                                    <i class="ri-information-line mr-1 text-gray-600"></i>
                                    Informasi Umum
                                </h4>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 mb-4">
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                                        <div class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Nomor Surat</dt>
                                            <dd class="mt-1 text-sm font-semibold text-gray-900" id="detail-nomor">Loading...</dd>
                            </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Tanggal Surat</dt>
                                            <dd class="mt-1 text-sm text-gray-900" id="detail-tanggal">Loading...</dd>
                            </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Jenis Surat</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <span id="detail-jenis-badge" class="px-2 py-1 text-xs font-medium rounded-full">
                                                    <span id="detail-jenis">Loading...</span>
                                                </span>
                                            </dd>
                            </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Sifat Surat</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <span id="detail-sifat-badge" class="px-2 py-1 text-xs font-medium rounded-full">
                                                    <span id="detail-sifat">Loading...</span>
                                                </span>
                                            </dd>
                            </div>
                                        <div class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Perihal</dt>
                                            <dd class="mt-1 text-sm text-gray-900" id="detail-perihal">Loading...</dd>
                        </div>
                                        <div class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">Perusahaan</dt>
                                            <dd class="mt-1 text-sm text-gray-900" id="detail-perusahaan">Loading...</dd>
                        </div>
                                        <div class="sm:col-span-2">
                                            <dt class="text-sm font-medium text-gray-500">File Surat</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <div id="detail-files-list" class="space-y-2"></div>
                                                <span id="no-file-text" class="text-gray-500 text-sm">Tidak ada file</span>
                                            </dd>
                                </div>
                                    </dl>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-base font-semibold text-gray-800 mb-3">
                                    <i class="ri-user-line mr-1 text-gray-600"></i>
                                    Informasi Disposisi
                                </h4>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                    <dl class="grid grid-cols-1 gap-y-3">
                                        <div class="flex justify-between items-center">
                                            <dt class="text-sm font-medium text-gray-500">Status Sekretaris</dt>
                                            <dd class="text-sm text-right" id="detail-status-sekretaris">Loading...</dd>
                                </div>
                                        <div class="flex justify-between items-center">
                                            <dt class="text-sm font-medium text-gray-500">Status Direktur</dt>
                                            <dd class="text-sm text-right" id="detail-status-dirut">Loading...</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <h4 class="text-base font-semibold text-gray-800 mb-3">
                                <i class="ri-chat-quote-line mr-1 text-gray-600"></i>
                                Keterangan
                            </h4>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 mb-6">
                                <dl class="grid grid-cols-1 gap-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan Pengirim</dt>
                                        <dd class="text-sm text-gray-700 bg-white p-3 rounded border border-gray-200" id="detail-keterangan-pengirim">Loading...</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan Sekretaris</dt>
                                        <dd class="text-sm text-gray-700 bg-white p-3 rounded border border-gray-200" id="detail-keterangan-sekretaris">Loading...</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan Direktur</dt>
                                        <dd class="text-sm text-gray-700 bg-white p-3 rounded border border-gray-200" id="detail-keterangan-dirut">Loading...</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Tujuan Disposisi -->
                        <div>
                            <h4 class="text-base font-semibold text-gray-800 mb-3">
                                <i class="ri-share-forward-line mr-1 text-gray-600"></i>
                                Tujuan Disposisi
                            </h4>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <div id="detail-tujuan-disposisi" class="text-sm text-gray-700">
                                    Loading...
                                </div>
                            </div>
                            </div>
                        </div>

                    <!-- Tab Preview File (di dalam modal detail surat keluar) -->
                    <div id="preview-tab-content" class="hidden px-6 py-4">
                        <h4 class="text-base font-semibold text-gray-800 mb-3">
                            <i class="ri-eye-line mr-1 text-gray-600"></i>
                            Preview File Surat
                        </h4>
                        <div id="preview-files-list" class="space-y-2 mb-4"></div>
                        <div id="preview-file-container" class="w-full">
                            <iframe id="pdf-preview" class="w-full h-[600px] hidden border-0"></iframe>
                            <img id="img-preview" class="w-full h-auto hidden border-0" style="max-height:600px;margin:auto;" />
                            <div id="error-preview" class="flex flex-col justify-center items-center h-full w-full hidden">
                                <i class="ri-error-warning-line text-5xl text-red-600"></i>
                                <p class="mt-4 text-gray-600 font-medium">File tidak dapat ditampilkan</p>
                                <p class="mt-1 text-gray-500">Silahkan download file untuk melihatnya</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" id="close-detail-modal-btn"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-lg">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview File (Ukuran Dioptimalkan & Tinggi Penuh) -->
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
                    <img id="img-preview" class="w-full h-auto hidden border-0" src="" style="max-height: 700px; margin:auto;" />
                    <div id="error-preview" class="flex flex-col justify-center items-center h-full w-full hidden">
                        <i class="ri-error-warning-line text-5xl text-red-600"></i>
                        <p class="mt-4 text-gray-600 font-medium">File tidak dapat ditampilkan</p>
                        <p class="mt-1 text-gray-500">Silahkan download file untuk melihatnya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Disposisi -->
    <div id="edit-disposisi-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Edit Disposisi Surat
                    </h3>
                    <!-- Formulir Disposisi -->
                    <form id="edit-disposisi-form" class="space-y-4">
                        <!-- Input tersembunyi untuk mencatat ID surat dan disposisi -->
                        <input type="hidden" id="surat_id" name="surat_id" value="">
                        <input type="hidden" id="disposisi_id" name="disposisi_id" value="">

                        <!-- Role Sekretaris -->
                        <div id="sekretaris-section" class="space-y-4 mb-6 border-b border-gray-200 pb-6">
                            <h4 class="text-md font-medium text-gray-800">Bagian Sekretaris</h4>

                            <div>
                                <label for="status_sekretaris"
                                    class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status_sekretaris" name="status_sekretaris"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                                    <option value="pending">Menunggu</option>
                                    <option value="review">Sedang Ditinjau</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>

                            <div>
                                <label for="waktu_review_sekretaris"
                                    class="block text-sm font-medium text-gray-700 mb-1">Waktu Review</label>
                                <input type="datetime-local" id="waktu_review_sekretaris" name="waktu_review_sekretaris"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                            </div>

                            <div>
                                <label for="keterangan_sekretaris"
                                    class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                <textarea id="keterangan_sekretaris" name="keterangan_sekretaris" rows="3"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                                    placeholder="Tambahkan catatan disposisi..."></textarea>
                            </div>
                        </div>

                        <!-- Keterangan Pengirim (tambahan) -->
                        <div class="space-y-4 mb-6 border-b border-gray-200 pb-6">
                            <h4 class="text-md font-medium text-gray-800">Keterangan Pengirim</h4>
                            <div>
                                <label for="keterangan_pengirim"
                                    class="block text-sm font-medium text-gray-700 mb-1">Catatan Pengirim</label>
                                <textarea id="keterangan_pengirim" name="keterangan_pengirim" rows="3"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                                    placeholder="Tambahkan catatan atau instruksi untuk penerima disposisi..."></textarea>
                            </div>
                        </div>

                        <!-- Role Direktur -->
                        <div id="direktur-section" class="space-y-4 mb-6 border-b border-gray-200 pb-6">
                            <h4 class="text-md font-medium text-gray-800">Bagian Direktur</h4>

                            <div>
                                <label for="status_dirut"
                                    class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status_dirut" name="status_dirut"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                                    <option value="pending">Menunggu</option>
                                    <option value="review">Sedang Ditinjau</option>
                                    <option value="approved">Disetujui</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>

                            <div>
                                <label for="waktu_review_dirut"
                                    class="block text-sm font-medium text-gray-700 mb-1">Waktu Review</label>
                                <input type="datetime-local" id="waktu_review_dirut" name="waktu_review_dirut"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                            </div>

                            <div>
                                <label for="keterangan_dirut" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                                <textarea id="keterangan_dirut" name="keterangan_dirut" rows="3"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                                    placeholder="Tambahkan catatan disposisi..."></textarea>
                            </div>
                            </div>

                        <!-- Tujuan Disposisi -->
                        <div id="tujuan-disposisi-section" class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Disposisi</label>
                                <div id="tujuan-disposisi-container" class="space-y-2">
                                    <!-- Checkbox tujuan disposisi akan diisi dinamis lewat JS -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="save-disposisi-button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" id="close-edit-disposisi-modal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk mengelola data dan interaksi -->
    <script>
        // Definisikan variabel global
        let suratData = [];
        let currentSuratId = null; // Keep track of current surat ID for preview
        let currentPage = 1;
        const itemsPerPage = 10;
        let totalPages = 0;

        document.addEventListener('DOMContentLoaded', function() {
            // Variabel untuk menyimpan data dan state
            let currentPage = 1;
            let itemsPerPage = 10;
            let totalPages = 0;

            // Fungsi untuk mengecek role user
            const userRole = {{ auth()->user()->role ?? 'null' }}; // Role 1: Sekretaris, Role 2: Direktur

            // Fungsi helper untuk status
            function getStatusClass(status) {
                switch (status) {
                    case 'pending':
                        return 'bg-yellow-100 text-yellow-800';
                    case 'review':
                        return 'bg-blue-100 text-blue-800';
                    case 'approved':
                        return 'bg-green-100 text-green-800';
                    case 'rejected':
                        return 'bg-red-100 text-red-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
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

            // Fungsi untuk menampilkan status dalam format HTML badge
            function getStatusHTML(status) {
                try {
                    return `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(status)}">
                        ${getStatusLabel(status)}
                    </span>`;
                } catch (error) {
                    console.error('Error rendering status HTML:', error, status);
                    return `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        ${status || 'Tidak Ada Status'}
                    </span>`;
                }
            }

            // Get current filters
            function getFilters() {
                return {
                    search: document.getElementById('search').value.trim(),
                    start_date: document.getElementById('start_date').value,
                    end_date: document.getElementById('end_date').value,
                    jenis_surat: document.getElementById('jenis_surat').value,
                    sifat_surat: document.getElementById('sifat_surat').value,
                    perusahaan: document.getElementById('perusahaan').value,
                    status_sekretaris: userRole !== 2 ? document.getElementById('filter_status_sekretaris').value :
                        'approved',
                    status_dirut: document.getElementById('filter_status_dirut').value
                };
            }

            // Apply filters
            function applyFilters() {
                currentPage = 1;
                loadSuratKeluar();
            }

            // Event listener untuk tombol filter
            document.getElementById('btn-filter').addEventListener('click', applyFilters);

            // Event listeners untuk auto-filter
            ['search', 'start_date', 'end_date', 'jenis_surat', 'sifat_surat',
                'perusahaan', 'filter_status_sekretaris', 'filter_status_dirut'
            ].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    // Untuk dropdown, gunakan event change
                    if (element.tagName === 'SELECT') {
                        element.addEventListener('change', applyFilters);
                    }
                    // Untuk input tanggal, gunakan event change
                    else if (element.type === 'date') {
                        element.addEventListener('change', applyFilters);
                    }
                    // Untuk input pencarian, gunakan event keyup dengan debounce
                    else if (id === 'search') {
                        let debounceTimer;
                        element.addEventListener('keyup', function() {
                            clearTimeout(debounceTimer);
                            debounceTimer = setTimeout(applyFilters, 500); // Delay 500ms
                        });
                    }
                }
            });

            // Tambahkan fungsi filterSurat di bawah deklarasi suratData, sebelum renderTable
            function filterSurat(data) {
                const searchQuery = document.getElementById('search').value.toLowerCase().trim();
                if (!searchQuery) return data;
                return data.filter(surat => {
                    const noDisposisi = (surat.disposisi && surat.disposisi.id ? surat.disposisi.id : '-').toString().toLowerCase();
                    const tanggal = (surat.tanggal_surat ? new Date(surat.tanggal_surat).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '').toLowerCase();
                    const nomorSurat = (surat.nomor_surat || '').toLowerCase();
                    const namaPerusahaan = (surat.perusahaanData && surat.perusahaanData.nama_perusahaan ? surat.perusahaanData.nama_perusahaan : (surat.nama_perusahaan ? surat.nama_perusahaan : (surat.perusahaan ? surat.perusahaan : '-'))).toLowerCase();
                    const perihal = (surat.perihal || '').toLowerCase();
                    const jenis = (surat.jenis_surat === 'internal' ? 'internal' : 'eksternal');
                    const pembuat = (surat.creator ? surat.creator.name : '').toLowerCase();
                    const jabatan = (surat.creator && surat.creator.jabatan ? surat.creator.jabatan : '').toLowerCase();
                    const statusSekretaris = (surat.disposisi && surat.disposisi.status_sekretaris ? surat.disposisi.status_sekretaris : 'pending').toLowerCase();
                    const statusDirut = (surat.disposisi && surat.disposisi.status_dirut ? surat.disposisi.status_dirut : 'pending').toLowerCase();
                    const tujuanDisposisi = (surat.disposisi && surat.disposisi.tujuan && surat.disposisi.tujuan.length > 0 ? surat.disposisi.tujuan.map(t => t.name).join(' ').toLowerCase() : '');
                    return (
                        noDisposisi.includes(searchQuery) ||
                        tanggal.includes(searchQuery) ||
                        nomorSurat.includes(searchQuery) ||
                        namaPerusahaan.includes(searchQuery) ||
                        perihal.includes(searchQuery) ||
                        jenis.includes(searchQuery) ||
                        pembuat.includes(searchQuery) ||
                        jabatan.includes(searchQuery) ||
                        statusSekretaris.includes(searchQuery) ||
                        statusDirut.includes(searchQuery) ||
                        tujuanDisposisi.includes(searchQuery)
                    );
                });
            }

            // Fungsi untuk memuat data surat keluar dengan filter
            async function loadSuratKeluar() {
                console.log('Loading surat keluar...');
                console.log('User Role:', userRole);
                console.log('User ID:', {{ auth()->id() }});

                const tableBody = document.getElementById('surat-table-body');

                // Tampilkan loading state
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                                <p class="mt-2 text-sm text-gray-500">Memuat data...</p>
                            </div>
                        </td>
                    </tr>
                `;

                try {
                    // Dapatkan filter yang aktif
                    const filters = getFilters();
                    console.log('Active filters:', filters);

                    // Buat query string dari filter
                    const queryString = Object.entries(filters)
                        .filter(([_, value]) => value) // Hanya ambil yang ada nilainya
                        .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
                        .join('&');

                    console.log('Query string:', queryString);

                    // Fetch data dengan filter
                    const response = await fetch(`/api/surat-keluar?${queryString}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                    console.log('Received data:', data);

                    // Filter data berdasarkan role - don't sort, preserve server-side sorting
                    let filteredData = data;
                    if (userRole === 0 || userRole === 1 || userRole === 3) { // Staff, Sekretaris, Admin
                        console.log('Filtering data for Staff/Sekretaris/Admin role');
                        filteredData = data.filter(surat => surat.created_by === {{ auth()->id() }});
                    }

                    console.log('Filtered data:', filteredData);
                    suratData = filteredData; // Simpan ke variabel global

                    if (filteredData.length === 0) {
                        console.log('No data found');
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center py-8">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="ri-inbox-line text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-gray-500">Tidak ada data yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    // Pada loadSuratKeluar, setelah dapatkan filteredData, sebelum renderTable, tambahkan:
                    const finalFilteredData = filterSurat(filteredData);
                    renderTable(finalFilteredData);
                    return;

                } catch (error) {
                    console.error('Error loading data:', error);
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
                }
            }

            // Render table from data
            function renderTable(data) {
                const tableBody = document.getElementById('surat-table-body');
                let html = '';

                // Pagination logic
                const total = data.length;
                totalPages = Math.ceil(total / itemsPerPage);
                if (currentPage > totalPages) currentPage = totalPages || 1;
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageData = data.slice(start, end);

                // Update info
                document.getElementById('page-start').textContent = total === 0 ? 0 : start + 1;
                document.getElementById('page-end').textContent = Math.min(end, total);
                document.getElementById('total-items').textContent = total;

                if (pageData.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ri-inbox-line text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada data yang ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    renderPagination();
                    return;
                }

                pageData.forEach(surat => {
                    // Format tanggal
                    const date = new Date(surat.tanggal_surat);
                    const tanggal = date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    // Ambil status dari disposisi
                    const statusSekretaris = surat.disposisi ? surat.disposisi.status_sekretaris : 'pending';
                    const statusDirut = surat.disposisi ? surat.disposisi.status_dirut : 'pending';

                    // Ambil No Disposisi
                    const noDisposisi = surat.disposisi && surat.disposisi.id ? surat.disposisi.id : '-';

                    // Render tujuan disposisi
                    let tujuanDisposisiHtml = '';
                    if (surat.disposisi && surat.disposisi.tujuan && surat.disposisi.tujuan.length > 0) {
                        tujuanDisposisiHtml = `
                            <div class="flex flex-wrap gap-1">
                                ${surat.disposisi.tujuan.slice(0, 2).map(tujuan => 
                                    `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="ri-user-line mr-1 text-blue-600"></i>
                                        ${tujuan.name}
                                    </span>`
                                ).join('')}
                                ${surat.disposisi.tujuan.length > 2 ? 
                                    `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        +${surat.disposisi.tujuan.length - 2}
                                    </span>` : 
                                    ''}
                            </div>
                        `;
                    } else {
                        tujuanDisposisiHtml = `
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="ri-user-unfollow-line mr-1"></i>
                                Tidak ada
                            </span>
                        `;
                    }

                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${noDisposisi}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${tanggal}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${surat.nomor_surat}
                                ${surat.created_by === {{ auth()->id() }} ? 
                                    '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Anda</span>' 
                                    : ''}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${(surat.perusahaanData && surat.perusahaanData.nama_perusahaan) ? surat.perusahaanData.nama_perusahaan : (surat.nama_perusahaan ? surat.nama_perusahaan : (surat.perusahaan ? surat.perusahaan : '-'))}
                            </td>
                            <td class="px-2 py-2 text-sm text-gray-500 max-w-xs truncate">
                                ${surat.perihal}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${surat.jenis_surat === 'internal' ? 'bg-sky-100 text-sky-800' : 'bg-fuchsia-100 text-fuchsia-800'}">
                                    ${surat.jenis_surat === 'internal' ? 'Internal' : 'Eksternal'}
                                </span>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                ${surat.creator ? surat.creator.name : 'Unknown'}
                                ${surat.creator?.jabatan ? 
                                    `<span class="text-xs text-gray-400">(${surat.creator.jabatan})</span>` 
                                    : ''}
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
                            <td class="px-2 py-2 text-sm text-gray-500">
                                ${tujuanDisposisiHtml}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap gap-2">
                                    <button onclick="showDetail(${surat.id})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded-md bg-green-50 hover:bg-green-100 transition-colors flex items-center" title="Lihat Detail">
                                        <i class="ri-eye-line mr-1"></i> Detail
                                    </button>
                                    ${surat.created_by === {{ auth()->id() }} ? `
                                        <a href="/suratkeluar/${surat.id}/edit" class="text-yellow-600 hover:text-yellow-800 px-2 py-1 rounded-md bg-yellow-50 hover:bg-yellow-100 transition-colors flex items-center" title="Edit Surat">
                                            <i class="ri-edit-line mr-1"></i> Edit
                                        </a>
                                        <button onclick="deleteSurat(${surat.id})" class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md bg-red-50 hover:bg-red-100 transition-colors flex items-center" title="Hapus Surat">
                                            <i class="ri-delete-bin-line mr-1"></i> Hapus
                                            </button>
                                        ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;
                renderPagination();
            }

            // Render pagination
            function renderPagination() {
                const container = document.getElementById('pagination-container');
                let html = '';
                // Previous button
                html += `
                    <button onclick="changePage(${Math.max(1, currentPage - 1)})" 
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                `;
                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (
                        i === 1 ||
                        i === totalPages ||
                        (i >= currentPage - 1 && i <= currentPage + 1)
                    ) {
                        html += `
                            <button onclick="changePage(${i})" 
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ${currentPage === i ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50'}">
                                ${i}
                            </button>
                        `;
                    } else if (
                        i === currentPage - 2 ||
                        i === currentPage + 2
                    ) {
                        html += `
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                ...
                            </span>
                        `;
                    }
                }
                // Next button
                html += `
                    <button onclick="changePage(${Math.min(totalPages, currentPage + 1)})" 
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                `;
                container.innerHTML = html;
                // Update mobile pagination buttons
                document.getElementById('prev-page-mobile').onclick = () => changePage(Math.max(1, currentPage - 1));
                document.getElementById('next-page-mobile').onclick = () => changePage(Math.min(totalPages, currentPage + 1));
            }

            // Change page
            window.changePage = function(page) {
                currentPage = page;
                renderTable(suratData);
                renderPagination();
                window.scrollTo(0, 0);
            };

            // Show detail modal
            window.showDetail = function(id) {
                const surat = suratData.find(s => s.id === id);
                if (!surat) return;

                // Store the current surat ID for preview tab
                currentSuratId = id;

                // Tampilkan loading state
                document.getElementById('detail-keterangan-pengirim').textContent = 'Memuat...';
                document.getElementById('detail-keterangan-sekretaris').textContent = 'Memuat...';
                document.getElementById('detail-keterangan-dirut').textContent = 'Memuat...';

                // Ambil data disposisi
                fetch(`/api/disposisi/surat/${surat.id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute(
                                    'content')
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.disposisi) {
                            // Set keterangan dari disposisi
                            document.getElementById('detail-keterangan-pengirim').textContent =
                                data.disposisi.keterangan_pengirim || '-';
                            document.getElementById('detail-keterangan-sekretaris')
                                .textContent =
                                data.disposisi.keterangan_sekretaris || '-';
                            document.getElementById('detail-keterangan-dirut').textContent =
                                data.disposisi.keterangan_dirut || '-';

                            // Set status dengan pengecekan nilai yang valid
                            document.getElementById('detail-status-sekretaris').innerHTML =
                                getStatusHTML(data.disposisi.status_sekretaris || 'pending');
                            document.getElementById('detail-status-dirut').innerHTML =
                                getStatusHTML(data.disposisi.status_dirut || 'pending');
                        } else {
                            // Jika tidak ada disposisi, tampilkan strip
                            document.getElementById('detail-keterangan-pengirim').textContent =
                                '-';
                            document.getElementById('detail-keterangan-sekretaris')
                                .textContent = '-';
                            document.getElementById('detail-keterangan-dirut').textContent =
                                '-';

                            // Set status default untuk disposisi yang tidak ada
                            document.getElementById('detail-status-sekretaris').innerHTML =
                                getStatusHTML('pending');
                            document.getElementById('detail-status-dirut').innerHTML =
                                getStatusHTML('pending');
                        }

                        // Ambil data tujuan disposisi jika ada disposisi
                        if (data.success && data.disposisi && data.disposisi.id) {
                            fetchTujuanDisposisi(data.disposisi.id);
                        } else {
                            // Tampilkan informasi tidak ada tujuan disposisi
                            document.getElementById('detail-tujuan-disposisi').innerHTML =
                                '<p class="text-gray-500">Tidak ada data tujuan disposisi</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching disposisi:', error);
                        // Jika terjadi error, tampilkan pesan error
                        document.getElementById('detail-keterangan-pengirim').textContent =
                            'Error memuat data';
                        document.getElementById('detail-keterangan-sekretaris').textContent =
                            'Error memuat data';
                        document.getElementById('detail-keterangan-dirut').textContent =
                            'Error memuat data';

                        // Tampilkan status error
                        document.getElementById('detail-status-sekretaris').innerHTML =
                            '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Error</span>';
                        document.getElementById('detail-status-dirut').innerHTML =
                            '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Error</span>';

                        // Tampilkan error tujuan disposisi
                        document.getElementById('detail-tujuan-disposisi').innerHTML =
                            '<p class="text-red-500">Error memuat data tujuan disposisi</p>';
                    });

                // Isi informasi surat lainnya
                document.getElementById('detail-nomor').textContent = surat.nomor_surat;
                document.getElementById('detail-tanggal').textContent = new Date(surat
                        .tanggal_surat)
                    .toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                document.getElementById('detail-jenis').textContent =
                    surat.jenis_surat === 'internal' ? 'Internal' : 'Eksternal';
                document.getElementById('detail-sifat').textContent =
                    surat.sifat_surat === 'urgent' ? 'Urgent' : 'Normal';
                document.getElementById('detail-perihal').textContent = surat.perihal;

                // Display company information
                const perusahaanElement = document.getElementById('detail-perusahaan');
                if (surat.perusahaanData && surat.perusahaanData.nama_perusahaan) {
                    // If we have the full company object with name
                    perusahaanElement.textContent = surat.perusahaanData.nama_perusahaan;
                    console.log('Using perusahaanData:', surat.perusahaanData.nama_perusahaan);
                } else if (surat.nama_perusahaan) {
                    // If we have the company name directly
                    perusahaanElement.textContent = surat.nama_perusahaan;
                    console.log('Using nama_perusahaan:', surat.nama_perusahaan);
                } else if (surat.perusahaan) {
                    // If we only have the company code or name directly
                    perusahaanElement.textContent = surat.perusahaan;
                    console.log('Using direct perusahaan value:', surat.perusahaan);
                } else {
                    // Default for internal letters or missing data
                    perusahaanElement.textContent = surat.jenis_surat === 'internal' ? 'RSAZRA' : '-';
                }

                // Set download link if element exists
                const detailFileLink = document.getElementById('detail-file-link');
                if (detailFileLink) {
                if (surat.file_path) {
                        detailFileLink.href = `/suratkeluar/${id}/download`;
                        detailFileLink.style.display = 'inline-flex';
                        document.getElementById('no-file-text').style.display = 'none';
                } else {
                        detailFileLink.style.display = 'none';
                        document.getElementById('no-file-text').style.display = 'inline';
                    }
                }

                // Reset to detail tab
                document.getElementById('detail-tab-content').classList.remove('hidden');
                document.getElementById('preview-tab-content').classList.add('hidden');
                document.getElementById('detail-tab-btn').classList.add('text-green-600', 'border-green-500');
                document.getElementById('detail-tab-btn').classList.remove('text-gray-500', 'border-transparent');
                document.getElementById('preview-tab-btn').classList.remove('text-green-600', 'border-green-500');
                document.getElementById('preview-tab-btn').classList.add('text-gray-500', 'border-transparent');

                // Tampilkan modal
                const modal = document.getElementById('detail-modal');
                modal.classList.remove('hidden');

                // Render file list
                renderDetailFiles(surat.files || []);
            };

            // Fungsi untuk mengambil data tujuan disposisi
            function fetchTujuanDisposisi(disposisiId) {
                fetch(`/api/disposisi/${disposisiId}/tujuan`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Data tujuan disposisi dari API:', data);
                        console.group('Debug Tujuan Disposisi');

                        if (data.success && data.tujuan && data.tujuan.length > 0) {
                            console.log('Jumlah tujuan:', data.tujuan.length);

                            // Debug setiap user
                            data.tujuan.forEach((user, index) => {
                                console.group(`User #${index+1}: ${user.name}`);
                                console.log('User object:', user);
                                console.log('Object keys:', Object.keys(user));

                                // Check bagaimana jabatan disediakan dalam respons
                                const jabatanOptions = [{
                                        key: 'jabatan',
                                        value: user.jabatan
                                    },
                                    {
                                        key: 'jabatan.nama_jabatan',
                                        value: user.jabatan?.nama_jabatan
                                    },
                                    {
                                        key: 'jabatan_name',
                                        value: user.jabatan_name
                                    },
                                    {
                                        key: 'jabatan_nama',
                                        value: user.jabatan_nama
                                    },
                                    {
                                        key: 'nama_jabatan',
                                        value: user.nama_jabatan
                                    }
                                ];

                                console.table(jabatanOptions);
                                console.groupEnd();
                            });
                        } else {
                            console.log('Tidak ada data tujuan yang tersedia');
                        }

                        console.groupEnd();

                        if (data.success && data.tujuan && data.tujuan.length > 0) {
                            let tujuanHTML = '<div class="space-y-2">';

                            data.tujuan.forEach(user => {
                                // Cek berbagai properti yang mungkin berisi informasi jabatan
                                let jabatan = '';

                                // Kasus khusus untuk Direktur Utama
                                if (user.name === 'Direktur Utama' || user.name.toLowerCase().includes(
                                        'direktur')) {
                                    jabatan = user.name; // Gunakan nama user sebagai jabatan
                                }
                                // Gunakan berbagai properti yang mungkin berisi informasi jabatan
                                else {
                                    jabatan = user.jabatan ||
                                        user.jabatan_name ||
                                        (user.jabatan_data ? user.jabatan_data.nama_jabatan : '');
                                }

                                tujuanHTML += `
                                    <div class="flex items-center space-x-2 p-2 bg-blue-50 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <span class="inline-block h-8 w-8 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center">
                                                <i class="ri-user-line"></i>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">${user.name}</p>
                                    ${jabatan ? 
                                                `<p class="text-xs text-gray-500 truncate">${jabatan}</p>` : 
                                                '<p class="text-xs text-gray-400 italic">Tanpa Jabatan</p>'}
                                        </div>
                                    </div>`;
                            });

                            tujuanHTML += '</div>';
                            document.getElementById('detail-tujuan-disposisi').innerHTML = tujuanHTML;
                        } else {
                            document.getElementById('detail-tujuan-disposisi').innerHTML =
                                '<div class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg">' +
                                '<span class="inline-block h-10 w-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center mb-2">' +
                                '<i class="ri-user-unfollow-line"></i></span>' +
                                '<p class="text-gray-500 text-sm">Belum ada tujuan disposisi</p></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching tujuan disposisi:', error);
                        document.getElementById('detail-tujuan-disposisi').innerHTML =
                            '<div class="flex flex-col items-center justify-center p-4 bg-red-50 rounded-lg">' +
                            '<span class="inline-block h-10 w-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center mb-2">' +
                            '<i class="ri-error-warning-line"></i></span>' +
                            '<p class="text-red-500 text-sm">Gagal memuat data tujuan disposisi</p></div>';
                    });
            }

            // Close detail modal
            document.getElementById('close-detail-modal').addEventListener('click', function() {
                document.getElementById('detail-modal').classList.add('hidden');
            });

            // Close detail modal with button
            document.getElementById('close-detail-modal-btn').addEventListener('click', function() {
                document.getElementById('detail-modal').classList.add('hidden');
            });

            // Handle tab switching in detail modal
            document.getElementById('detail-tab-btn').addEventListener('click', function() {
                // Show detail tab, hide preview tab
                document.getElementById('detail-tab-content').classList.remove('hidden');
                document.getElementById('preview-tab-content').classList.add('hidden');
                
                // Update active tab styling
                this.classList.add('text-green-600', 'border-green-500');
                this.classList.remove('text-gray-500', 'border-transparent');
                document.getElementById('preview-tab-btn').classList.remove('text-green-600', 'border-green-500');
                document.getElementById('preview-tab-btn').classList.add('text-gray-500', 'border-transparent');
            });
            
            document.getElementById('preview-tab-btn').addEventListener('click', function() {
                // Show preview tab, hide detail tab
                document.getElementById('detail-tab-content').classList.add('hidden');
                document.getElementById('preview-tab-content').classList.remove('hidden');
                
                // Update active tab styling
                this.classList.add('text-green-600', 'border-green-500');
                this.classList.remove('text-gray-500', 'border-transparent');
                document.getElementById('detail-tab-btn').classList.remove('text-green-600', 'border-green-500');
                document.getElementById('detail-tab-btn').classList.add('text-gray-500', 'border-transparent');
                
                // Load file preview if surat has file
                if (currentSuratId) {
                    loadFilePreviewInTab(currentSuratId);
                }
            });
            
            // Function to load file preview in the tab
            function loadFilePreviewInTab(id) {
                const surat = suratData.find(s => s.id === id);
                if (!surat) return;
                
                const filesList = document.getElementById('preview-files-list');
                filesList.innerHTML = '';
                
                if (!surat.files || surat.files.length === 0) {
                    filesList.innerHTML = '<span class="text-gray-500 text-sm">Tidak ada file</span>';
                    return;
                }
                
                surat.files.forEach((file, idx) => {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'flex items-center justify-between bg-gray-50 rounded p-2 border border-gray-200';
                    
                    // Tentukan ekstensi file untuk icon
                    const ext = file.file_path.split('.').pop().toLowerCase();
                    const iconClass = ['pdf'].includes(ext) ? 'ri-file-pdf-line text-red-500' : 
                            ['doc', 'docx'].includes(ext) ? 'ri-file-word-line text-blue-500' :
                            ['jpg', 'jpeg', 'png'].includes(ext) ? 'ri-image-line text-green-500' :
                            'ri-file-text-line text-gray-500';
                    
                    fileDiv.innerHTML = `
                        <div class="flex items-center">
                            <i class="${iconClass} text-xl mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">${file.original_name || file.file_path.split('/').pop()}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            ${['pdf', 'jpg', 'jpeg', 'png'].includes(ext) ? 
                                `<a href="/${file.file_path}" target="_blank" class="ml-2 text-xs text-blue-600 underline">Preview</a>` : ''}
                            <a href="/${file.file_path}" download class="px-2 py-1 text-xs font-medium text-green-600 bg-green-100 rounded hover:bg-green-200">
                                <i class="ri-download-line mr-1"></i> Download
                            </a>
                        </div>
                    `;
                    filesList.appendChild(fileDiv);
                });
                
                // Event listener untuk tombol preview file
                filesList.querySelectorAll('.inline-preview-file-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const idx = this.getAttribute('data-file-idx');
                        previewFileInline(surat, idx);
                    });
                });
            }

            // Delete surat
            window.deleteSurat = function(id) {
                if (confirm('Apakah Anda yakin ingin menghapus surat ini?')) {
                    // Tampilkan loading
                    const actionCell = document.querySelector(`button[onclick="deleteSurat(${id})"]`).closest('td');
                    const originalContent = actionCell.innerHTML;
                    actionCell.innerHTML = `<div class="flex justify-center"><i class="ri-loader-4-line animate-spin text-xl text-green-600"></i></div>`;
                    
                    fetch(`/suratkeluar/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Terjadi kesalahan saat menghapus surat');
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Tampilkan notifikasi sukses
                            const notification = document.createElement('div');
                            notification.className = 'fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 z-50 shadow-lg rounded';
                            notification.innerHTML = `
                                <div class="flex items-center">
                                    <i class="ri-checkbox-circle-line text-xl mr-2"></i>
                                    <p>${data.message || 'Surat berhasil dihapus'}</p>
                                </div>
                            `;
                            document.body.appendChild(notification);
                            
                            // Hapus notifikasi setelah 3 detik
                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                            
                            // Reload data
                            loadSuratKeluar();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            
                            // Kembalikan tampilan tombol
                            actionCell.innerHTML = originalContent;
                            
                            // Tampilkan notifikasi error
                            const notification = document.createElement('div');
                            notification.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 z-50 shadow-lg rounded';
                            notification.innerHTML = `
                                <div class="flex items-center">
                                    <i class="ri-error-warning-line text-xl mr-2"></i>
                                    <p>${error.message || 'Terjadi kesalahan saat menghapus surat'}</p>
                                </div>
                            `;
                            document.body.appendChild(notification);
                            
                            // Hapus notifikasi setelah 5 detik
                            setTimeout(() => {
                                notification.remove();
                            }, 5000);
                        });
                }
            };

            // Fungsi untuk preview file dalam modal
            window.previewFile = function(id) {
                const surat = suratData.find(s => s.id === id);
                if (!surat) return;

                // Set judul modal
                document.getElementById('preview-modal-title').textContent =
                    `Preview File: ${surat.nomor_surat}`;

                // Set download link
                document.getElementById('preview-download-link').href =
                    `/suratkeluar/${id}/download`;

                // Reset tampilan preview
                const pdfPreview = document.getElementById('pdf-preview');
                const loadingPreview = document.getElementById('loading-preview');
                const errorPreview = document.getElementById('error-preview');

                pdfPreview.classList.add('hidden');
                errorPreview.classList.add('hidden');
                loadingPreview.classList.remove('hidden');

                // Tampilkan modal
                document.getElementById('file-preview-modal').classList.remove('hidden');

                // Aktifkan keyboard shortcut Escape untuk menutup modal
                document.addEventListener('keydown', function escListener(e) {
                    if (e.key === 'Escape') {
                        document.getElementById('file-preview-modal').classList.add(
                            'hidden');
                        pdfPreview.src = '';
                        document.removeEventListener('keydown', escListener);
                    }
                });

                // Ambil file untuk preview
                fetch(`/suratkeluar/${id}/preview`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('File tidak dapat diambil');
                        }

                        // Cek tipe file dari Content-Type header
                        const contentType = response.headers.get('Content-Type');

                        // Jika PDF, tampilkan dalam iframe
                        if (contentType && contentType.includes('application/pdf')) {
                            return response.blob().then(blob => {
                                const url = URL.createObjectURL(blob);
                                pdfPreview.src = url;
                                pdfPreview.onload = function() {
                                    pdfPreview.classList.remove('hidden');
                                    loadingPreview.classList.add('hidden');
                                };
                            });
                        } else {
                            // Jika bukan PDF, tampilkan pesan error
                            loadingPreview.classList.add('hidden');
                            errorPreview.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading file:', error);
                        loadingPreview.classList.add('hidden');
                        errorPreview.classList.remove('hidden');
                    });
            };

            // Tutup modal preview
            document.getElementById('close-preview-modal').addEventListener('click', function() {
                document.getElementById('file-preview-modal').classList.add('hidden');
                document.getElementById('pdf-preview').src = '';
            });

            // Fungsi untuk edit disposisi
            window.editDisposisi = function(id) {
                const surat = suratData.find(s => s.id === id);
                if (!surat) return;

                // Reset form
                document.getElementById('edit-disposisi-form').reset();

                // Set ID surat dan disposisi
                document.getElementById('surat_id').value = surat.id;
                document.getElementById('disposisi_id').value = surat.disposisi ? surat.disposisi.id : '';

                // Tampilkan atau sembunyikan bagian form berdasarkan role
                const sekretarisSection = document.getElementById('sekretaris-section');
                const direkturSection = document.getElementById('direktur-section');
                const tujuanDisposisiSection = document.getElementById('tujuan-disposisi-section');

                // Default semua field disabled
                document.getElementById('status_sekretaris').disabled = true;
                document.getElementById('waktu_review_sekretaris').disabled = true;
                document.getElementById('keterangan_sekretaris').disabled = true;
                document.getElementById('status_dirut').disabled = true;
                document.getElementById('waktu_review_dirut').disabled = true;
                document.getElementById('keterangan_dirut').disabled = true;

                // Tampilkan loading indicator
                const loadingHtml =
                    '<div class="flex justify-center py-4"><i class="ri-loader-4-line animate-spin text-2xl text-green-600"></i></div>';
                sekretarisSection.innerHTML = loadingHtml;
                direkturSection.innerHTML = loadingHtml;

                // Ambil data disposisi dari API
                fetch(`/api/disposisi/surat/${surat.id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute(
                                    'content')
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response error text:', text);
                                throw new Error(
                                    `Error status ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data disposisi diterima:', data);

                        // Kembalikan konten asli section
                        sekretarisSection.innerHTML = `
                        <h4 class="text-md font-medium text-gray-800">Bagian Sekretaris</h4>
                        <div>
                            <label for="status_sekretaris" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status_sekretaris" name="status_sekretaris"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                                <option value="pending">Menunggu</option>
                                <option value="review">Sedang Ditinjau</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label for="waktu_review_sekretaris" class="block text-sm font-medium text-gray-700 mb-1">Waktu Review</label>
                            <input type="datetime-local" id="waktu_review_sekretaris" name="waktu_review_sekretaris"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                        </div>
                        <div>
                            <label for="keterangan_sekretaris" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea id="keterangan_sekretaris" name="keterangan_sekretaris" rows="3"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                                placeholder="Tambahkan catatan disposisi..."></textarea>
                        </div>
                    `;

                        direkturSection.innerHTML = `
                        <h4 class="text-md font-medium text-gray-800">Bagian Direktur</h4>
                        <div>
                            <label for="status_dirut" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status_dirut" name="status_dirut"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                                <option value="pending">Menunggu</option>
                                <option value="review">Sedang Ditinjau</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label for="waktu_review_dirut" class="block text-sm font-medium text-gray-700 mb-1">Waktu Review</label>
                            <input type="datetime-local" id="waktu_review_dirut" name="waktu_review_dirut"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all">
                        </div>
                        <div>
                            <label for="keterangan_dirut" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea id="keterangan_dirut" name="keterangan_dirut" rows="3"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                                placeholder="Tambahkan catatan disposisi..."></textarea>
                        </div>
                        <div id="tujuan-disposisi-section" class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Disposisi</label>
                            <div id="tujuan-disposisi-container" class="space-y-2">
                                <!-- Checkbox tujuan disposisi akan diisi dinamis lewat JS -->
                            </div>
                        </div>
                    `;

                        if (data.success) {
                            const disposisi = data.disposisi;
                            const isNew = data.is_new === true;
                            
                            // If this is a new disposisi that doesn't exist in the database yet
                            if (isNew) {
                                // Set the surat_id for creating a new disposisi
                                document.getElementById('surat_id').value = surat.id;
                                // Don't set disposisi ID as it doesn't exist yet
                                document.getElementById('disposisi_id').value = '';
                                
                                // Show notification if needed
                                if (data.message) {
                                    const notification = document.createElement('div');
                                    notification.className = 'bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4';
                                    notification.innerHTML = `
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="ri-information-line text-yellow-400"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">${data.message}</p>
                                            </div>
                                        </div>
                                    `;
                                    
                                    // Insert notification at the top of the modal
                                    const modalContent = document.getElementById('disposisi-form-content');
                                    modalContent.insertBefore(notification, modalContent.firstChild);
                                    
                                    // Auto-remove notification after 5 seconds
                                    setTimeout(() => {
                                        notification.remove();
                                    }, 5000);
                                }
                            } else {
                                // Set the disposisi ID for existing disposisi
                            document.getElementById('disposisi_id').value = disposisi.id;
                            }

                            // Isi nilai form dengan data disposisi
                            document.getElementById('status_sekretaris').value = disposisi
                                .status_sekretaris || 'pending';
                            document.getElementById('status_dirut').value = disposisi
                                .status_dirut ||
                                'pending';
                            document.getElementById('keterangan_sekretaris').value = disposisi
                                .keterangan_sekretaris || '';
                            document.getElementById('keterangan_dirut').value = disposisi
                                .keterangan_dirut || '';

                            // Format waktu review jika ada
                            if (disposisi.waktu_review_sekretaris) {
                                const waktuSekretaris = new Date(disposisi
                                    .waktu_review_sekretaris);
                                document.getElementById('waktu_review_sekretaris').value =
                                    waktuSekretaris.toISOString().slice(0,
                                        16); // Format YYYY-MM-DDTHH:MM
                            }

                            if (disposisi.waktu_review_dirut) {
                                const waktuDirut = new Date(disposisi.waktu_review_dirut);
                                document.getElementById('waktu_review_dirut').value =
                                    waktuDirut.toISOString().slice(0,
                                        16); // Format YYYY-MM-DDTHH:MM
                            }

                            // Set enabled fields berdasarkan role
                            if (userRole === 1) { // Sekretaris
                                document.getElementById('status_sekretaris').disabled = false;
                                document.getElementById('waktu_review_sekretaris').disabled =
                                    false;
                                document.getElementById('keterangan_sekretaris').disabled =
                                    false;

                                // Tampilkan hanya bagian sekretaris, sembunyikan bagian direktur
                                sekretarisSection.classList.remove('hidden');
                                direkturSection.classList.add('hidden');
                            } else if (userRole === 2) { // Direktur
                                document.getElementById('status_dirut').disabled = false;
                                document.getElementById('waktu_review_dirut').disabled = false;
                                document.getElementById('keterangan_dirut').disabled = false;

                                // Tampilkan hanya bagian direktur, sembunyikan bagian sekretaris
                                sekretarisSection.classList.add('hidden');
                                direkturSection.classList.remove('hidden');

                                // Selalu tampilkan bagian tujuan disposisi untuk Direktur
                                document.getElementById('tujuan-disposisi-section').classList
                                    .remove(
                                        'hidden');

                                // For a new disposisi, we need to handle tujuan differently
                                if (!isNew && disposisi.id) {
                                    // Only load tujuan for existing disposisis
                                loadDisposisiUsers(disposisi.id);
                                } else {
                                    // For new disposisi, we'll load available users differently
                                    loadAvailableUsers();
                                }
                            }

                            // Tampilkan modal
                            document.getElementById('edit-disposisi-modal').classList.remove(
                                'hidden');
                            
                            // Hide loading, show content
                            document.getElementById('disposisi-loading').classList.add('hidden');
                            document.getElementById('disposisi-form-content').classList.remove('hidden');
                        } else {
                            alert('Gagal memuat data disposisi: ' + (data.message ||
                                'Terjadi kesalahan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching disposisi:', error);
                        alert('Terjadi kesalahan saat memuat data disposisi');

                        // Kembalikan modal ke kondisi normal
                        sekretarisSection.innerHTML =
                            '<div class="py-4 text-red-500">Gagal memuat data sekretaris</div>';
                        direkturSection.innerHTML =
                            '<div class="py-4 text-red-500">Gagal memuat data direktur</div>';
                    });
            };

            // Fungsi untuk memuat daftar user tujuan disposisi
            function loadDisposisiUsers(disposisiId) {
                const container = document.getElementById('tujuan-disposisi-container');
                container.innerHTML =
                    '<div class="py-2 text-center text-gray-500"><i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Memuat data...</div>';

                // Debugging info
                console.log('Memuat data tujuan disposisi untuk ID:', disposisiId);

                fetch(`/api/disposisi/${disposisiId}/tujuan`, {
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
                        console.log('Data tujuan yang diterima:', data);

                        // Buat HTML untuk checkbox users yang tersedia
                        let html = '';

                        // Periksa apakah data memiliki struktur yang benar
                        if (data && data.available_users && Array.isArray(data.available_users)) {
                            const selectedUserIds = data.tujuan ? data.tujuan.map(t => t.id) : [];

                            data.available_users.forEach(user => {
                                const isChecked = selectedUserIds.includes(user.id);
                                html += `
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                        id="tujuan_${user.id}" 
                                        name="tujuan_disposisi[]" 
                                        value="${user.id}" 
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                        ${isChecked ? 'checked' : ''}>
                                    <label for="tujuan_${user.id}" class="ml-3 block text-sm text-gray-700">
                                        ${user.name} ${user.jabatan ? `(${user.jabatan})` : ''}
                                    </label>
                                </div>
                            `;
                            });
                        } else {
                            console.error('Data users tidak tersedia atau format tidak sesuai:',
                                data);
                            html =
                                '<div class="py-2 text-center text-yellow-500">Format data tidak valid</div>';
                        }

                        // Tampilkan hasil
                        if (html === '') {
                            container.innerHTML =
                                '<div class="py-2 text-center text-gray-500">Tidak ada pengguna yang tersedia</div>';
                        } else {
                            container.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Error memuat tujuan disposisi:', error);
                        container.innerHTML = `
                        <div class="py-2 text-center text-red-500">
                            Gagal memuat data tujuan disposisi
                            <button type="button" onclick="reloadTujuanDisposisi(${disposisiId})" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-xs">
                                Coba lagi
                            </button>
                        </div>
                    `;
                    });
            }

            // Function to load all available users for a new disposisi
            function loadAvailableUsers() {
                const container = document.getElementById('tujuan-disposisi-container');
                container.innerHTML =
                    '<div class="py-2 text-center text-gray-500"><i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Memuat data...</div>';

                // Debugging info
                console.log('Memuat data semua users untuk disposisi baru');

                fetch(`/api/users/disposisi`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        console.log('Status response users:', response.status);
                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response error text:', text);
                                throw new Error(`Error status ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data users yang diterima:', data);

                        // Buat HTML untuk checkbox users yang tersedia
                        let html = '';

                        // Periksa apakah data memiliki struktur yang benar
                        if (data && data.success && data.users && Array.isArray(data.users)) {
                            data.users.forEach(user => {
                                html += `
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                        id="tujuan_${user.id}" 
                                        name="tujuan_disposisi[]" 
                                        value="${user.id}" 
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="tujuan_${user.id}" class="ml-3 block text-sm text-gray-700">
                                        ${user.name}
                                    </label>
                                </div>
                            `;
                            });
                        } else {
                            console.error('Data users tidak tersedia atau format tidak sesuai:', data);
                            html = '<div class="py-2 text-center text-yellow-500">Format data tidak valid</div>';
                        }

                        // Tampilkan hasil
                        if (html === '') {
                            container.innerHTML =
                                '<div class="py-2 text-center text-gray-500">Tidak ada pengguna yang tersedia</div>';
                        } else {
                            container.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Error memuat users:', error);
                        container.innerHTML = `
                        <div class="py-2 text-center text-red-500">
                            Gagal memuat data users
                            <button type="button" onclick="loadAvailableUsers()" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-xs">
                                Coba lagi
                            </button>
                        </div>
                    `;
                    });
            }
            
            // Reload tujuan disposisi for an existing disposisi
            window.reloadTujuanDisposisi = function(disposisiId) {
                loadDisposisiUsers(disposisiId);
            };

            // Event listener untuk tombol simpan perubahan disposisi
            document.getElementById('save-disposisi-button').addEventListener('click', function() {
                const disposisiId = document.getElementById('disposisi_id').value;
                const suratId = document.getElementById('surat_id').value;
                
                // Validasi surat ID
                if (!suratId) {
                    alert('ID Surat tidak valid!');
                    return;
                }

                // Disable tombol untuk mencegah multiple submit
                this.disabled = true;
                this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Menyimpan...';

                // Ambil data dari form
                const formData = new FormData();
                formData.append('surat_keluar_id', suratId);
                
                // Ambil keterangan pengirim, pastikan valuenya diambil dengan benar
                const keteranganPengirim = document.getElementById('keterangan_pengirim').value.trim();
                formData.append('keterangan_pengirim', keteranganPengirim);

                console.log('Data keterangan yang akan dikirim:', {
                    surat_id: suratId,
                    keterangan: keteranganPengirim
                });

                if (userRole == 1) { // Sekretaris
                    const statusSekretaris = document.getElementById('status_sekretaris').value;
                    const keteranganSekretaris = document.getElementById('keterangan_sekretaris').value.trim();
                    const waktuReviewSekretaris = document.getElementById('waktu_review_sekretaris').value;
                    
                    formData.append('status_sekretaris', statusSekretaris);
                    formData.append('keterangan_sekretaris', keteranganSekretaris);
                    if (waktuReviewSekretaris) {
                        formData.append('waktu_review_sekretaris', waktuReviewSekretaris);
                    }
                } else if (userRole == 2) { // Direktur
                    const statusDirut = document.getElementById('status_dirut').value;
                    const keteranganDirut = document.getElementById('keterangan_dirut').value.trim();
                    const waktuReviewDirut = document.getElementById('waktu_review_dirut').value;
                    
                    formData.append('status_dirut', statusDirut);
                    formData.append('keterangan_dirut', keteranganDirut);
                    if (waktuReviewDirut) {
                        formData.append('waktu_review_dirut', waktuReviewDirut);
                    }

                    // Tujuan disposisi untuk direktur
                    const selected = [];
                    document.querySelectorAll('input[name="tujuan_disposisi[]"]:checked').forEach(checkbox => {
                        selected.push(checkbox.value);
                        formData.append('tujuan_disposisi[]', checkbox.value);
                    });
                }

                // Log semua data yang akan dikirim
                console.log(`${disposisiId ? 'Updating' : 'Creating new'} disposisi for surat ID: ${suratId}`);
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                
                // Set the endpoint URL based on whether this is a new disposisi or an existing one
                let endpoint;
                let method;
                
                if (disposisiId) {
                    // Update an existing disposisi
                    endpoint = `/api/disposisi/${disposisiId}/update`;
                    method = 'POST';
                } else {
                    // Create a new disposisi
                    endpoint = `/api/disposisi/store`;
                    method = 'POST';
                }

                // Send the request
                fetch(endpoint, {
                        method: method,
                        body: formData,
                        headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                    .then(data => {
                    console.log('Response from server:', data);
                    
                    if (data.success) {
                        // Tutup modal
                        document.getElementById('edit-disposisi-modal').classList.add('hidden');
                        
                        // Tampilkan pesan sukses
                        showAlert('success', data.message || 'Disposisi berhasil disimpan');
                        
                        // Reload data setelah 1 detik
                        setTimeout(() => {
                            loadSuratKeluar();
                        }, 1000);
                    } else {
                        showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan disposisi');
                    }
                    })
                    .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat mengirim data');
                    })
                    .finally(() => {
                    // Re-enable button
                        this.disabled = false;
                    this.innerHTML = '<i class="ri-save-line mr-1"></i> Simpan Perubahan';
                    });
            });

            // Event listener untuk tombol batal/tutup modal
            document.getElementById('close-edit-disposisi-modal').addEventListener('click', function() {
                document.getElementById('edit-disposisi-modal').classList.add('hidden');
            });

            // Sembunyikan filter status sekretaris untuk role 2
            if (userRole === 2) {
                const statusSekretarisFilter = document.getElementById('filter_status_sekretaris')
                    .closest(
                        'select');
                if (statusSekretarisFilter) {
                    statusSekretarisFilter.style.display = 'none';
                }
            }

            // Initial load
            loadSuratKeluar();

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
                            <div class=\"flex items-center space-x-2\">
                                <i class=\"ri-file-text-line text-lg text-green-500\"></i>
                                <span class=\"text-sm\">${file.original_name || file.file_path.split('/').pop()}</span>
                                <a href=\"/${file.file_path}\" target=\"_blank\" class=\"text-xs text-green-600 underline\">Download</a>
                                ${previewBtn}
                            </div>
                        `;
                    });
                    
                    // Event listener untuk tombol preview di daftar file
                    filesList.querySelectorAll('.modal-preview-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const idx = this.getAttribute('data-file-idx');
                            // Gunakan fullscreen preview modal
                            previewFileFullscreen(suratData.find(s => s.id === currentSuratId), idx);
                        });
                    });
                } else {
                    noFileText.style.display = 'inline';
                }
            }

            // Fungsi untuk preview file di dalam modal (inline)
            function previewFileInline(surat, idx) {
                const file = surat.files[idx];
                if (!file) return;
                
                const pdfPreview = document.getElementById('preview-tab-content').querySelector('#pdf-preview');
                const imgPreview = document.getElementById('preview-tab-content').querySelector('#img-preview');
                const errorPreview = document.getElementById('preview-tab-content').querySelector('#error-preview');
                
                // Reset tampilan
                pdfPreview.classList.add('hidden');
                imgPreview.classList.add('hidden');
                errorPreview.classList.add('hidden');
                
                // Tentukan tipe file berdasarkan ekstensi
                const ext = file.file_path.split('.').pop().toLowerCase();
                
                if (['pdf'].includes(ext)) {
                    // Untuk file PDF
                    pdfPreview.src = '/' + file.file_path;
                    pdfPreview.classList.remove('hidden');
                } else if (['jpg','jpeg','png'].includes(ext)) {
                    // Untuk file gambar
                    imgPreview.src = '/' + file.file_path;
                    imgPreview.classList.remove('hidden');
                } else {
                    // Format tidak didukung
                    errorPreview.classList.remove('hidden');
                }
            }
            
            // Fungsi untuk preview file dalam modal full screen
            function previewFileFullscreen(surat, idx) {
                console.log('Membuka preview file untuk surat:', surat.id, 'file index:', idx);
                
                if (!surat || !surat.files || !surat.files[idx]) {
                    console.error('File tidak ditemukan:', { suratId: surat?.id, fileIdx: idx });
                    alert('File tidak ditemukan');
                    return;
                }
                
                const file = surat.files[idx];
                console.log('File yang akan di-preview:', file);
                
                // Gunakan modal full screen untuk preview file
                document.getElementById('file-preview-modal').classList.remove('hidden');
                const fileName = file.original_name || file.file_path.split('/').pop();
                document.getElementById('preview-modal-title').textContent = 'Preview: ' + fileName;
                document.getElementById('preview-download-link').href = '/' + file.file_path;
                
                const pdfPreview = document.getElementById('file-preview-modal').querySelector('#pdf-preview');
                const imgPreview = document.getElementById('file-preview-modal').querySelector('#img-preview');
                const loadingPreview = document.getElementById('file-preview-modal').querySelector('#loading-preview');
                const errorPreview = document.getElementById('file-preview-modal').querySelector('#error-preview');
                
                // Reset tampilan
                pdfPreview.classList.add('hidden');
                imgPreview.classList.add('hidden');
                errorPreview.classList.add('hidden');
                loadingPreview.classList.remove('hidden');
                
                // Tentukan tipe file berdasarkan ekstensi
                const ext = file.file_path.split('.').pop().toLowerCase();
                console.log('File extension:', ext);
                
                if (['pdf'].includes(ext)) {
                    // Untuk file PDF
                    pdfPreview.src = '/' + file.file_path;
                    pdfPreview.onload = function() {
                        console.log('PDF loaded successfully');
                        loadingPreview.classList.add('hidden');
                        pdfPreview.classList.remove('hidden');
                    };
                    pdfPreview.onerror = function(error) {
                        console.error('Error loading PDF:', error);
                        loadingPreview.classList.add('hidden');
                        errorPreview.classList.remove('hidden');
                        errorPreview.textContent = 'Gagal memuat file PDF. Silakan coba download file.';
                    };
                    
                    // Fallback jika onload tidak dipanggil
                    setTimeout(() => {
                        if (!pdfPreview.classList.contains('hidden')) return;
                        loadingPreview.classList.add('hidden');
                        pdfPreview.classList.remove('hidden');
                    }, 1500);
                } else if (['jpg','jpeg','png'].includes(ext)) {
                    // Untuk file gambar
                    imgPreview.src = '/' + file.file_path;
                    imgPreview.onload = function() {
                        console.log('Image loaded successfully');
                        loadingPreview.classList.add('hidden');
                        imgPreview.classList.remove('hidden');
                    };
                    imgPreview.onerror = function(error) {
                        console.error('Error loading image:', error);
                        loadingPreview.classList.add('hidden');
                        errorPreview.classList.remove('hidden');
                        errorPreview.textContent = 'Gagal memuat file gambar. Silakan coba download file.';
                    };
                    
                    // Fallback jika onload tidak dipanggil
                    setTimeout(() => {
                        if (!imgPreview.classList.contains('hidden')) return;
                        loadingPreview.classList.add('hidden');
                        imgPreview.classList.remove('hidden');
                    }, 1500);
                } else {
                    // Format tidak didukung
                    console.log('Format file tidak didukung untuk preview:', ext);
                    loadingPreview.classList.add('hidden');
                    errorPreview.classList.remove('hidden');
                    errorPreview.textContent = 'Format file ini tidak didukung untuk preview. Silakan download file.';
                }
            }

            // Fungsi untuk mempersiapkan form edit disposisi
            function prepareEditDisposisiForm(surat, isNew = false) {
                console.log('Mempersiapkan form disposisi untuk surat ID: ' + surat.id, 'isNew:', isNew);
                
                const disposisiId = isNew ? '' : (surat.disposisi ? surat.disposisi.id : '');
                
                // Tampilkan modal
                document.getElementById('edit-disposisi-modal').classList.remove('hidden');
                
                // Set ID surat dan disposisi
                document.getElementById('surat_id').value = surat.id;
                document.getElementById('disposisi_id').value = disposisiId;
                
                // Tampilkan data surat yang dipilih
                document.getElementById('edit-disposisi-title').textContent = isNew ? 
                    'Buat Disposisi Baru' : 'Edit Disposisi';
                document.getElementById('edit-disposisi-info').innerHTML = `
                    <div class="text-sm"><span class="font-medium">Nomor Surat:</span> ${surat.nomor_surat}</div>
                    <div class="text-sm"><span class="font-medium">Perihal:</span> ${surat.perihal}</div>
                    <div class="text-sm"><span class="font-medium">Tanggal:</span> ${new Date(surat.tanggal_surat).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                `;
                
                // Reset form
                document.getElementById('edit-disposisi-form').reset();
                
                if (!isNew && surat.disposisi) {
                    const disposisi = surat.disposisi;
                    console.log('Data disposisi yang akan diisi ke form:', disposisi);
                    
                    // Set keterangan pengirim jika ada
                    if (disposisi.keterangan_pengirim) {
                        document.getElementById('keterangan_pengirim').value = disposisi.keterangan_pengirim;
                        console.log('Mengisi keterangan pengirim:', disposisi.keterangan_pengirim);
                    }
                    
                    // Set status sekretaris jika ada
                    if (disposisi.status_sekretaris) {
                        document.getElementById('status_sekretaris').value = disposisi.status_sekretaris;
                    }
                    
                    // Set keterangan sekretaris jika ada
                    if (disposisi.keterangan_sekretaris) {
                        document.getElementById('keterangan_sekretaris').value = disposisi.keterangan_sekretaris;
                    }
                    
                    // Set waktu review sekretaris jika ada
                    if (disposisi.waktu_review_sekretaris) {
                        document.getElementById('waktu_review_sekretaris').value = 
                            disposisi.waktu_review_sekretaris.replace(' ', 'T');
                    }
                    
                    // Set status dirut jika ada
                    if (disposisi.status_dirut) {
                        document.getElementById('status_dirut').value = disposisi.status_dirut;
                    }
                    
                    // Set keterangan dirut jika ada
                    if (disposisi.keterangan_dirut) {
                        document.getElementById('keterangan_dirut').value = disposisi.keterangan_dirut;
                    }
                    
                    // Set waktu review dirut jika ada
                    if (disposisi.waktu_review_dirut) {
                        document.getElementById('waktu_review_dirut').value = 
                            disposisi.waktu_review_dirut.replace(' ', 'T');
                    }
                }
            }

            // Fungsi untuk menampilkan modal edit disposisi
            window.openEditDisposisiModal = function(id) {
                const surat = suratData.find(s => s.id === parseInt(id));
                if (!surat) {
                    alert('Surat tidak ditemukan!');
                    return;
                }
                
                // Gunakan fungsi prepareEditDisposisiForm
                prepareEditDisposisiForm(surat, !surat.disposisi);
                
                // Periksa jika surat memiliki disposisi dan disposisi memiliki tujuan
                if (surat.disposisi && surat.disposisi.tujuan) {
                    loadDisposisiTujuan(surat.disposisi.id);
            } else {
                    // Jika tidak ada disposisi atau tidak ada tujuan, muat semua user
                    loadAvailableUsers();
                }
            };

            // Fungsi untuk menampilkan alert
            function showAlert(type, message) {
                const alertDiv = document.createElement('div');
                const isSuccess = type === 'success';
                
                alertDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg ${isSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} z-50 flex items-center`;
                
                alertDiv.innerHTML = `
                    <i class="${isSuccess ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'} text-xl mr-2"></i>
                    <span>${message}</span>
                `;
                
                document.body.appendChild(alertDiv);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    alertDiv.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => {
                        document.body.removeChild(alertDiv);
                    }, 500);
                }, 3000);
            }
        });

        // Close preview modal when button is clicked
        document.getElementById('close-preview-modal').addEventListener('click', function() {
            document.getElementById('file-preview-modal').classList.add('hidden');
            // Reset iframe source to avoid memory issues
            const pdfPreview = document.getElementById('file-preview-modal').querySelector('#pdf-preview');
            if (pdfPreview) pdfPreview.src = '';
        });
    </script>
@endsection

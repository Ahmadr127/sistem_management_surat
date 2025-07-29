<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
            <p class="text-sm text-gray-600 mt-1">Format: <span class="font-mono">{{ $format }}</span></p>
        </div>
        <button id="generate-btn-{{ $jenis }}" class="generate-btn inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
            <i class="ri-add-line mr-2"></i> Generate Nomor Baru
        </button>
    </div>

    <!-- Search & Pagination Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <div class="flex items-center gap-2">
            <input id="search-{{ $jenis }}" type="text" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Cari nomor/perihal/pengirim...">
            <button id="search-btn-{{ $jenis }}" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"><i class="ri-search-line"></i></button>
            <!-- Filter Dropdown Sort -->
            <div class="relative ml-2">
                <button id="filter-btn-{{ $jenis }}" type="button" class="px-2 py-2 bg-white border border-gray-200 rounded-md shadow-sm hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center" aria-haspopup="true" aria-expanded="false">
                    <i class="ri-filter-3-line text-lg mr-1"></i>
                    <span id="filter-label-{{ $jenis }}" class="text-xs text-gray-700">Terbaru</span>
                    <i class="ri-arrow-down-s-line ml-1 text-gray-400"></i>
                </button>
                <div id="filter-dropdown-{{ $jenis }}" class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                    <button type="button" class="filter-option block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50" data-sort="asc">Nomor Terkecil</button>
                    <button type="button" class="filter-option block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50" data-sort="desc">Nomor Terbesar</button>
                    <button type="button" class="filter-option block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50" data-sort="terbaru">Terbaru</button>
                    <button type="button" class="filter-option block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50" data-sort="terlama">Terlama</button>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Tampil</label>
            <select id="perpage-{{ $jenis }}" class="px-2 py-1 border border-gray-300 rounded-md">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-600">/ halaman</span>
        </div>
    </div>

    <!-- Generate Modal -->
    <div id="modal-{{ $jenis }}" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Nomor Surat Selanjutnya</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut Berikutnya</label>
                            <div id="next-nomor-{{ $jenis }}" class="text-2xl font-bold text-green-700">...</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format Nomor Surat Lengkap</label>
                            <div id="next-nomor-lengkap-{{ $jenis }}" class="text-lg font-mono text-gray-800">...</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                            <div id="next-tanggal-{{ $jenis }}" class="text-base text-gray-700">...</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                            <div id="next-kode-{{ $jenis }}" class="text-base text-gray-700">{{ $kode }}</div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button id="cancel-{{ $jenis }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perihal</th>
                    </tr>
                </thead>
                <tbody id="tbody-{{ $jenis }}" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan diisi secara dinamis -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4" id="pagination-{{ $jenis }}"></div>
    <!-- Empty State -->
    <div id="empty-{{ $jenis }}" class="hidden text-center py-12">
        <i class="ri-file-list-line text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada nomor surat</h3>
        <p class="text-gray-500 mb-4">Mulai dengan generate nomor surat pertama</p>
        <button id="empty-generate-btn-{{ $jenis }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700">
            <i class="ri-add-line mr-2"></i> Generate Nomor Pertama
        </button>
    </div>
</div>

<script>
(function() {
    const jenis = '{{ $jenis }}';
    const kode = '{{ $kode }}';
    const tbody = document.getElementById(`tbody-${jenis}`);
    const emptyState = document.getElementById(`empty-${jenis}`);
    const pagination = document.getElementById(`pagination-${jenis}`);
    const searchInput = document.getElementById(`search-${jenis}`);
    const searchBtn = document.getElementById(`search-btn-${jenis}`);
    const perPageSelect = document.getElementById(`perpage-${jenis}`);
    const modal = document.getElementById(`modal-${jenis}`);
    const generateBtn = document.getElementById(`generate-btn-${jenis}`);
    const cancelBtn = document.getElementById(`cancel-${jenis}`);
    const confirmBtn = document.getElementById(`confirm-${jenis}`);
    const tanggalInput = document.getElementById(`tanggal-${jenis}`);
    const nomorInput = document.getElementById(`nomor-${jenis}`);
    const emptyGenerateBtn = document.getElementById(`empty-generate-btn-${jenis}`);

    let currentPage = 1;
    let currentSearch = '';
    let currentPerPage = 10;
    let currentSort = 'terbaru';
    let lastData = [];

    // Helper untuk ekstrak nomor urut dari string nomor surat
    function extractNomorUrut(nomorSurat) {
        if (!nomorSurat) return 0;
        // Ambil bagian sebelum '/'
        const match = nomorSurat.match(/^(\d+)/);
        return match ? parseInt(match[1], 10) : 0;
    }
    // Helper untuk parse tanggal
    function parseTanggal(tgl) {
        if (!tgl) return 0;
        const d = new Date(tgl);
        return isNaN(d) ? 0 : d.getTime();
    }

    function renderTable(data) {
        lastData = data ? [...data] : [];
        // Sorting frontend sesuai currentSort
        if (Array.isArray(data) && data.length > 0) {
            data.sort(function(a, b) {
                if (currentSort === 'desc' || currentSort === 'asc') {
                    const na = extractNomorUrut(a.nomor_surat);
                    const nb = extractNomorUrut(b.nomor_surat);
                    return currentSort === 'desc' ? nb - na : na - nb;
                } else if (currentSort === 'terbaru' || currentSort === 'terlama') {
                    const ta = parseTanggal(a.tanggal_surat);
                    const tb = parseTanggal(b.tanggal_surat);
                    return currentSort === 'terbaru' ? tb - ta : ta - tb;
                }
                return 0;
            });
        }
        tbody.innerHTML = '';
        if (!data || data.length === 0) {
            tbody.parentElement.parentElement.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }
        tbody.parentElement.parentElement.classList.remove('hidden');
        emptyState.classList.add('hidden');
        data.forEach((item, idx) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${idx + 1 + (currentPage-1)*currentPerPage}</td>
                <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">${item.nomor_surat}</div></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(item.tanggal_surat)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.waktu}</td>
                <td class="px-6 py-4 whitespace-normal break-words text-sm text-gray-900">${item.pengirim}</td>
                <td class="px-6 py-4 whitespace-normal break-words text-sm text-gray-900">${item.tujuan}</td>
                <td class="px-6 py-4 whitespace-normal break-words text-sm text-gray-900">${item.perusahaan || '-'}</td>
                <td class="px-6 py-4 whitespace-normal break-words text-sm text-gray-900">${item.perihal}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderPagination(total, lastPage, currentPage) {
        let html = '';
        if (lastPage <= 1) { pagination.innerHTML = ''; return; }
        html += `<div class="flex gap-1">`;
        // Tombol Previous
        html += `<button class="px-3 py-1 rounded-l ${currentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>Prev</button>`;
        let pageNumbers = [];
        if (lastPage <= 5) {
            for (let i = 1; i <= lastPage; i++) pageNumbers.push(i);
        } else {
            pageNumbers.push(1);
            if (currentPage > 3) pageNumbers.push('...');
            let start = Math.max(2, currentPage - 1);
            let end = Math.min(lastPage - 1, currentPage + 1);
            for (let i = start; i <= end; i++) pageNumbers.push(i);
            if (currentPage + 2 < lastPage) pageNumbers.push('...');
            pageNumbers.push(lastPage);
        }
        pageNumbers.forEach(i => {
            if (i === '...') {
                html += `<span class='px-2 py-1 text-gray-400'>...</span>`;
            } else {
                html += `<button class="px-3 py-1 rounded ${i === currentPage ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700'}" data-page="${i}">${i}</button>`;
            }
        });
        // Tombol Next
        html += `<button class="px-3 py-1 rounded-r ${currentPage === lastPage ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}" data-page="${currentPage + 1}" ${currentPage === lastPage ? 'disabled' : ''}>Next</button>`;
        html += `</div>`;
        pagination.innerHTML = html;
        // Event
        pagination.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', function() {
                const page = parseInt(this.getAttribute('data-page'));
                if (!isNaN(page) && page >= 1 && page <= lastPage && page !== currentPage) {
                    loadData(page);
                }
            });
        });
    }

    function loadData(page = 1) {
        currentPage = page;
        currentPerPage = parseInt(perPageSelect.value);
        currentSearch = searchInput.value.trim();
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-8 text-gray-400">Memuat data...</td></tr>';
        fetch(`/api/surat-keluar/by-format?kode=${encodeURIComponent(kode)}&page=${page}&per_page=${currentPerPage}&search=${encodeURIComponent(currentSearch)}`)
            .then(res => res.json())
            .then(res => {
                console.log('API response:', res);
                if (res.success) {
                    try {
                        renderTable(res.data);
                        renderPagination(res.total, res.last_page, res.current_page);
                    } catch (err) {
                        console.error('Error in renderTable:', err);
                        tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-red-400">Error render data: ${err.message}</td></tr>`;
                    }
                } else {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-red-400">Gagal memuat data</td></tr>`;
                }
            })
            .catch((err) => {
                console.error('Fetch error:', err);
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-red-400">Gagal memuat data (fetch error)</td></tr>`;
            });
    }

    // Search & per page event
    searchBtn.addEventListener('click', () => loadData(1));
    searchInput.addEventListener('keyup', function(e) { if (e.key === 'Enter') loadData(1); });
    perPageSelect.addEventListener('change', () => loadData(1));

    // Initial load
    loadData(1);

    // Modal logic
    function getTodayStr() {
        const today = new Date();
        return today.toISOString().slice(0,10);
    }
    function formatDateIndo(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }
    function convertToRoman(num) {
        const romanNumerals = {1:'I',2:'II',3:'III',4:'IV',5:'V',6:'VI',7:'VII',8:'VIII',9:'IX',10:'X',11:'XI',12:'XII'};
        return romanNumerals[num];
    }
    function buildNomorLengkap(nomorUrut, kode, tanggal) {
        const d = new Date(tanggal);
        const bulan = convertToRoman(d.getMonth() + 1);
        const tahun = d.getFullYear();
        if (jenis === 'diradm') {
            return `${nomorUrut}/Dir.Adm.Keu/RSAZRA/${bulan}/${tahun}`;
        }
        return `${nomorUrut}/${kode}/${bulan}/${tahun}`;
    }
    generateBtn.addEventListener('click', function() {
        const tanggal = getTodayStr();
        fetch(`/suratkeluar/get-last-number?kode_jabatan=${encodeURIComponent(kode)}&tanggal_surat=${encodeURIComponent(tanggal)}`)
            .then(res => res.json())
            .then(res => {
                let nextNomor = '001';
                if (res.success && res.last_number !== undefined) {
                    nextNomor = String(parseInt(res.last_number, 10) + 1).padStart(3, '0');
                }
                document.getElementById(`next-nomor-${jenis}`).textContent = nextNomor;
                document.getElementById(`next-nomor-lengkap-${jenis}`).textContent = buildNomorLengkap(nextNomor, kode, tanggal);
                document.getElementById(`next-tanggal-${jenis}`).textContent = formatDateIndo(tanggal);
                document.getElementById(`next-kode-${jenis}`).textContent = kode;
                modal.classList.remove('hidden');
            })
            .catch(() => {
                document.getElementById(`next-nomor-${jenis}`).textContent = '001';
                document.getElementById(`next-nomor-lengkap-${jenis}`).textContent = buildNomorLengkap('001', kode, getTodayStr());
                document.getElementById(`next-tanggal-${jenis}`).textContent = formatDateIndo(getTodayStr());
                document.getElementById(`next-kode-${jenis}`).textContent = kode;
                modal.classList.remove('hidden');
            });
    });
    emptyGenerateBtn.addEventListener('click', function() {
        const tanggal = getTodayStr();
        fetch(`/suratkeluar/get-last-number?kode_jabatan=${encodeURIComponent(kode)}&tanggal_surat=${encodeURIComponent(tanggal)}`)
            .then(res => res.json())
            .then(res => {
                let nextNomor = '001';
                if (res.success && res.last_number !== undefined) {
                    nextNomor = String(parseInt(res.last_number, 10) + 1).padStart(3, '0');
                }
                document.getElementById(`next-nomor-${jenis}`).textContent = nextNomor;
                document.getElementById(`next-nomor-lengkap-${jenis}`).textContent = buildNomorLengkap(nextNomor, kode, tanggal);
                document.getElementById(`next-tanggal-${jenis}`).textContent = formatDateIndo(tanggal);
                document.getElementById(`next-kode-${jenis}`).textContent = kode;
                modal.classList.remove('hidden');
            })
            .catch(() => {
                document.getElementById(`next-nomor-${jenis}`).textContent = '001';
                document.getElementById(`next-nomor-lengkap-${jenis}`).textContent = buildNomorLengkap('001', kode, getTodayStr());
                document.getElementById(`next-tanggal-${jenis}`).textContent = formatDateIndo(getTodayStr());
                document.getElementById(`next-kode-${jenis}`).textContent = kode;
                modal.classList.remove('hidden');
            });
    });

    // Pastikan event listener tombol Tutup pada modal selalu aktif
    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
    // Juga tutup modal jika klik di luar konten modal (overlay)
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Tambahkan fungsi formatDate sebelum renderTable
    function formatDate(date) {
        if (!date) return '-';
        const d = new Date(date);
        if (isNaN(d)) return date;
        return d.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }

    // --- Filter Dropdown Sort ---
    const filterBtn = document.getElementById(`filter-btn-${jenis}`);
    const filterDropdown = document.getElementById(`filter-dropdown-${jenis}`);
    const filterLabel = document.getElementById(`filter-label-${jenis}`);
    const filterOptions = filterDropdown.querySelectorAll('.filter-option');
    const sortLabels = {
        'asc': 'Nomor Terkecil',
        'desc': 'Nomor Terbesar',
        'terbaru': 'Terbaru',
        'terlama': 'Terlama'
    };
    // Toggle dropdown
    filterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        filterDropdown.classList.toggle('hidden');
        filterBtn.setAttribute('aria-expanded', filterDropdown.classList.contains('hidden') ? 'false' : 'true');
    });
    // Pilih sort
    filterOptions.forEach(btn => {
        btn.addEventListener('click', function() {
            currentSort = this.dataset.sort;
            filterLabel.textContent = sortLabels[currentSort];
            filterDropdown.classList.add('hidden');
            renderTable(lastData);
        });
    });
    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
        if (!filterDropdown.classList.contains('hidden')) {
            if (!filterDropdown.contains(e.target) && !filterBtn.contains(e.target)) {
                filterDropdown.classList.add('hidden');
                filterBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });
    // Set label default
    filterLabel.textContent = sortLabels[currentSort];
})();
</script>
<style>
/* Tambahan styling untuk filter dropdown agar modern */
#filter-btn-{{ $jenis }}[aria-expanded="true"] {
    border-color: #10B981;
    background: #f0fdf4;
}
#filter-dropdown-{{ $jenis }} {
    min-width: 160px;
    box-shadow: 0 8px 24px 0 rgba(16,185,129,0.08);
}
.filter-option[data-sort="terbaru"].active,
.filter-option[data-sort="asc"].active,
.filter-option[data-sort="desc"].active,
.filter-option[data-sort="terlama"].active {
    background: #10B981 !important;
    color: #fff !important;
}
</style>
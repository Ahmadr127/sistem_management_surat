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
                    <h3 class="text-lg font-semibold text-gray-800">Generate Nomor {{ $title }}</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                            <input type="date" id="tanggal-{{ $jenis }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" value="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut</label>
                            <input type="number" id="nomor-{{ $jenis }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="001" min="1">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button id="cancel-{{ $jenis }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button id="confirm-{{ $jenis }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Generate
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perihal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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

    function renderTable(data) {
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
                <td class="px-6 py-4 whitespace-normal break-words text-sm text-gray-900">${item.perihal}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">${item.status}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="text-green-600 hover:text-green-900 mr-3" title="Lihat"><i class="ri-eye-line"></i></button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderPagination(total, lastPage, currentPage) {
        let html = '';
        if (lastPage <= 1) { pagination.innerHTML = ''; return; }
        html += `<div class="flex gap-1">`;
        let pageNumbers = [];
        if (lastPage <= 7) {
            for (let i = 1; i <= lastPage; i++) pageNumbers.push(i);
        } else {
            pageNumbers.push(1);
            if (currentPage > 4) pageNumbers.push('...');
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
        html += `</div>`;
        pagination.innerHTML = html;
        // Event
        pagination.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', function() {
                loadData(parseInt(this.getAttribute('data-page')));
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
                if (res.success) {
                    renderTable(res.data);
                    renderPagination(res.total, res.last_page, res.current_page);
                } else {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-red-400">Gagal memuat data</td></tr>`;
                }
            })
            .catch(() => {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-8 text-red-400">Gagal memuat data</td></tr>`;
            });
    }

    // Search & per page event
    searchBtn.addEventListener('click', () => loadData(1));
    searchInput.addEventListener('keyup', function(e) { if (e.key === 'Enter') loadData(1); });
    perPageSelect.addEventListener('change', () => loadData(1));

    // Initial load
    loadData(1);

    // Modal logic
    generateBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
        setTimeout(() => tanggalInput.focus(), 100);
    });
    emptyGenerateBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
        setTimeout(() => tanggalInput.focus(), 100);
    });
    function hideModal() {
        modal.classList.add('hidden');
        tanggalInput.value = '{{ date('Y-m-d') }}';
        nomorInput.value = '';
    }
    cancelBtn.addEventListener('click', hideModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
    function convertToRoman(num) {
        const romanNumerals = {
            1: 'I', 2: 'II', 3: 'III', 4: 'IV', 5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X', 11: 'XI', 12: 'XII'
        };
        return romanNumerals[num];
    }
    function formatDate(date) {
        return new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }
    confirmBtn.addEventListener('click', function() {
        const tanggal = tanggalInput.value;
        const nomor = nomorInput.value;
        if (!tanggal) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan pilih tanggal surat!', confirmButtonText: 'OK', confirmButtonColor: '#10B981' });
            return;
        }
        if (!nomor) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Silakan masukkan nomor urut!', confirmButtonText: 'OK', confirmButtonColor: '#10B981' });
            return;
        }
        const dateObj = new Date(tanggal);
        const bulan = convertToRoman(dateObj.getMonth() + 1);
        const tahun = dateObj.getFullYear();
        const nomorFormatted = String(nomor).padStart(3, '0');
        const nomorSurat = `${nomorFormatted}/${kode}/${bulan}/${tahun}`;
        const tanggalFormatted = formatDate(tanggal);
        // Add new row to table
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50';
        newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${tbody.children.length + 1}</td>
            <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">${nomorSurat}</div></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${tanggalFormatted}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="text-green-600 hover:text-green-900 mr-3" title="Lihat"><i class="ri-eye-line"></i></button>
            </td>
        `;
        tbody.appendChild(newRow);
        hideModal();
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: `Nomor surat ${nomorSurat} berhasil digenerate`, timer: 2000, showConfirmButton: false });
        loadData(1); // reload data agar sinkron
    });
})();
</script> 
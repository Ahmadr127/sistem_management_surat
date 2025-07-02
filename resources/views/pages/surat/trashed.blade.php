@extends('home')

@section('title', 'Surat Terhapus - SISM Azra')

@section('content')
    <!-- Tambahkan jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Surat Terhapus</h1>
                <p class="mt-1 text-sm text-gray-500">Surat keluar yang telah dihapus (30 hari sebelum dihapus permanen)</p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" id="search"
                        class="w-full sm:w-64 px-4 py-2 rounded-lg border border-gray-200 focus:border-gray-500 focus:ring focus:ring-gray-200 transition-all duration-200"
                        placeholder="Cari surat terhapus...">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <i class="ri-search-line"></i>
                    </div>
                </div>

                <!-- Kembali Button -->
                <a href="{{ route('suratkeluar.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                    <i class="ri-arrow-left-line mr-1.5"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-md">
            <!-- Table -->
            <div class="overflow-x-auto border border-gray-100 rounded-lg shadow-sm m-4">
                <table class="w-full" id="trashedSuratTable">
                    <thead class="bg-red-600">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Nomor Surat
                            </th>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Tanggal Surat
                            </th>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Perihal
                            </th>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Jenis
                            </th>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Pembuat
                            </th>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Dihapus Pada
                            </th>
                            <th scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="trashed-surat-table-body">
                        <!-- Data akan diisi melalui JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk mengelola data dan interaksi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variabel untuk menyimpan data
            let trashedSuratData = [];

            // Fungsi untuk memuat data surat terhapus
            async function loadTrashedSurat() {
                const tableBody = document.getElementById('trashed-surat-table-body');

                // Tampilkan loading state
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500"></div>
                                <p class="mt-2 text-sm text-gray-500">Memuat data...</p>
                            </div>
                        </td>
                    </tr>
                `;

                try {
                    // Ambil data dari API
                    const response = await fetch('/api/surat-keluar/trashed', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                    console.log('Received trashed data:', data);
                    
                    // Simpan data untuk referensi
                    trashedSuratData = data;

                    // Filter: surat yang dibuat oleh dia atau tujuan disposisi ke dia
                    let userRole = {{ auth()->user()->role ?? 'null' }};
                    let userId = {{ auth()->id() }};

                    if (userRole === 5) {
                        data = data.filter(surat => surat.created_by === userId || (surat.disposisi && surat.disposisi.tujuan && surat.disposisi.tujuan.some(t => t.id === userId)));
                    }

                    if (data.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="ri-delete-bin-line text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-gray-500">Tidak ada surat yang dihapus</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }

                    // Render data
                    renderTrashedTable(data);

                } catch (error) {
                    console.error('Error loading trashed data:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-8">
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
            function renderTrashedTable(data) {
                const tableBody = document.getElementById('trashed-surat-table-body');
                let html = '';

                data.forEach(surat => {
                    // Format tanggal
                    const date = new Date(surat.tanggal_surat);
                    const tanggal = date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    // Format tanggal dihapus
                    const deletedDate = new Date(surat.deleted_at);
                    const deletedAt = deletedDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${surat.nomor_surat}
                                ${surat.created_by === {{ auth()->id() }} ? 
                                    '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Anda</span>' 
                                    : ''}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${tanggal}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                ${surat.perihal}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${surat.jenis_surat === 'internal' ? 'bg-sky-100 text-sky-800' : 'bg-fuchsia-100 text-fuchsia-800'}">
                                    ${surat.jenis_surat === 'internal' ? 'Internal' : 'Eksternal'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${surat.creator ? surat.creator.name : 'Unknown'}
                                ${surat.creator?.jabatan ? 
                                    `<span class="text-xs text-gray-400">(${surat.creator.jabatan})</span>` 
                                    : ''}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    ${deletedAt}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap gap-2">
                                    <button onclick="restoreSurat(${surat.id})" class="text-green-600 hover:text-green-900 px-2 py-1 rounded-md bg-green-50 hover:bg-green-100 transition-colors flex items-center" title="Kembalikan Surat">
                                        <i class="ri-refresh-line mr-1"></i> Kembalikan
                                    </button>
                                    <button onclick="forceDeleteSurat(${surat.id})" class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md bg-red-50 hover:bg-red-100 transition-colors flex items-center" title="Hapus Permanen">
                                        <i class="ri-delete-bin-line mr-1"></i> Hapus Permanen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;
            }

            // Mengembalikan surat yang sudah dihapus
            window.restoreSurat = function(id) {
                if (confirm('Apakah Anda yakin ingin mengembalikan surat ini?')) {
                    // Tampilkan loading
                    const actionCell = document.querySelector(`button[onclick="restoreSurat(${id})"]`).closest('td');
                    const originalContent = actionCell.innerHTML;
                    actionCell.innerHTML = `<div class="flex justify-center"><i class="ri-loader-4-line animate-spin text-xl text-green-600"></i></div>`;
                    
                    fetch(`/suratkeluar/${id}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Terjadi kesalahan saat mengembalikan surat');
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
                                    <p>${data.message || 'Surat berhasil dikembalikan'}</p>
                                </div>
                            `;
                            document.body.appendChild(notification);
                            
                            // Hapus notifikasi setelah 3 detik
                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                            
                            // Reload data
                            loadTrashedSurat();
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
                                    <p>${error.message || 'Terjadi kesalahan saat mengembalikan surat'}</p>
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

            // Menghapus surat secara permanen
            window.forceDeleteSurat = function(id) {
                if (confirm('PERHATIAN: Surat akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?')) {
                    // Tampilkan loading
                    const actionCell = document.querySelector(`button[onclick="forceDeleteSurat(${id})"]`).closest('td');
                    const originalContent = actionCell.innerHTML;
                    actionCell.innerHTML = `<div class="flex justify-center"><i class="ri-loader-4-line animate-spin text-xl text-red-600"></i></div>`;
                    
                    fetch(`/suratkeluar/${id}/force`, {
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
                                    throw new Error(data.message || 'Terjadi kesalahan saat menghapus permanen surat');
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
                                    <p>${data.message || 'Surat berhasil dihapus permanen'}</p>
                                </div>
                            `;
                            document.body.appendChild(notification);
                            
                            // Hapus notifikasi setelah 3 detik
                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                            
                            // Reload data
                            loadTrashedSurat();
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
                                    <p>${error.message || 'Terjadi kesalahan saat menghapus permanen surat'}</p>
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

            // Pencarian
            document.getElementById('search').addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                
                if (!trashedSuratData || trashedSuratData.length === 0) {
                    return;
                }
                
                const filteredData = trashedSuratData.filter(surat => 
                    surat.nomor_surat.toLowerCase().includes(searchTerm) ||
                    surat.perihal.toLowerCase().includes(searchTerm) ||
                    (surat.creator && surat.creator.name.toLowerCase().includes(searchTerm))
                );
                
                renderTrashedTable(filteredData);
            });

            // Muat data saat halaman dimuat
            loadTrashedSurat();
        });
    </script>
@endsection 
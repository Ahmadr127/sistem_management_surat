@extends('home')

@section('title', 'Surat Unit Manager - SISM Azra')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Surat Unit Manager</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola surat dari unit ke manager</p>
        </div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
            @if(auth()->user()->role == 0)
            <a href="{{ route('surat-unit-manager.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                <i class="ri-add-line"></i>
                <span>Tambah Surat</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="px-8 py-4 border-b border-gray-100 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" id="searchInput" placeholder="Cari nomor surat, perihal..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" id="startDate" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" id="endDate" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Surat
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Perihal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Unit
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Manager
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="suratTableBody">
                @forelse($suratUnitManager as $surat)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $surat->nomor_surat }}</div>
                        <div class="text-sm text-gray-500">{{ $surat->jenis_surat }} - {{ $surat->sifat_surat }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $surat->tanggal_surat->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ Str::limit($surat->perihal, 50) }}</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($surat->isi_surat, 30) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $surat->unit->name }}</div>
                        <div class="text-sm text-gray-500">{{ optional($surat->unit->jabatan)->nama_jabatan }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $surat->manager->name }}</div>
                        <div class="text-sm text-gray-500">{{ optional($surat->manager->jabatan)->nama_jabatan }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColor = $surat->status_color;
                            $statusText = $surat->current_status;
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                     @if($statusColor == 'success') bg-green-100 text-green-800
                                     @elseif($statusColor == 'warning') bg-yellow-100 text-yellow-800
                                     @elseif($statusColor == 'danger') bg-red-100 text-red-800
                                     @else bg-gray-100 text-gray-800 @endif">
                            {{ $statusText }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('surat-unit-manager.show', $surat->id) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Detail">
                                <i class="ri-eye-line"></i>
                            </a>
                            
                            @if(auth()->user()->role == 0 && $surat->unit_id == auth()->id() && $surat->status_manager == 'pending')
                            <a href="{{ route('surat-unit-manager.edit', $surat->id) }}" 
                               class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                <i class="ri-edit-line"></i>
                            </a>
                            
                            <button onclick="deleteSurat({{ $surat->id }})" 
                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @endif
                            
                            @if($surat->file_path)
                            <a href="{{ route('surat-unit-manager.download', $surat->id) }}" 
                               class="text-green-600 hover:text-green-900" title="Download">
                                <i class="ri-download-line"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data surat unit manager
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="ri-error-warning-line text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Hapus</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus surat ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="confirmDelete" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Hapus
                </button>
                <button type="button" onclick="closeDeleteModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentSuratId = null;

// Filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('startDate').addEventListener('change', filterTable);
document.getElementById('endDate').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    const rows = document.querySelectorAll('#suratTableBody tr');
    
    rows.forEach(row => {
        if (row.cells.length < 7) return; // Skip empty rows
        
        const nomorSurat = row.cells[0].textContent.toLowerCase();
        const perihal = row.cells[2].textContent.toLowerCase();
        const tanggal = row.cells[1].textContent;
        const statusElement = row.cells[5].querySelector('span');
        const status = statusElement ? statusElement.textContent.toLowerCase() : '';
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !nomorSurat.includes(searchTerm) && !perihal.includes(searchTerm)) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter && !status.includes(statusFilter)) {
            showRow = false;
        }
        
        // Date filter
        if (startDate || endDate) {
            const rowDate = new Date(tanggal.split('/').reverse().join('-'));
            if (startDate && rowDate < new Date(startDate)) {
                showRow = false;
            }
            if (endDate && rowDate > new Date(endDate)) {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Delete functionality
function deleteSurat(suratId) {
    currentSuratId = suratId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentSuratId = null;
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (currentSuratId) {
        fetch(`/surat-unit-manager/${currentSuratId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus surat'
            });
        })
        .finally(() => {
            closeDeleteModal();
        });
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush 
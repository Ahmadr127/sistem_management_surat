@extends('home')

@section('title', 'Persetujuan Surat Unit - Manager')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Persetujuan Surat Unit</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola persetujuan surat dari unit</p>
        </div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="px-8 py-4 border-b border-gray-100 bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <option value="pending" selected>Pending</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" id="dateFilter" 
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
                        <div class="text-xs text-gray-500">{{ $surat->created_at->format('H:i') }}</div>
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
                        @php
                            $statusColor = $surat->status_manager === 'approved' ? 'green' : 
                                          ($surat->status_manager === 'rejected' ? 'red' : 'yellow');
                            $statusText = $surat->status_manager === 'approved' ? 'Disetujui' : 
                                         ($surat->status_manager === 'rejected' ? 'Ditolak' : 'Menunggu');
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                     @if($statusColor == 'green') bg-green-100 text-green-800
                                     @elseif($statusColor == 'red') bg-red-100 text-red-800
                                     @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $statusText }}
                        </span>
                        @if($surat->waktu_review_manager)
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $surat->waktu_review_manager->format('d/m/Y H:i') }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('surat-unit-manager.manager.show', $surat->id) }}" 
                               class="text-blue-600 hover:text-blue-900" title="Detail & Review">
                                <i class="ri-eye-line"></i>
                            </a>
                            
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
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada surat yang menunggu persetujuan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('dateFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#suratTableBody tr');
    
    rows.forEach(row => {
        if (row.cells.length < 6) return; // Skip empty rows
        
        const nomorSurat = row.cells[0].textContent.toLowerCase();
        const perihal = row.cells[2].textContent.toLowerCase();
        const tanggal = row.cells[1].textContent;
        const statusElement = row.cells[4].querySelector('span');
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
        if (dateFilter) {
            const rowDate = new Date(tanggal.split('/').reverse().join('-'));
            if (rowDate.toDateString() !== new Date(dateFilter).toDateString()) {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}
</script>
@endpush 
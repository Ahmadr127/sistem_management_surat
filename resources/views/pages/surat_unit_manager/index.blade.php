@extends('home')

@section('title', 'Surat Unit Manager')

@section('content')
<div class="bg-white rounded-xl shadow-md">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-200 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Surat Unit Anda</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola surat yang Anda kirim untuk persetujuan manajer</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('surat-unit-manager.create') }}" 
               class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                <i class="ri-add-line mr-1"></i> Buat Surat Baru
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <x-table-filter label="Cari Surat" searchPlaceholder="Cari nomor surat atau perihal...">
        <!-- Status Filter -->
        <div class="min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status Persetujuan</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
    </x-table-filter>

    <!-- Table -->
    <div class="p-4">
        <div class="overflow-x-auto border border-gray-100 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Detail Surat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Disetujui</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status Persetujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suratUnitManager as $surat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $suratUnitManager->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">{{ $surat->nomor_surat }}</div>
                                <div class="text-xs text-gray-600 truncate max-w-xs">{{ $surat->perihal }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $surat->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $surat->waktu_review_manager ? \Carbon\Carbon::parse($surat->waktu_review_manager)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClass = match($surat->status_manager) {
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($surat->status_manager) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('surat-unit-manager.show', $surat->id) }}"
                                       class="text-gray-600 hover:text-gray-900 px-2 py-1 rounded-md bg-gray-50 hover:bg-gray-100 transition-colors flex items-center" title="Lihat Detail">
                                        <i class="ri-eye-line mr-1"></i> Detail
                                    </a>
                                    
                                    @if(in_array($surat->status_manager, ['pending', 'rejected']))
                                        <a href="{{ route('surat-unit-manager.edit', $surat->id) }}"
                                           class="text-yellow-600 hover:text-yellow-800 px-2 py-1 rounded-md bg-yellow-50 hover:bg-yellow-100 transition-colors flex items-center" title="Edit Surat">
                                            <i class="ri-edit-line mr-1"></i> Edit
                                        </a>
                                        <button onclick="confirmDelete({{ $surat->id }})"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded-md bg-red-50 hover:bg-red-100 transition-colors flex items-center" title="Hapus Surat">
                                            <i class="ri-delete-bin-line mr-1"></i> Hapus
                                        </button>
                                        <form id="delete-form-{{ $surat->id }}" action="{{ route('surat-unit-manager.destroy', $surat->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada surat yang cocok dengan filter atau Anda belum membuat surat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $suratUnitManager->count() }} dari {{ $suratUnitManager->total() }} surat
                </div>
                @if ($suratUnitManager->hasPages())
                    <div>
                        {{ $suratUnitManager->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Anda yakin?',
            text: "Surat ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
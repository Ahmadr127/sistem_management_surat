@extends('home')

@section('title', 'Persetujuan Surat Unit')

@section('content')
<div class="bg-white rounded-xl shadow-md">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-200 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Persetujuan Surat Unit</h2>
            <p class="text-xs text-gray-500 mt-1">
                Daftar surat yang memerlukan persetujuan Anda sebagai 
                <span class="font-bold text-green-600">{{ ucfirst($context) }}</span>
            </p>
        </div>
        <div class="text-sm text-gray-500">
            <i class="ri-time-line"></i>
            <span>{{ date('d M Y') }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <x-table-filter label="Cari Surat" searchPlaceholder="Cari nomor surat, perihal, atau unit...">
        <!-- Status Filter -->
        <div class="min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Dari Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status Anda</th>
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
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $surat->unit->name ?? '-' }}</div>
                                <div class="text-xs text-gray-600">{{ $surat->unit->jabatan_name ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $surat->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    // Determine status based on context
                                    $currentStatus = match($context) {
                                        'manager' => $surat->status_manager,
                                        'sekretaris' => $surat->status_sekretaris,
                                        'dirut' => $surat->status_dirut,
                                        default => 'pending'
                                    };

                                    $statusClass = match($currentStatus) {
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($currentStatus ?? 'Pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('surat-unit-manager.approval.show', $surat->id) }}" 
                                   class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center w-fit">
                                    <i class="ri-search-eye-line mr-1"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada data surat yang cocok dengan filter.
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

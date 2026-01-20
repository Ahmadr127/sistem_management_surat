@extends('home')

@section('title', 'Surat Masuk - SISM Azra')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        {{-- Title --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Surat Masuk</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data surat masuk</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-md">
        {{-- Filters Form --}}
        <form method="GET" action="{{ route('suratmasuk.index') }}" id="filterForm">
            <div class="p-5 border-b border-gray-200">
                <div class="flex flex-wrap items-end gap-3">
                    {{-- Search with Searchable Dropdown --}}
                    <div class="flex-1 min-w-[200px]">
                        <x-searchable-dropdown
                            name="search_surat"
                            label="Cari Surat"
                            placeholder="Cari nomor surat, perihal..."
                            :options="$suratOptions"
                            valueField="id"
                            labelField="label"
                        />
                    </div>

                    {{-- Filter Tanggal --}}
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                    </div>

                    {{-- Filter Jenis Surat --}}
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat</label>
                        <select name="jenis_surat"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                            <option value="">Semua Jenis</option>
                            <option value="internal" {{ request('jenis_surat') == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="eksternal" {{ request('jenis_surat') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                        </select>
                    </div>

                    {{-- Filter Sifat Surat --}}
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sifat Surat</label>
                        <select name="sifat_surat"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                            <option value="">Semua Sifat</option>
                            <option value="normal" {{ request('sifat_surat') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="urgent" {{ request('sifat_surat') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    {{-- Filter Status Sekretaris --}}
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Sekretaris</label>
                        <select name="status_sekretaris"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_sekretaris') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="review" {{ request('status_sekretaris') == 'review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                            <option value="approved" {{ request('status_sekretaris') == 'approved' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status_sekretaris') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- Filter Status Direktur --}}
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Direktur</label>
                        <select name="status_dirut"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_dirut') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="review" {{ request('status_dirut') == 'review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                            <option value="approved" {{ request('status_dirut') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status_dirut') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- Reset Button --}}
                    <div>
                        <a href="{{ route('suratmasuk.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 text-sm">
                            <i class="ri-refresh-line mr-1.5"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="p-4">
            <div class="overflow-x-auto relative rounded-lg shadow-sm border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">No. Disposisi</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">No. Surat</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Perusahaan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Perihal</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Jenis</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Pengirim</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status Sekretaris</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status Direktur</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suratMasuk as $surat)
                            <tr class="hover:bg-gray-50 {{ $surat->created_by == auth()->id() ? 'bg-green-50' : '' }}">
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $surat->disposisi->id ?? '-' }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $surat->nomor_surat }}
                                    @if($surat->created_by == auth()->id())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Anda</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $surat->perusahaanData->nama_perusahaan ?? $surat->perusahaan ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-500 max-w-[200px] truncate" title="{{ $surat->perihal }}">
                                    {{ $surat->perihal }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $surat->jenis_surat === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $surat->jenis_surat === 'internal' ? 'Internal' : 'Eksternal' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-500">
                                    {{ $surat->creator->name ?? '-' }}
                                    @if($surat->creator && $surat->creator->jabatan)
                                        <span class="text-gray-400">({{ $surat->creator->jabatan->nama_jabatan }})</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm">
                                    @php
                                        $statusSekretaris = $surat->disposisi->status_sekretaris ?? 'pending';
                                        $statusClass = match($statusSekretaris) {
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'review' => 'bg-blue-100 text-blue-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusLabel = match($statusSekretaris) {
                                            'pending' => 'Menunggu',
                                            'review' => 'Ditinjau',
                                            'approved' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            default => '-'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm">
                                    @php
                                        $statusDirut = $surat->disposisi->status_dirut ?? 'pending';
                                        $statusClass = match($statusDirut) {
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'review' => 'bg-blue-100 text-blue-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusLabel = match($statusDirut) {
                                            'pending' => 'Menunggu',
                                            'review' => 'Ditinjau',
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                            default => '-'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-nowrap gap-2">
                                        <button onclick="window.showDetailSuratMasuk({{ $surat->id }})"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-white border border-green-300 hover:bg-green-50 text-green-700 rounded-md shadow-sm transition-colors duration-200">
                                            <i class="ri-eye-line mr-1"></i> Detail
                                        </button>

                                        @if(in_array(auth()->user()->role, [1, 2, 5, 8]))
                                            <button onclick="window.openEditDisposisiSuratMasuk({{ $surat->id }})"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-white border border-indigo-300 hover:bg-indigo-50 text-indigo-700 rounded-md shadow-sm transition-colors duration-200">
                                                <i class="ri-file-edit-line mr-1"></i> Disposisi
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                    <i class="ri-inbox-line text-4xl mb-2 block text-gray-300"></i>
                                    Tidak ada data surat masuk
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $suratMasuk->count() }} dari {{ $suratMasuk->total() }} surat
                </div>
                @if ($suratMasuk->hasPages())
                    <div>
                        {!! $suratMasuk->links() !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Include Modals --}}
@include('pages.surat.surat_masuk.modal.detail_surat')
@include('pages.surat.surat_masuk.modal.edit_disposisi')

@push('scripts')
<script>
    // Set user role globally
    window.userRole = {{ auth()->user()->role }};

    // Auto-submit form on filter change
    document.querySelectorAll('select[name="jenis_surat"], select[name="sifat_surat"], select[name="status_sekretaris"], select[name="status_dirut"]').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Watch for searchable dropdown selection
    document.addEventListener('alpine:init', () => {
        const searchDropdown = document.querySelector('[name="search_surat"]');
        if (searchDropdown) {
            searchDropdown.addEventListener('change', function() {
                const suratId = this.value;
                if (suratId && window.showDetailSuratMasuk) {
                    window.showDetailSuratMasuk(suratId);
                }
            });
        }
    });
</script>
@endpush
@endsection

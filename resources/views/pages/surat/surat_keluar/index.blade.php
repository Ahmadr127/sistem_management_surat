@extends('home')

@section('title', 'Surat Keluar - SISM Azra')

@section('content')
{{-- Single Card untuk semua konten --}}
<div class="bg-white rounded-xl shadow-md">
    {{-- Header Section --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Surat Keluar</h1>
                <p class="text-sm text-gray-500">Kelola data surat keluar</p>
            </div>
            <div class="flex items-center gap-3">
                <x-button 
                    variant="success" 
                    icon="ri-add-line"
                    onclick="window.location.href='{{ route('suratkeluar.create') }}'">
                    Tambah Surat
                </x-button>
            </div>
        </div>

        {{-- Filters Form --}}
        <form method="GET" action="{{ route('suratkeluar.index') }}" id="filterForm">
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

                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat</label>
                    <select name="jenis_surat"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                        <option value="">Semua Jenis</option>
                        <option value="internal" {{ request('jenis_surat') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="eksternal" {{ request('jenis_surat') == 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sifat Surat</label>
                    <select name="sifat_surat"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                        <option value="">Semua Sifat</option>
                        <option value="normal" {{ request('sifat_surat') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="urgent" {{ request('sifat_surat') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Sekretaris</label>
                    <select name="status_sekretaris"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status_sekretaris') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="review" {{ request('status_sekretaris') == 'review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="approved" {{ request('status_sekretaris') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status_sekretaris') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Direktur</label>
                    <select name="status_dirut"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status_dirut') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="review" {{ request('status_dirut') == 'review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="approved" {{ request('status_dirut') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status_dirut') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2">
                    <button 
                        type="submit"
                        class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200"
                        title="Filter">
                        <i class="ri-filter-line text-lg"></i>
                    </button>

                    <a 
                        href="{{ route('suratkeluar.index') }}"
                        class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors duration-200"
                        title="Reset">
                        <i class="ri-refresh-line text-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="p-6">
        @if($suratKeluar->count() > 0)
            {{-- Table --}}
            <div class="overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">No Disposisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Nomor Surat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Perusahaan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Perihal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Status Sekretaris</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Status Direktur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($suratKeluar as $surat)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $surat->disposisi->id ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $surat->tanggal_surat ? \Carbon\Carbon::parse($surat->tanggal_surat)->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $surat->nomor_surat ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $surat->perusahaanData->nama_perusahaan ?? $surat->perusahaan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $surat->perihal ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($surat->jenis_surat == 'internal')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Internal</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Eksternal</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $statusSekretaris = $surat->disposisi->status_sekretaris ?? 'pending';
                                        $badgeClass = match($statusSekretaris) {
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'review' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-yellow-100 text-yellow-800'
                                        };
                                        $badgeLabel = match($statusSekretaris) {
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                            'review' => 'Ditinjau',
                                            default => 'Menunggu'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $statusDirut = $surat->disposisi->status_dirut ?? 'pending';
                                        $badgeClass = match($statusDirut) {
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'review' => 'bg-blue-100 text-blue-800',
                                            default => 'bg-yellow-100 text-yellow-800'
                                        };
                                        $badgeLabel = match($statusDirut) {
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                            'review' => 'Ditinjau',
                                            default => 'Menunggu'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $surat->disposisi?->tujuan?->pluck('name')->join(', ') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center space-x-2">
                                        {{-- View Detail --}}
                                        <button onclick="showDetailSurat({{ $surat->id }})" 
                                                class="text-blue-600 hover:text-blue-800 transition-colors"
                                                title="Lihat Detail">
                                            <i class="ri-eye-line text-lg"></i>
                                        </button>

                                        {{-- Edit (only for creator or admin) --}}
                                        @if($surat->created_by == auth()->id() || auth()->user()->role == 3)
                                            <a href="{{ route('suratkeluar.edit', $surat->id) }}" 
                                               class="text-green-600 hover:text-green-800 transition-colors"
                                               title="Edit">
                                                <i class="ri-edit-line text-lg"></i>
                                            </a>
                                            
                                            <form action="{{ route('suratkeluar.destroy', $surat->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 transition-colors"
                                                        title="Hapus">
                                                    <i class="ri-delete-bin-line text-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit Disposisi (for sekretaris, direktur, or admin) --}}
                                        @if(in_array(auth()->user()->role, [1, 2, 3]))
                                            <button onclick="openEditDisposisiModal({{ $surat->id }})" 
                                                    class="text-purple-600 hover:text-purple-800 transition-colors"
                                                    title="Edit Disposisi">
                                                <i class="ri-file-edit-line text-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Info --}}
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $suratKeluar->count() }} data
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="ri-inbox-line text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Tidak ada data surat keluar</p>
                <p class="text-gray-400 text-sm mt-2">Silakan tambah surat keluar baru atau ubah filter pencarian</p>
            </div>
        @endif
    </div>
</div>

{{-- Include Modals --}}
@include('pages.surat.surat_keluar.modal.detail_surat')
@include('pages.surat.surat_keluar.modal.edit_disposisi')

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Set user role dan ID
    window.userRole = {{ auth()->user()->role ?? 'null' }};
    window.userId = {{ auth()->id() }};

    // Auto-submit form on filter change
    document.querySelectorAll('select[name="jenis_surat"], select[name="sifat_surat"], select[name="status_sekretaris"], select[name="status_dirut"]').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Watch for searchable dropdown selection (surat search)
    document.addEventListener('alpine:init', () => {
        // Find the searchable dropdown component
        const searchDropdown = document.querySelector('[name="search_surat"]');
        if (searchDropdown) {
            // Listen for changes on the hidden input
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        const suratId = searchDropdown.value;
                        if (suratId && window.showDetailSurat) {
                            window.showDetailSurat(suratId);
                        }
                    }
                });
            });
            
            // Observe the hidden input for value changes
            observer.observe(searchDropdown, {
                attributes: true,
                attributeFilter: ['value']
            });
            
            // Also listen for input event
            searchDropdown.addEventListener('change', function() {
                const suratId = this.value;
                if (suratId && window.showDetailSurat) {
                    window.showDetailSurat(suratId);
                }
            });
        }
    });


</script>
@endpush

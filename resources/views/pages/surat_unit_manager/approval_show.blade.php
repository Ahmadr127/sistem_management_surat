@extends('home')

@section('title', 'Review Surat Unit - ' . $surat->nomor_surat)

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Review Surat Unit - {{ $surat->nomor_surat }}</h2>
            <p class="text-xs text-gray-500 mt-1">
                Detail surat dan persetujuan sebagai <span class="font-bold text-green-600">{{ ucfirst($context) }}</span>
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
            <a href="{{ route('surat-unit-manager.approval.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="p-8">
        <!-- Status Badge -->
        <div class="mb-6">
            @php
                $currentStatus = $surat->status_manager;

                $statusColor = $currentStatus === 'approved' ? 'green' : 
                              ($currentStatus === 'rejected' ? 'red' : 'yellow');
                $statusText = $currentStatus === 'approved' ? 'Disetujui Kepala Unit' : 
                             ($currentStatus === 'rejected' ? 'Ditolak Kepala Unit' : 'Menunggu Persetujuan Kepala Unit');
            @endphp
            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full 
                         @if($statusColor == 'green') bg-green-100 text-green-800
                         @elseif($statusColor == 'red') bg-red-100 text-red-800
                         @else bg-yellow-100 text-yellow-800 @endif">
                <i class="ri-information-line mr-2"></i>
                {{ $statusText }}
            </span>
        </div>

        <!-- Main Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="ri-file-text-line mr-2 text-blue-600"></i>
                        Informasi Surat
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nomor Surat</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $surat->nomor_surat }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Surat</label>
                            <p class="text-gray-900">{{ $surat->tanggal_surat->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Perihal</label>
                            <p class="text-gray-900">{{ $surat->perihal }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Isi Surat</label>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $surat->isi_surat }}</p>
                        </div>
                    </div>
                </div>

                <!-- Classification -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="ri-settings-line mr-2 text-green-600"></i>
                        Klasifikasi
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jenis Surat</label>
                            <p class="text-gray-900 capitalize">{{ $surat->jenis_surat }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Sifat Surat</label>
                            <p class="text-gray-900 capitalize">{{ $surat->sifat_surat }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Perusahaan</label>
                            <p class="text-gray-900">{{ $surat->perusahaanData->nama_perusahaan ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- File Attachment -->
                @if($surat->files->count() > 0)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="ri-attachment-line mr-2 text-purple-600"></i>
                        Lampiran
                    </h3>
                    <div class="space-y-2">
                        @foreach($surat->files as $file)
                        <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200">
                            <div class="flex items-center overflow-hidden">
                                <i class="ri-file-line text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-600 truncate">{{ $file->original_name }}</span>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('surat-unit-manager.preview-file', ['suratUnitManager' => $surat->id, 'fileId' => $file->id]) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Preview
                                </a>
                                <a href="{{ route('surat-unit-manager.download-file', ['suratUnitManager' => $surat->id, 'fileId' => $file->id]) }}" 
                                   class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Approval Form -->
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="ri-check-double-line mr-2 text-green-600"></i>
                        Form Persetujuan
                    </h3>

                    @if($currentStatus === 'pending')
                    <form action="{{ route('surat-unit-manager.approval.process', $surat->id) }}" method="POST" id="approvalForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="catatan" id="catatan" rows="4" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                      placeholder="Tambahkan catatan jika ada..."></textarea>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" name="action" value="approve"
                                    class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center">
                                <i class="ri-check-line mr-2"></i>
                                Setujui
                            </button>
                            <button type="button" onclick="confirmReject()"
                                    class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center">
                                <i class="ri-close-line mr-2"></i>
                                Tolak
                            </button>
                        </div>
                        <!-- Hidden input for reject action -->
                        <input type="hidden" name="action" id="actionInput" value="approve">
                    </form>
                    @else
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <i class="ri-checkbox-circle-line text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Surat ini sudah diproses.</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Status: <span class="font-semibold">{{ ucfirst($currentStatus) }}</span>
                        </p>
                        @php
                            $catatan = $surat->keterangan_manager;
                            $waktu   = $surat->waktu_review_manager;
                        @endphp
                        @if($catatan)
                        <div class="mt-3 text-left bg-white p-3 rounded border border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Catatan:</p>
                            <p class="text-sm text-gray-800">{{ $catatan }}</p>
                        </div>
                        @endif
                        @if($waktu)
                        <p class="text-xs text-gray-400 mt-3">
                            Diproses pada: {{ \Carbon\Carbon::parse($waktu)->format('d M Y H:i') }}
                        </p>
                        @endif

                        @if($currentStatus === 'approved')
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                @if(!$surat->surat_keluar_id)
                                    <p class="text-sm text-gray-600 mb-3">Surat ini dapat diajukan sebagai Surat Keluar resmi untuk proses lebih lanjut.</p>
                                    <a href="{{ route('surat-unit-manager.approval.convert-form', $surat->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="ri-send-plane-line mr-2"></i> Ajukan sebagai Surat Keluar
                                    </a>
                                @else
                                    <div class="bg-blue-50 text-blue-800 p-3 rounded-lg flex items-start text-left">
                                        <i class="ri-information-line mt-0.5 mr-2 text-lg"></i>
                                        <div>
                                            <p class="font-medium text-sm">Telah dikonversi menjadi Surat Keluar</p>
                                            <a href="{{ route('suratkeluar.show', $surat->surat_keluar_id) }}" class="text-xs underline hover:text-blue-600 mt-1 inline-block">Lihat Detail Surat Keluar</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- History Log -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Persetujuan</h3>
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($surat->histories as $history)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                                {{ $history->action == 'approve' ? 'bg-green-500' : ($history->action == 'reject' ? 'bg-red-500' : 'bg-gray-500') }}">
                                                <i class="ri-{{ $history->action == 'approve' ? 'check' : ($history->action == 'reject' ? 'close' : 'user') }}-line text-white text-sm"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    <span class="font-medium text-gray-900">{{ $history->user->name }}</span>
                                                    {{ $history->action == 'approve' ? 'menyetujui' : ($history->action == 'reject' ? 'menolak' : 'memproses') }} surat
                                                </p>
                                                @if($history->keterangan)
                                                <p class="mt-1 text-sm text-gray-600 italic">"{{ $history->keterangan }}"</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $history->created_at }}">{{ $history->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'SUCCESS',
        text: "{{ session('success') }}",
        showConfirmButton: true,
        confirmButtonText: 'ok'
    });
    @endif

    function confirmReject() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menolak surat ini. Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            input: 'textarea',
            inputLabel: 'Alasan Penolakan (Wajib)',
            inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
            inputValidator: (value) => {
                if (!value) {
                    return 'Anda harus menuliskan alasan penolakan!'
                }
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('actionInput').value = 'reject';
                document.getElementById('catatan').value = result.value;
                document.getElementById('approvalForm').submit();
            }
        })
    }
</script>
@endpush
@endsection

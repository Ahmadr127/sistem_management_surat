@extends('home')

@section('title', 'Review Surat Unit - Manager')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Review Surat Unit</h2>
            <p class="text-xs text-gray-500 mt-1">Detail surat dan persetujuan</p>
        </div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
            <a href="{{ route('surat-unit-manager.manager.index') }}" 
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
                $statusColor = $suratUnitManager->status_manager === 'approved' ? 'green' : 
                              ($suratUnitManager->status_manager === 'rejected' ? 'red' : 'yellow');
                $statusText = $suratUnitManager->status_manager === 'approved' ? 'Disetujui' : 
                             ($suratUnitManager->status_manager === 'rejected' ? 'Ditolak' : 'Menunggu Persetujuan');
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
                            <p class="text-lg font-semibold text-gray-900">{{ $suratUnitManager->nomor_surat }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Surat</label>
                            <p class="text-gray-900">{{ $suratUnitManager->tanggal_surat->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Perihal</label>
                            <p class="text-gray-900">{{ $suratUnitManager->perihal }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Isi Surat</label>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $suratUnitManager->isi_surat }}</p>
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
                            <p class="text-gray-900 capitalize">{{ $suratUnitManager->jenis_surat }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Sifat Surat</label>
                            <p class="text-gray-900 capitalize">{{ $suratUnitManager->sifat_surat }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Perusahaan</label>
                            <p class="text-gray-900">{{ $suratUnitManager->nama_perusahaan }}</p>
                        </div>
                    </div>
                </div>

                <!-- File Attachment -->
                @if($suratUnitManager->file_path)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="ri-attachment-line mr-2 text-purple-600"></i>
                        File Lampiran
                    </h3>
                    <div class="flex items-center space-x-3 p-3 bg-white border border-gray-200 rounded-md">
                        <i class="ri-file-line text-gray-500 text-xl"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ basename($suratUnitManager->file_path) }}</p>
                            <p class="text-xs text-gray-500">File lampiran surat</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('surat-unit-manager.preview', $suratUnitManager->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm" title="Preview">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="{{ route('surat-unit-manager.download', $suratUnitManager->id) }}" 
                               class="text-green-600 hover:text-green-800 text-sm" title="Download">
                                <i class="ri-download-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Unit Info -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <i class="ri-user-line mr-2 text-blue-600"></i>
                        Unit (Pembuat Surat)
                    </h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="ri-user-line text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-blue-900">{{ $suratUnitManager->unit->name }}</p>
                            <p class="text-sm text-blue-700">{{ optional($suratUnitManager->unit->jabatan)->nama_jabatan }}</p>
                            <p class="text-xs text-blue-600">{{ $suratUnitManager->unit->email }}</p>
                        </div>
                    </div>
                    @if($suratUnitManager->keterangan_unit)
                    <div class="mt-4 p-3 bg-white rounded-md">
                        <label class="block text-sm font-medium text-blue-700 mb-1">Keterangan Unit:</label>
                        <p class="text-sm text-blue-800">{{ $suratUnitManager->keterangan_unit }}</p>
                    </div>
                    @endif
                </div>

                <!-- Approval Form -->
                @if($suratUnitManager->status_manager === 'pending')
                <div class="bg-yellow-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                        <i class="ri-check-line mr-2 text-yellow-600"></i>
                        Persetujuan Manager
                    </h3>
                    
                    <form id="approvalForm" action="{{ route('surat-unit-manager.manager.approval', $suratUnitManager->id) }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-yellow-700 mb-2">Keputusan</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="action" value="approve" class="mr-2 text-green-600" required>
                                        <span class="text-sm text-green-700">Setujui</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="action" value="reject" class="mr-2 text-red-600" required>
                                        <span class="text-sm text-red-700">Tolak</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label for="keterangan_manager" class="block text-sm font-medium text-yellow-700 mb-2">
                                    Keterangan (Opsional)
                                </label>
                                <textarea id="keterangan_manager" name="keterangan_manager" rows="4"
                                          class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                          placeholder="Masukkan keterangan atau alasan keputusan..."></textarea>
                            </div>
                            
                            <div class="flex space-x-3 pt-4">
                                <button type="submit" id="submitApproval"
                                        class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                                    <i class="ri-send-plane-line"></i>
                                    <span>Kirim Keputusan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @else
                <!-- Approval Status -->
                <div class="bg-{{ $statusColor }}-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-{{ $statusColor }}-800 mb-4 flex items-center">
                        <i class="ri-check-line mr-2 text-{{ $statusColor }}-600"></i>
                        Status Persetujuan
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                         @if($statusColor == 'green') bg-green-100 text-green-800
                                         @elseif($statusColor == 'red') bg-red-100 text-red-800
                                         @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $statusText }}
                            </span>
                        </div>
                        
                        @if($suratUnitManager->waktu_review_manager)
                        <div>
                            <label class="block text-sm font-medium text-{{ $statusColor }}-700">Waktu Review</label>
                            <p class="text-sm text-{{ $statusColor }}-800">{{ $suratUnitManager->waktu_review_manager->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($suratUnitManager->keterangan_manager)
                        <div>
                            <label class="block text-sm font-medium text-{{ $statusColor }}-700">Keterangan</label>
                            <p class="text-sm text-{{ $statusColor }}-800">{{ $suratUnitManager->keterangan_manager }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Next Steps -->
                @if($suratUnitManager->status_manager === 'approved')
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <i class="ri-arrow-right-line mr-2 text-green-600"></i>
                        Langkah Selanjutnya
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="ri-check-line text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-900">Manager: Disetujui</p>
                                <p class="text-xs text-green-700">Surat akan diteruskan ke Sekretaris</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="ri-time-line text-gray-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Sekretaris: Menunggu</p>
                                <p class="text-xs text-gray-500">Menunggu persetujuan sekretaris</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="ri-time-line text-gray-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Direktur: Menunggu</p>
                                <p class="text-xs text-gray-500">Menunggu persetujuan direktur</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const approvalForm = document.getElementById('approvalForm');
    
    if (approvalForm) {
        approvalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get selected action
            const selectedAction = document.querySelector('input[name="action"]:checked');
            if (!selectedAction) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Keputusan',
                    text: 'Silakan pilih keputusan (Setujui/Tolak)'
                });
                return;
            }
            
            // Confirm action
            const actionText = selectedAction.value === 'approve' ? 'menyetujui' : 'menolak';
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Keputusan',
                text: `Apakah Anda yakin ingin ${actionText} surat ini?`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: selectedAction.value === 'approve' ? '#10B981' : '#EF4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable submit button
                    const submitBtn = document.getElementById('submitApproval');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i><span>Mengirim...</span>';
                    
                    // Create FormData
                    const formData = new FormData(this);
                    
                    // Submit form
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Terjadi kesalahan saat memproses permintaan'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengirim keputusan. Silakan coba lagi.'
                        });
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                }
            });
        });
    } else {
        console.error('Approval form not found');
    }
});
</script>
@endpush 
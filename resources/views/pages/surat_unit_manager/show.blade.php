@extends('home')

@section('title', 'Detail Surat Unit Manager - SISM Azra')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Detail Surat Unit Manager</h2>
            <p class="text-xs text-gray-500 mt-1">Informasi lengkap surat</p>
        </div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-gray-500">
                <i class="ri-time-line"></i>
                <span>{{ date('d M Y') }}</span>
            </div>
            <a href="{{ route('surat-unit-manager.index') }}" 
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
                $statusColor = $suratUnitManager->status_color;
                $statusText = $suratUnitManager->current_status;
            @endphp
            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full 
                         @if($statusColor == 'success') bg-green-100 text-green-800
                         @elseif($statusColor == 'warning') bg-yellow-100 text-yellow-800
                         @elseif($statusColor == 'danger') bg-red-100 text-red-800
                         @else bg-gray-100 text-gray-800 @endif">
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
                @if($suratUnitManager->files->count() > 0)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="ri-attachment-2 mr-2 text-gray-600"></i>
                        Lampiran ({{ $suratUnitManager->files->count() }} file)
                    </h3>
                    <div class="space-y-3">
                        @foreach($suratUnitManager->files as $file)
                        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md">
                            <div class="flex items-center space-x-3">
                                <i class="{{ $file->file_icon }} text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $file->formatted_file_size }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('surat-unit-manager.preview-file', [$suratUnitManager->id, $file->id]) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-1 rounded-md hover:bg-blue-50" title="Preview" target="_blank">
                                    <i class="ri-eye-line"></i> Preview
                                </a>
                                <a href="{{ route('surat-unit-manager.download-file', [$suratUnitManager->id, $file->id]) }}" 
                                   class="text-green-600 hover:text-green-800 p-1 rounded-md hover:bg-green-50" title="Download">
                                    <i class="ri-download-line"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($suratUnitManager->files->count() > 1)
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <a href="{{ route('surat-unit-manager.download', $suratUnitManager->id) }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="ri-download-2-line mr-1"></i>
                            Download Semua File (ZIP)
                        </a>
                    </div>
                    @endif
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

                <!-- Manager Info -->
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <i class="ri-user-star-line mr-2 text-green-600"></i>
                        Manager
                    </h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="ri-user-star-line text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-green-900">{{ $suratUnitManager->manager->name }}</p>
                            <p class="text-sm text-green-700">{{ optional($suratUnitManager->manager->jabatan)->nama_jabatan }}</p>
                            <p class="text-xs text-green-600">{{ $suratUnitManager->manager->email }}</p>
                        </div>
                    </div>
                    
                    <!-- Manager Status -->
                    <div class="mt-4">
                        @php
                            $managerStatusColor = $suratUnitManager->status_manager === 'approved' ? 'green' : 
                                                ($suratUnitManager->status_manager === 'rejected' ? 'red' : 'yellow');
                            $managerStatusText = $suratUnitManager->status_manager === 'approved' ? 'Disetujui' : 
                                               ($suratUnitManager->status_manager === 'rejected' ? 'Ditolak' : 'Menunggu');
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                     @if($managerStatusColor == 'green') bg-green-100 text-green-800
                                     @elseif($managerStatusColor == 'red') bg-red-100 text-red-800
                                     @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $managerStatusText }}
                        </span>
                        @if($suratUnitManager->waktu_review_manager)
                        <p class="text-xs text-green-600 mt-1">
                            {{ $suratUnitManager->waktu_review_manager->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                    
                    @if($suratUnitManager->keterangan_manager)
                    <div class="mt-4 p-3 bg-white rounded-md">
                        <label class="block text-sm font-medium text-green-700 mb-1">Keterangan Manager:</label>
                        <p class="text-sm text-green-800">{{ $suratUnitManager->keterangan_manager }}</p>
                    </div>
                    @endif
                </div>

                <!-- Secretary Info -->
                @if($suratUnitManager->sekretaris)
                <div class="bg-purple-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                        <i class="ri-user-settings-line mr-2 text-purple-600"></i>
                        Sekretaris
                    </h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="ri-user-settings-line text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-purple-900">{{ $suratUnitManager->sekretaris->name }}</p>
                            <p class="text-sm text-purple-700">{{ optional($suratUnitManager->sekretaris->jabatan)->nama_jabatan }}</p>
                        </div>
                    </div>
                    
                    <!-- Secretary Status -->
                    <div class="mt-4">
                        @php
                            $secretaryStatusColor = $suratUnitManager->status_sekretaris === 'approved' ? 'green' : 
                                                  ($suratUnitManager->status_sekretaris === 'rejected' ? 'red' : 'yellow');
                            $secretaryStatusText = $suratUnitManager->status_sekretaris === 'approved' ? 'Disetujui' : 
                                                 ($suratUnitManager->status_sekretaris === 'rejected' ? 'Ditolak' : 'Menunggu');
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                     @if($secretaryStatusColor == 'green') bg-green-100 text-green-800
                                     @elseif($secretaryStatusColor == 'red') bg-red-100 text-red-800
                                     @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $secretaryStatusText }}
                        </span>
                        @if($suratUnitManager->waktu_review_sekretaris)
                        <p class="text-xs text-purple-600 mt-1">
                            {{ $suratUnitManager->waktu_review_sekretaris->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                    
                    @if($suratUnitManager->keterangan_sekretaris)
                    <div class="mt-4 p-3 bg-white rounded-md">
                        <label class="block text-sm font-medium text-purple-700 mb-1">Keterangan Sekretaris:</label>
                        <p class="text-sm text-purple-800">{{ $suratUnitManager->keterangan_sekretaris }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Director Info -->
                @if($suratUnitManager->dirut)
                <div class="bg-orange-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                        <i class="ri-user-3-line mr-2 text-orange-600"></i>
                        Direktur
                    </h3>
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="ri-user-3-line text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-orange-900">{{ $suratUnitManager->dirut->name }}</p>
                            <p class="text-sm text-orange-700">{{ optional($suratUnitManager->dirut->jabatan)->nama_jabatan }}</p>
                        </div>
                    </div>
                    
                    <!-- Director Status -->
                    <div class="mt-4">
                        @php
                            $directorStatusColor = $suratUnitManager->status_dirut === 'approved' ? 'green' : 
                                                 ($suratUnitManager->status_dirut === 'rejected' ? 'red' : 'yellow');
                            $directorStatusText = $suratUnitManager->status_dirut === 'approved' ? 'Disetujui' : 
                                                ($suratUnitManager->status_dirut === 'rejected' ? 'Ditolak' : 'Menunggu');
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                     @if($directorStatusColor == 'green') bg-green-100 text-green-800
                                     @elseif($directorStatusColor == 'red') bg-red-100 text-red-800
                                     @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $directorStatusText }}
                        </span>
                        @if($suratUnitManager->waktu_review_dirut)
                        <p class="text-xs text-orange-600 mt-1">
                            {{ $suratUnitManager->waktu_review_dirut->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                    
                    @if($suratUnitManager->keterangan_dirut)
                    <div class="mt-4 p-3 bg-white rounded-md">
                        <label class="block text-sm font-medium text-orange-700 mb-1">Keterangan Direktur:</label>
                        <p class="text-sm text-orange-800">{{ $suratUnitManager->keterangan_dirut }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            @if(auth()->user()->role == 0 && $suratUnitManager->unit_id == auth()->id() && $suratUnitManager->status_manager == 'pending')
            <a href="{{ route('surat-unit-manager.edit', $suratUnitManager->id) }}" 
               class="px-6 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                <i class="ri-edit-line"></i>
                <span>Edit Surat</span>
            </a>
            @endif
            
            @if($suratUnitManager->files->count() > 0)
            <a href="{{ route('surat-unit-manager.download', $suratUnitManager->id) }}" 
               class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                <i class="ri-download-line"></i>
                <span>{{ $suratUnitManager->files->count() > 1 ? 'Download ZIP' : 'Download File' }}</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection 
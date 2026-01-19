@props(['suratId', 'createdBy'])

@php
    $user = auth()->user();
    $userRole = $user->role ?? 0;
    $userId = $user->id;
    
    // Role definitions
    $isStaff = $userRole === 0;
    $isSekretaris = $userRole === 1;
    $isDirektur = $userRole === 2;
    $isAdmin = $userRole === 3;
    
    // Permission checks
    $canEdit = ($createdBy == $userId) || $isAdmin;
    $canDelete = ($createdBy == $userId) || $isAdmin;
    $canEditDisposisi = $isSekretaris || $isDirektur || $isAdmin;
@endphp

<div class="flex items-center space-x-2">
    {{-- Detail Button - Semua user bisa lihat --}}
    <button onclick="showDetailSurat({{ $suratId }})" 
            class="text-blue-600 hover:text-blue-800 transition-colors"
            title="Lihat Detail">
        <i class="ri-eye-line text-lg"></i>
    </button>

    {{-- Edit - Hanya untuk pembuat surat atau admin --}}
    @if($canEdit)
        <a href="/suratkeluar/{{ $suratId }}/edit" 
           class="text-green-600 hover:text-green-800 transition-colors"
           title="Edit Surat">
            <i class="ri-edit-line text-lg"></i>
        </a>
    @endif
    
    {{-- Delete - Hanya untuk pembuat surat atau admin --}}
    @if($canDelete)
        <button onclick="deleteSurat({{ $suratId }})" 
                class="text-red-600 hover:text-red-800 transition-colors"
                title="Hapus Surat">
            <i class="ri-delete-bin-line text-lg"></i>
        </button>
    @endif

    {{-- Edit Disposisi - Untuk sekretaris, direktur, dan admin --}}
    @if($canEditDisposisi)
        <button onclick="openEditDisposisiModal({{ $suratId }})" 
                class="text-purple-600 hover:text-purple-800 transition-colors"
                title="Edit Disposisi">
            <i class="ri-file-edit-line text-lg"></i>
        </button>
    @endif
</div>


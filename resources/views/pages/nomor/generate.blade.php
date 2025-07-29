@extends('home')

@section('title', 'Generate Nomor Surat - SISM Azra')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Generate Nomor Surat</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola dan generate nomor surat sesuai jenis</p>
        </div>
    </div>
    
    <div class="p-8">
        <!-- Tabs untuk jenis nomor surat -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button id="tab-umum" class="tab-button border-b-2 border-green-500 py-2 px-1 text-sm font-medium text-green-600" data-tab="umum">
                        Umum (Internal)
                    </button>
                    <button id="tab-diradm" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="diradm">
                        Manajemen Adm & Keuangan
                    </button>
                    <button id="tab-dirrs" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="dirrs">
                        Direktur RS
                    </button>
                    <button id="tab-asp" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="asp">
                        ASP
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div id="tab-content-umum" class="tab-content">
            @include('pages.nomor.components.nomor-table', [
                'jenis' => 'umum',
                'title' => 'Nomor Surat Umum (Internal)',
                'format' => 'Nomor/RSAZRA/Bulan/Tahun',
                'kode' => 'RSAZRA'
            ])
        </div>

        <div id="tab-content-diradm" class="tab-content hidden">
            @include('pages.nomor.components.nomor-table', [
                'jenis' => 'diradm',
                'title' => 'Nomor Surat Manajemen Adm & Keuangan',
                'format' => 'Nomor/Dir.Adm.Keu/RSAZRA/Bulan/Tahun',
                'kode' => 'Dir.Adm.Keu'
            ])
        </div>

        <div id="tab-content-dirrs" class="tab-content hidden">
            @include('pages.nomor.components.nomor-table', [
                'jenis' => 'dirrs',
                'title' => 'Nomor Surat Direktur RS',
                'format' => 'Nomor/DIRRS/RSAZRA/Bulan/Tahun',
                'kode' => 'DIRRS'
            ])
        </div>

        <div id="tab-content-asp" class="tab-content hidden">
            @include('pages.nomor.components.nomor-table', [
                'jenis' => 'asp',
                'title' => 'Nomor Surat ASP',
                'format' => 'Nomor/ASP/Bulan/Tahun',
                'kode' => 'ASP'
            ])
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // T ab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update button states
            tabButtons.forEach(btn => {
                btn.classList.remove('border-green-500', 'text-green-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-green-500', 'text-green-600');
            
            // Show/hide content
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`tab-content-${targetTab}`).classList.remove('hidden');
        });
    });
});
</script>

<style>
.tab-button:hover {
    border-color: #10B981;
    color: #10B981;
}
</style> 
{{-- @extends('home')

@section('title', 'Disposisi - SISM Azra')

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div x-data="suratKeluarModal">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <!-- Header Section -->
            <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Form Disposisi</h2>
                    <p class="text-xs text-gray-500 mt-1">Silakan isi data disposisi dengan lengkap</p>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="ri-time-line"></i>
                    <span>{{ date('d M Y') }}</span>
                </div>
            </div>

            <!-- Form Section -->
            <form class="p-8" @submit.prevent="submitForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Kolom Kiri -->
                    <div class="space-y-6">
                        <!-- Jenis Disposisi dengan ikon -->
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="ri-barcode-line text-gray-400 mr-2"></i>
                                <label class="text-sm font-semibold text-gray-800">Indeks</label>
                            </div>
                            <div class="flex space-x-2">
                                <input type="text" id="index_surat" name="index_surat" x-model="selectedSurat.id"
                                    @input="handleIndexChange($event.target.value)"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                                    placeholder="Masukkan kode unik">
                                <button type="button" @click="openModal()"
                                    class="px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl space-y-3">
                            <div class="flex items-center">
                                <i class="ri-git-branch-line text-gray-400 mr-2"></i>
                                <label class="text-sm font-semibold text-gray-800">Jenis Disposisi</label>
                            </div>
                            <div class="flex space-x-6">
                                <label class="relative flex items-center group">
                                    <input type="radio" name="jenis_disposisi" value="internal" checked
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">Internal</span>
                                </label>
                                <label class="relative flex items-center group">
                                    <input type="radio" name="jenis_disposisi" value="external"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">External</span>
                                </label>
                            </div>
                        </div>

                        <!-- Status Penyelesaian -->
                        <div class="bg-gray-50 p-6 rounded-xl space-y-3">
                            <div class="flex items-center">
                                <i class="ri-time-line text-gray-400 mr-2"></i>
                                <label class="text-sm font-semibold text-gray-800">Status Penyelesaian</label>
                            </div>
                            <div class="flex space-x-6">
                                <label class="relative flex items-center group">
                                    <input type="radio" name="status" value="selesai"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">✅ Selesai</span>
                                </label>
                                <label class="relative flex items-center group">
                                    <input type="radio" name="status" value="belum selesai" checked
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">⏱ Belum
                                        Selesai</span>
                                </label>
                            </div>
                        </div>

                        <!-- Tingkat Kepentingan -->
                        <div class="bg-gray-50 p-6 rounded-xl space-y-3">
                            <div class="flex items-center">
                                <i class="ri-shield-star-line text-gray-400 mr-2"></i>
                                <label class="text-sm font-semibold text-gray-800">Tingkat Kepentingan</label>
                            </div>
                            <div class="flex space-x-6">
                                <label class="relative flex items-center group">
                                    <input type="radio" name="tingkat_kepentingan" value="rahasia"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">🔒 Rahasia</span>
                                </label>
                                <label class="relative flex items-center group">
                                    <input type="radio" name="tingkat_kepentingan" value="penting"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">⚡ Penting</span>
                                </label>
                                <label class="relative flex items-center group">
                                    <input type="radio" name="tingkat_kepentingan" value="biasa"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">📝 Biasa</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="space-y-6">
                        <!-- Jenis Perusahaan -->
                        <div class="bg-gray-50 p-6 rounded-xl space-y-3">
                            <div class="flex items-center">
                                <i class="ri-hospital-line text-gray-400 mr-2"></i>
                                <label class="text-sm font-semibold text-gray-800">Jenis Perusahaan</label>
                            </div>
                            <div class="flex space-x-6">
                                <label class="relative flex items-center group">
                                    <input type="radio" name="jenis_perusahaan" value="RSAZRA"
                                        :checked="selectedSurat.perusahaan && selectedSurat.perusahaan
                                            .toUpperCase() === 'RSAZRA'"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">RS Azra</span>
                                </label>
                                <label class="relative flex items-center group">
                                    <input type="radio" name="jenis_perusahaan" value="DIRUT"
                                        :checked="selectedSurat.perusahaan && selectedSurat.perusahaan
                                            .toUpperCase() === 'DIRUT'"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">DIRUT</span>
                                </label>
                                <label class="relative flex items-center group">
                                    <input type="radio" name="jenis_perusahaan" value="ASP"
                                        :checked="selectedSurat.perusahaan && selectedSurat.perusahaan
                                            .toUpperCase() === 'ASP'"
                                        class="w-5 h-5 border-2 border-gray-300 rounded-full focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-green-600 transition">
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-green-600">ASP</span>
                                </label>
                            </div>
                            <!-- Debug output -->
                            <div class="text-sm text-gray-500" x-text="'Selected: ' + selectedSurat.perusahaan"></div>
                        </div>

                        <!-- Hal -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Hal</label>
                            <input type="text" name="hal" x-model="selectedSurat.perihal"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                                placeholder="Perihal surat" readonly>
                        </div>

                        <!-- Tanggal dan No Surat -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-800">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" x-model="selectedSurat.tanggal_surat"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                                    readonly>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-800">Nomor Surat</label>
                                <input type="text" name="nomor_surat" x-model="selectedSurat.nomor_surat"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                                    placeholder="No. Surat" readonly>
                            </div>
                        </div>

                        <!-- Asal Surat -->
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-gray-800">Asal Surat</label>
                            <input type="text" x-model="userInfo"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                                placeholder="Asal surat" readonly>
                        </div>
                    </div>
                </div>

                <!-- Full Width Fields -->
                <div class="mt-8 space-y-6">
                    <!-- Instruksi/Informasi -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800">Instruksi/Informasi</label>
                        <textarea name="instruksi"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                            rows="4" placeholder="Masukkan instruksi atau informasi"></textarea>
                    </div>

                    <!-- Diteruskan Kepada -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800">Diteruskan Kepada</label>
                        <div id="hs-combobox-basic-usage" class="relative" x-data="comboboxData"
                            @click.away="closeDropdown()">
                            <div class="relative">
                                <input type="text" x-model="searchUser" @input="filterUsers" @focus="openDropdown"
                                    class="py-2.5 sm:py-3 ps-4 pe-9 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Cari berdasarkan nama atau jabatan..." role="combobox"
                                    aria-expanded="false">
                                <div class="absolute top-1/2 end-3 -translate-y-1/2" role="button"
                                    @click="toggleDropdown">
                                    <svg class="shrink-0 size-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="m7 15 5 5 5-5"></path>
                                        <path d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="isOpen"
                                class="absolute z-50 w-full max-h-72 p-1 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto"
                                role="listbox">
                                <template x-for="user in filteredUsers" :key="user.id">
                                    <div class="cursor-pointer py-2 px-4 w-full text-sm text-gray-800 hover:bg-gray-100 rounded-lg"
                                        @click="selectUser(user)" role="option">
                                        <div class="flex justify-between items-center w-full">
                                            <span x-text="user.displayText"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-800">Catatan</label>
                        <textarea name="catatan"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 transition duration-200"
                            rows="4" placeholder="Tambahkan catatan jika ada"></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" @click="resetForm"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="ri-close-line mr-2"></i>
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 text-sm font-medium text-white bg-green-600 border border-transparent rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="ri-save-line mr-2"></i>
                        Simpan Disposisi
                    </button>
                </div>
            </form>
        </div>

        <!-- Modal (dengan x-cloak) -->
        <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!-- Modal panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <!-- Modal content -->
                        <div class="flex justify-between items-center pb-3">
                            <h3 class="text-lg font-semibold">Pilih Surat Keluar</h3>
                            <button @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>

                        <!-- Search Box -->
                        <div class="mb-4">
                            <input type="text" x-model="searchTerm" @input="searchSurat()"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300" placeholder="Cari surat...">
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Surat</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Perihal</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Perusahaan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="surat in filteredSuratKeluar" :key="surat.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap" x-text="surat.id"></td>
                                            <td class="px-6 py-4 whitespace-nowrap" x-text="surat.nomor_surat"></td>
                                            <td class="px-6 py-4 whitespace-nowrap" x-text="surat.tanggal_surat"></td>
                                            <td class="px-6 py-4 whitespace-nowrap" x-text="surat.perihal"></td>
                                            <td class="px-6 py-4 whitespace-nowrap" x-text="surat.perusahaan"></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <button @click="selectSurat(surat)"
                                                    class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                                    Pilih
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('suratKeluarModal', () => ({
                isOpen: false,
                suratKeluar: [],
                filteredSuratKeluar: [],
                selectedSurat: {
                    id: '',
                    nomor_surat: '',
                    tanggal_surat: '',
                    perihal: '',
                    perusahaan: ''
                },
                searchTerm: '',
                userInfo: '{{ Auth::user()->id }} - {{ Auth::user()->name }} ({{ Auth::user()->jabatanName }})',
                selectedUser: '',
                users: [],

                init() {
                    console.log('Modal initialized');
                    this.fetchSuratKeluar();
                    this.fetchUsers();
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toISOString().split('T')[0];
                },

                async fetchSuratKeluar() {
                    try {
                        console.log('Fetching data...');
                        const response = await fetch('/api/surat-keluar');
                        const data = await response.json();
                        this.suratKeluar = data.map(surat => ({
                            ...surat,
                            tanggal_surat: this.formatDate(surat.tanggal_surat)
                        }));
                        this.filteredSuratKeluar = this.suratKeluar;
                    } catch (error) {
                        console.error('Error fetching surat keluar:', error);
                    }
                },

                async handleIndexChange(value) {
                    console.log('Index changed:', value);
                    if (!value) {
                        this.resetForm();
                        return;
                    }

                    const surat = this.suratKeluar.find(s => s.id.toString() === value.toString());

                    if (surat) {
                        console.log('Surat found:', surat);
                        this.selectedSurat = {
                            id: surat.id,
                            nomor_surat: surat.nomor_surat,
                            tanggal_surat: this.formatDate(surat.tanggal_surat),
                            perihal: surat.perihal,
                            perusahaan: surat.perusahaan.toUpperCase()
                        };
                        console.log('Updated selectedSurat:', this.selectedSurat);
                    } else {
                        console.log('Surat not found');
                        this.resetForm();
                    }
                },

                formatPerusahaan(perusahaan) {
                    const mapping = {
                        'rs_azra': 'RS Azra',
                        'asp': 'ASP',
                        'isp': 'ISP',
                        'RS AZRA': 'RS Azra',
                        'ASP': 'ASP',
                        'ISP': 'ISP'
                    };
                    return mapping[perusahaan] || perusahaan;
                },

                resetForm() {
                    this.selectedSurat = {
                        id: '',
                        nomor_surat: '',
                        tanggal_surat: '',
                        perihal: '',
                        perusahaan: ''
                    };
                },

                openModal() {
                    console.log('Opening modal');
                    this.isOpen = true;
                },

                closeModal() {
                    console.log('Closing modal');
                    this.isOpen = false;
                    this.searchTerm = '';
                    this.filteredSuratKeluar = this.suratKeluar;
                },

                selectSurat(surat) {
                    console.log('Selecting surat:', surat);
                    this.selectedSurat = {
                        id: surat.id,
                        nomor_surat: surat.nomor_surat,
                        tanggal_surat: this.formatDate(surat.tanggal_surat),
                        perihal: surat.perihal,
                        perusahaan: surat.perusahaan.toUpperCase()
                    };
                    console.log('Updated selectedSurat:', this.selectedSurat);
                    this.closeModal();
                },

                searchSurat() {
                    const searchTerm = this.searchTerm.toLowerCase();
                    this.filteredSuratKeluar = this.suratKeluar.filter(surat =>
                        surat.nomor_surat.toLowerCase().includes(searchTerm) ||
                        surat.perihal.toLowerCase().includes(searchTerm) ||
                        surat.perusahaan.toLowerCase().includes(searchTerm)
                    );
                },

                async fetchUsers() {
                    try {
                        const response = await fetch('/api/users');
                        const data = await response.json();
                        const currentUserId = {{ Auth::id() }};

                        // Filter dan format data users
                        this.users = data
                            .filter(user => user.id !== currentUserId)
                            .map(user => ({
                                id: user.id,
                                name: user.name,
                                jabatan: user.jabatan || 'Tidak ada jabatan',
                                email: user.email,
                                displayText: `${user.name} - ${user.jabatan || 'Tidak ada jabatan'} (${user.email})`
                            }))
                            .sort((a, b) => a.name.localeCompare(b
                                .name)); // Urutkan berdasarkan nama

                        console.log('Users loaded:', this.users);
                    } catch (error) {
                        console.error('Error fetching users:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data users'
                        });
                    }
                },

                submitForm() {
                    // Validasi form sebelum submit
                    if (!this.selectedSurat.id) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih surat terlebih dahulu'
                        });
                        return;
                    }

                    if (!this.selectedUser) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih penerima disposisi'
                        });
                        return;
                    }

                    // Dapatkan data user yang dipilih
                    const selectedUserData = this.users.find(user => user.id.toString() === this
                        .selectedUser.toString());

                    // Ambil hanya ID dari userInfo
                    const userId = this.userInfo.split(' - ')[0]; // Akan mengambil ID saja

                    // Konfirmasi sebelum menyimpan
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin menyimpan disposisi ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan loading
                            Swal.fire({
                                title: 'Sedang Memproses...',
                                html: `
                                    <div class="flex flex-col items-center">
                                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mb-3"></div>
                                        <div class="text-sm text-gray-500">Menyimpan disposisi dan mengirim email...</div>
                                    </div>
                                `,
                                allowOutsideClick: false,
                                showConfirmButton: false
                            });

                            // Lanjutkan dengan pengiriman form
                            const formData = {
                                kd_surat_keluar: this.selectedSurat.id,
                                jenis_disposisi: document.querySelector(
                                    'input[name="jenis_disposisi"]:checked')?.value,
                                status_penyelesaian: document.querySelector(
                                    'input[name="status"]:checked')?.value,
                                tingkat_kepentingan: document.querySelector(
                                    'input[name="tingkat_kepentingan"]:checked')?.value,
                                instruksi: document.querySelector(
                                    'textarea[name="instruksi"]')?.value || '',
                                diteruskan_kepada: this.selectedUser,
                                catatan: document.querySelector('textarea[name="catatan"]')
                                    ?.value || '',
                                email_penerima: selectedUserData.email,
                                nama_penerima: selectedUserData.name,
                                asal_surat: userId // Kirim hanya ID user
                            };

                            // Ambil CSRF token
                            const token = document.querySelector('meta[name="csrf-token"]')
                                ?.content;
                            if (!token) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'CSRF token tidak ditemukan'
                                });
                                return;
                            }

                            // Kirim request
                            fetch('/disposisi', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(formData)
                                })
                                .then(async response => {
                                    const data = await response.json();
                                    if (!response.ok) {
                                        throw new Error(data.message ||
                                            `HTTP error! status: ${response.status}`
                                        );
                                    }
                                    return data;
                                })
                                .then(data => {
                                    if (data.status === 'success') {
                                        // Tutup loading dan tampilkan pesan sukses
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: data.message,
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#10B981'
                                        }).then(() => {
                                            this.resetForm();
                                            window.location.reload();
                                        });
                                    } else {
                                        throw new Error(data.message ||
                                            'Terjadi kesalahan saat menyimpan disposisi'
                                        );
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    // Tutup loading dan tampilkan pesan error
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: error.message ||
                                            'Terjadi kesalahan saat menyimpan disposisi',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#10B981'
                                    });
                                });
                        }
                    });
                }
            }));

            Alpine.data('comboboxData', () => ({
                searchUser: '',
                isOpen: false,
                filteredUsers: [],

                init() {
                    this.filteredUsers = this.users;
                },

                openDropdown() {
                    this.isOpen = true;
                },

                closeDropdown() {
                    this.isOpen = false;
                },

                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                },

                filterUsers() {
                    const searchTerm = this.searchUser.toLowerCase();
                    this.filteredUsers = this.users.filter(user =>
                        user.name.toLowerCase().includes(searchTerm) ||
                        user.jabatan.toLowerCase().includes(searchTerm)
                    );
                },

                selectUser(user) {
                    this.searchUser = user.displayText;
                    this.selectedUser = user.id;
                    this.closeDropdown();
                }
            }));
        });
    </script>
@endpush --}}

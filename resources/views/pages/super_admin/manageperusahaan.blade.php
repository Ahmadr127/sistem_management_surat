@extends('home')

@section('title', 'Kelola Perusahaan - SISM Azra')

@section('content')
<div x-data="perusahaanManager" class="h-full">
    <!-- Header Section -->
    <div class="mb-6 flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Kelola Perusahaan</h1>
            <p class="text-sm text-gray-500 mt-1">Tambah, edit, dan hapus data perusahaan yang tersedia dalam sistem</p>
        </div>
        <button @click="openCreateModal" 
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="ri-add-line mr-2"></i> Tambah Perusahaan
        </button>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-white uppercase tracking-wider bg-green-600 border-b border-gray-200">
                        <th class="px-6 py-3.5">Kode</th>
                        <th class="px-6 py-3.5">Nama Perusahaan</th>
                        <th class="px-6 py-3.5">Alamat</th>
                        <th class="px-6 py-3.5">Telepon</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(perusahaan, index) in paginatedPerusahaans" :key="perusahaan.id">
                        <tr class="text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 font-medium" x-text="perusahaan.kode"></td>
                            <td class="px-6 py-4" x-text="perusahaan.nama_perusahaan"></td>
                            <td class="px-6 py-4" x-text="perusahaan.alamat || '-'"></td>
                            <td class="px-6 py-4" x-text="perusahaan.telepon || '-'"></td>
                            <td class="px-6 py-4" x-text="perusahaan.email || '-'"></td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-green-100 text-green-800': perusahaan.status === 'aktif',
                                    'bg-red-100 text-red-800': perusahaan.status === 'nonaktif'
                                }" class="px-2 py-1 text-xs font-medium rounded-full" x-text="perusahaan.status"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button @click="openEditModal(perusahaan)" class="text-blue-600 hover:text-blue-800">
                                        <i class="ri-edit-line mr-1"></i> Edit
                                    </button>
                                    <button @click="confirmDelete(perusahaan)" class="text-red-600 hover:text-red-800">
                                        <i class="ri-delete-bin-line mr-1"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Menampilkan <span x-text="pageStart"></span> - <span x-text="pageEnd"></span> dari <span x-text="filteredPerusahaans.length"></span> data
            </div>
            <div class="flex gap-1">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1 border rounded-l bg-white text-gray-700 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === 1}">&laquo;</button>
                <template x-for="page in totalPages" :key="page">
                    <button @click="changePage(page)" :class="{'bg-green-100 text-green-700 font-bold': currentPage === page, 'bg-white text-gray-700': currentPage !== page}" class="px-3 py-1 border-t border-b" x-text="page"></button>
                </template>
                <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded-r bg-white text-gray-700 hover:bg-gray-50" :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}">&raquo;</button>
            </div>
        </div>

        <!-- Empty State -->
        <div class="p-8 text-center" x-show="perusahaans.length === 0">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="ri-building-line text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 font-medium">Belum ada data perusahaan</p>
            <p class="text-sm text-gray-400 mt-1">Tambahkan perusahaan baru untuk mulai mengelola</p>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="isEditing ? 'Edit Perusahaan' : 'Tambah Perusahaan'"></h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Kode Perusahaan <span class="text-red-600">*</span>
                                </label>
                                <input type="text" x-model="formData.kode" :disabled="isEditing"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    :class="{'bg-gray-100': isEditing}" required>
                                <p x-show="errors.kode" x-text="errors.kode" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Perusahaan <span class="text-red-600">*</span>
                                </label>
                                <input type="text" x-model="formData.nama_perusahaan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" required>
                                <p x-show="errors.nama_perusahaan" x-text="errors.nama_perusahaan" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat
                                </label>
                                <textarea x-model="formData.alamat"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    rows="3"></textarea>
                                <p x-show="errors.alamat" x-text="errors.alamat" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon
                                </label>
                                <input type="text" x-model="formData.telepon"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <p x-show="errors.telepon" x-text="errors.telepon" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input type="email" x-model="formData.email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <div x-show="isEditing">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <select x-model="formData.status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                                <p x-show="errors.status" x-text="errors.status" class="mt-1 text-sm text-red-600"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <span x-text="isEditing ? 'Simpan Perubahan' : 'Tambah Perusahaan'"></span>
                        </button>
                        <button type="button" @click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('perusahaanManager', () => ({
            perusahaans: @json($perusahaans),
            showModal: false,
            isEditing: false,
            formData: {
                id: null,
                kode: '',
                nama_perusahaan: '',
                alamat: '',
                telepon: '',
                email: '',
                status: 'aktif'
            },
            errors: {},
            currentPage: 1,
            itemsPerPage: 10,
            get filteredPerusahaans() {
                if (!this.searchQuery || this.searchQuery.trim() === '') return this.perusahaans;
                return this.perusahaans.filter(p => p.nama_perusahaan.toLowerCase().includes(this.searchQuery.toLowerCase()));
            },
            get totalPages() {
                return Math.ceil(this.filteredPerusahaans.length / this.itemsPerPage) || 1;
            },
            get paginatedPerusahaans() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredPerusahaans.slice(start, start + this.itemsPerPage);
            },
            get pageStart() {
                if (this.filteredPerusahaans.length === 0) return 0;
                return (this.currentPage - 1) * this.itemsPerPage + 1;
            },
            get pageEnd() {
                return Math.min(this.currentPage * this.itemsPerPage, this.filteredPerusahaans.length);
            },
            changePage(page) {
                if (page < 1 || page > this.totalPages) return;
                this.currentPage = page;
            },
            init() {
                this.$watch('searchQuery', () => { this.currentPage = 1; });
            },
            openCreateModal() {
                this.isEditing = false;
                this.formData = {
                    kode: '',
                    nama_perusahaan: '',
                    alamat: '',
                    telepon: '',
                    email: '',
                    status: 'aktif'
                };
                this.errors = {};
                this.showModal = true;
            },

            openEditModal(perusahaan) {
                this.isEditing = true;
                this.formData = { ...perusahaan };
                this.errors = {};
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            async submitForm() {
                try {
                    this.errors = {};
                    const url = this.isEditing 
                        ? `/api/perusahaan/${this.formData.id}` 
                        : '/api/perusahaan';
                    
                    const method = this.isEditing ? 'PUT' : 'POST';
                    
                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            this.errors = result.errors;
                            return;
                        }
                        throw new Error(result.message || 'Terjadi kesalahan');
                    }

                    if (this.isEditing) {
                        const index = this.perusahaans.findIndex(p => p.id === this.formData.id);
                        if (index !== -1) {
                            this.perusahaans[index] = result.data;
                        }
                    } else {
                        this.perusahaans.push(result.data);
                    }

                    this.closeModal();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menyimpan data',
                    });
                }
            },

            async confirmDelete(perusahaan) {
                try {
                    const result = await Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: `Perusahaan "${perusahaan.nama_perusahaan}" akan dihapus`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    });

                    if (result.isConfirmed) {
                        await this.deletePerusahaan(perusahaan.id);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },

            async deletePerusahaan(id) {
                try {
                    const response = await fetch(`/api/perusahaan/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.message || 'Terjadi kesalahan saat menghapus');
                    }

                    // Remove from array
                    this.perusahaans = this.perusahaans.filter(p => p.id !== id);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menghapus data',
                    });
                }
            }
        }));
    });
</script>
@endpush

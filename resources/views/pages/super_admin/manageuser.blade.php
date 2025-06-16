@extends('home')

@section('title', 'Manajemen User - SISM Azra')

@section('content')
    <div x-data="userManagement" class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Manajemen User</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola semua pengguna sistem</p>
                </div>
                <button @click="openCreateModal()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                    <i class="ri-user-add-line"></i>
                    Tambah User
                </button>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Cari User</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery"
                            class="w-full pl-10 pr-4 py-2.5 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200"
                            placeholder="Cari berdasarkan nama atau email...">
                        <i class="ri-search-line absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Filter Role</label>
                    <select x-model="roleFilter"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="">Semua Role</option>
                        <option value="0">Staff</option>
                        <option value="1">Sekretaris</option>
                        <option value="2">Direktur</option>
                        <option value="3">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Status Akun</label>
                    <select x-model="statusFilter"
                        class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Username
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jabatan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <template x-for="user in paginatedUsers" :key="user.id">
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img :src="user.foto_url" class="h-10 w-10 rounded-full object-cover"
                                                :alt="user.name"
                                                onerror="this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&color=7F9CF5&background=EBF4FF'">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                            <div class="text-sm text-gray-500"
                                                x-text="'Bergabung ' + formatDate(user.created_at)"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.username"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="user.email"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-purple-100 text-purple-800': user.role === 3,
                                            'bg-blue-100 text-blue-800': user.role === 1,
                                            'bg-green-100 text-green-800': user.role === 0,
                                            'bg-yellow-100 text-yellow-800': user.role === 2
                                        }"
                                        x-text="formatRole(user.role)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="user.jabatan?.nama_jabatan || 'Tidak ada jabatan'"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': user.status_akun === 'aktif',
                                            'bg-red-100 text-red-800': user.status_akun === 'nonaktif'
                                        }"
                                        x-text="user.status_akun">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800">
                                            <i class="ri-edit-line text-lg"></i>
                                        </button>
                                        <button @click="toggleStatus(user)" class="text-yellow-600 hover:text-yellow-800">
                                            <i class="ri-toggle-line text-lg"></i>
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
                    Menampilkan <span x-text="pageStart"></span> - <span x-text="pageEnd"></span> dari <span x-text="filteredUsers.length"></span> data
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
            <div x-show="filteredUsers.length === 0" class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <i class="ri-user-search-line text-3xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada user yang ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
            </div>
        </div>

        <!-- Modal Form -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showModal = false"></div>

                <div
                    class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
                    <div class="bg-gray-50 p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900"
                                x-text="formMode === 'create' ? 'Tambah User Baru' : 'Edit User'"></h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" x-model="formData.name" required
                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Username -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input type="text" x-model="formData.username" required
                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Email -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" x-model="formData.email" required
                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Password -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Password
                                    <span x-show="formMode === 'edit'" class="text-sm text-gray-500">(Kosongkan jika tidak
                                        ingin mengubah)</span>
                                </label>
                                <input type="password" x-model="formData.password" :required="formMode === 'create'"
                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <select x-model="formData.role" required
                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="0">Staff</option>
                                    <option value="1">Sekretaris</option>
                                    <option value="2">Direktur</option>
                                    <option value="3">Admin</option>
                                </select>
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                <select x-model="formData.jabatan_id" required
                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <template x-for="jabatan in jabatanList" :key="jabatan.id">
                                        <option :value="jabatan.id" x-text="jabatan.nama_jabatan"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Akun</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" x-model="formData.status_akun" value="aktif"
                                            class="form-radio text-green-600">
                                        <span class="ml-2">Aktif</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" x-model="formData.status_akun" value="nonaktif"
                                            class="form-radio text-red-600">
                                        <span class="ml-2">Non-Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <span x-text="formMode === 'create' ? 'Tambah User' : 'Simpan Perubahan'"></span>
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
            Alpine.data('userManagement', () => ({
                users: [],
                jabatanList: [],
                searchQuery: '',
                roleFilter: '',
                statusFilter: '',
                showModal: false,
                formMode: 'create',
                formData: {
                    id: null,
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    role: 0, // Default to Staff
                    jabatan_id: '',
                    status_akun: 'aktif'
                },
                currentPage: 1,
                itemsPerPage: 10,

                async init() {
                    await Promise.all([
                        this.fetchUsers(),
                        this.fetchJabatan()
                    ]);
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('roleFilter', () => { this.currentPage = 1; });
                    this.$watch('statusFilter', () => { this.currentPage = 1; });
                },

                async fetchUsers() {
                    try {
                        const response = await fetch('/users', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();
                        this.users = data;
                    } catch (error) {
                        console.error('Error fetching users:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal mengambil data users'
                        });
                    }
                },

                async fetchJabatan() {
                    try {
                        const response = await fetch('/jabatan/data', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const result = await response.json();
                        if (result.status === 'success') {
                            this.jabatanList = result.data.filter(j => j.status === 'aktif');
                            // Initialize form data with first jabatan if available
                            if (this.jabatanList.length > 0 && !this.formData.jabatan_id) {
                                this.formData.jabatan_id = this.jabatanList[0].id;
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching jabatan:', error);
                    }
                },

                get filteredUsers() {
                    return this.users.filter(user => {
                        const matchSearch = user.name.toLowerCase().includes(this
                                .searchQuery.toLowerCase()) ||
                            user.email.toLowerCase().includes(this.searchQuery
                                .toLowerCase());
                        const matchRole = this.roleFilter === '' || user.role == this
                            .roleFilter;
                        const matchStatus = this.statusFilter === '' || user.status_akun ===
                            this.statusFilter;
                        return matchSearch && matchRole && matchStatus;
                    });
                },

                formatRole(role) {
                    const roles = {
                        0: 'Staff',
                        1: 'Sekretaris',
                        2: 'Direktur',
                        3: 'Admin'
                    };
                    return roles[role] || 'Unknown';
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                openCreateModal() {
                    this.formMode = 'create';
                    this.resetForm();
                    // Ensure jabatan_id is set if jabatanList has been loaded
                    if (this.jabatanList.length > 0 && !this.formData.jabatan_id) {
                        this.formData.jabatan_id = this.jabatanList[0].id;
                    }
                    this.showModal = true;
                },

                editUser(user) {
                    this.formMode = 'edit';
                    this.formData = {
                        id: user.id,
                        name: user.name,
                        username: user.username,
                        email: user.email,
                        password: '',
                        role: user.role,
                        jabatan_id: user.jabatan_id,
                        status_akun: user.status_akun
                    };
                    this.showModal = true;
                },

                async submitForm() {
                    try {
                        const url = this.formMode === 'create' ? '/users' :
                            `/users/${this.formData.id}`;

                        // Jika method PUT, gunakan POST dengan _method
                        const formDataToSend = new FormData();
                        Object.keys(this.formData).forEach(key => {
                            // Don't send empty email
                            if (key === 'email' && !this.formData[key]) {
                                return;
                            }
                            formDataToSend.append(key, this.formData[key]);
                        });

                        // Tambahkan _method untuk simulasi PUT
                        if (this.formMode === 'edit') {
                            formDataToSend.append('_method', 'PUT');
                        }

                        const response = await fetch(url, {
                            method: 'POST', // Selalu gunakan POST
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                // Hapus Content-Type, biarkan browser yang mengatur untuk FormData
                            },
                            credentials: 'same-origin', // Penting untuk cookies/session
                            body: formDataToSend
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            // Handle validation errors specially
                            if (response.status === 422 && result.errors) {
                                const errorMessages = Object.values(result.errors).flat().join(
                                    '<br>');
                                throw new Error(errorMessages);
                            }
                            throw new Error(result.message || 'Terjadi kesalahan');
                        }

                        await this.fetchUsers();
                        this.showModal = false;

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: error.message || 'Terjadi kesalahan saat menyimpan data'
                        });
                    }
                },

                async toggleStatus(user) {
                    try {
                        const result = await Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: `Status user akan diubah menjadi ${user.status_akun === 'aktif' ? 'non-aktif' : 'aktif'}`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, ubah status!',
                            cancelButtonText: 'Batal'
                        });

                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('_token', document.querySelector(
                                'meta[name="csrf-token"]').content);

                            const response = await fetch(`/users/${user.id}/toggle-status`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin',
                                body: formData
                            });

                            if (!response.ok) {
                                const error = await response.json();
                                throw new Error(error.message || 'Terjadi kesalahan');
                            }

                            const data = await response.json();
                            await this.fetchUsers();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat mengubah status'
                        });
                    }
                },

                resetForm() {
                    // Set default jabatan_id to the first available jabatan if any exist
                    const defaultJabatanId = this.jabatanList.length > 0 ? this.jabatanList[0].id : '';

                    this.formData = {
                        id: null,
                        name: '',
                        username: '',
                        email: '',
                        password: '',
                        role: 0, // Default to Staff
                        jabatan_id: defaultJabatanId,
                        status_akun: 'aktif'
                    };
                },

                get totalPages() {
                    return Math.ceil(this.filteredUsers.length / this.itemsPerPage) || 1;
                },
                get paginatedUsers() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    return this.filteredUsers.slice(start, start + this.itemsPerPage);
                },
                get pageStart() {
                    if (this.filteredUsers.length === 0) return 0;
                    return (this.currentPage - 1) * this.itemsPerPage + 1;
                },
                get pageEnd() {
                    return Math.min(this.currentPage * this.itemsPerPage, this.filteredUsers.length);
                },
                changePage(page) {
                    if (page < 1 || page > this.totalPages) return;
                    this.currentPage = page;
                }
            }));
        });
    </script>
@endpush

@extends('home')

@section('title', 'Manajemen User - SISM Azra')

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        /* Custom scrollbar untuk dropdown */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Hide scrollbar for IE, Edge and Firefox */
        .overflow-y-auto {
            -ms-overflow-style: none;
            scrollbar-width: thin;
        }
    </style>
@endpush

@section('content')
    <div x-data="userManagement">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Manajemen User</h1>
                        <p class="text-sm text-gray-500 mt-1">Kelola semua user dalam sistem</p>
                    </div>
                    <a href="{{ route('manageuser.create') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                        <i class="ri-add-line"></i>
                        Tambah User
                    </a>
                </div>
            </div>

            <!-- Search & Filter Section -->
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
                            <option value="4">Manager</option>
                            <option value="6">General Manager</option>
                            <option value="1">Sekretaris</option>
                            <option value="5">Sekretaris ASP</option>
                            <option value="2">Direktur</option>
                            <option value="7">Manager Keuangan</option>
                            <option value="8">Direktur ASP</option>
                            <option value="3">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Filter Status</label>
                        <select x-model="statusFilter"
                            class="w-full py-2.5 px-4 text-sm text-gray-700 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- User Table -->
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
                                    Posisi</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Manager</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    General Manager</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="user in paginatedUsers" :key="user.id">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                                <div class="text-sm text-gray-500" x-text="formatDate(user.created_at)"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="user.username"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900" x-text="user.email || '-'"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-purple-100 text-purple-800': user.role === 3,
                                                'bg-blue-100 text-blue-800': user.role === 1,
                                                'bg-green-100 text-green-800': user.role === 0,
                                                'bg-yellow-100 text-yellow-800': user.role === 2,
                                                'bg-orange-100 text-orange-800': user.role === 4,
                                                'bg-pink-100 text-pink-800': user.role === 5,
                                                'bg-indigo-100 text-indigo-800': user.role === 6,
                                                'bg-teal-100 text-teal-800': user.role === 7,
                                                'bg-cyan-100 text-cyan-800': user.role === 8
                                            }"
                                            x-text="formatRole(user.role)">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        x-text="user.jabatan_name || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span x-text="user.manager ? user.manager.name : '-'"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span x-text="user.general_manager ? user.general_manager.name : '-'"></span>
                                        <span x-show="user.role == 4 && !user.general_manager" class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Independen</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': user.status_akun === 'aktif',
                                                'bg-red-100 text-red-800': user.status_akun === 'nonaktif'
                                            }"
                                            x-text="user.status_akun">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a :href="`/manageuser/${user.id}/edit`"
                                                class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <button @click="toggleUserStatus(user)"
                                                :class="{
                                                    'text-green-600 hover:text-green-900': user.status_akun === 'nonaktif',
                                                    'text-red-600 hover:text-red-900': user.status_akun === 'aktif'
                                                }"
                                                class="transition-colors duration-200">
                                                <i :class="user.status_akun === 'aktif' ? 'ri-user-unfollow-line' : 'ri-user-follow-line'"></i>
                                            </button>
                                            <button @click="confirmDelete(user)"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button @click="changePage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Previous
                        </button>
                        <button @click="changePage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium" x-text="pageStart"></span>
                                to
                                <span class="font-medium" x-text="pageEnd"></span>
                                of
                                <span class="font-medium" x-text="filteredUsers.length"></span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination">
                                <button @click="changePage(currentPage - 1)"
                                    :disabled="currentPage === 1"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="ri-arrow-left-s-line"></i>
                                </button>
                                <template x-for="page in totalPages" :key="page">
                                    <button @click="changePage(page)"
                                        :class="{
                                            'bg-green-50 border-green-500 text-green-600': page === currentPage,
                                            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': page !== currentPage
                                        }"
                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        <span x-text="page"></span>
                                    </button>
                                </template>
                                <button @click="changePage(currentPage + 1)"
                                    :disabled="currentPage === totalPages"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="ri-arrow-right-s-line"></i>
                                </button>
                            </nav>
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
            Alpine.data('userManagement', () => ({
                users: [],
                searchQuery: '',
                roleFilter: '',
                statusFilter: '',
                currentPage: 1,
                itemsPerPage: 10,

                async init() {
                    console.log('Initializing userManagement...');
                    await this.fetchUsers();
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('roleFilter', () => { this.currentPage = 1; });
                    this.$watch('statusFilter', () => { this.currentPage = 1; });
                    console.log('userManagement initialized');
                },

                async fetchUsers() {
                    try {
                        const response = await fetch('/api/users', {
                            method: 'GET',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        this.users = await response.json();
                    } catch (error) {
                        console.error('Error fetching users:', error);
                        this.showError('Gagal memuat data user.');
                    }
                },

                get filteredUsers() {
                    return this.users.filter(user => {
                        const matchSearch = user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            (user.email || '').toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchRole = this.roleFilter === '' || user.role == this.roleFilter;
                        const matchStatus = this.statusFilter === '' || user.status_akun === this.statusFilter;
                        return matchSearch && matchRole && matchStatus;
                    });
                },

                formatRole(role) {
                    const roles = { 0: 'Staff', 1: 'Sekretaris', 2: 'Direktur', 3: 'Admin', 4: 'Manager', 5: 'Sekretaris ASP', 6: 'General Manager', 7: 'Manager Keuangan', 8: 'Direktur ASP' };
                    return roles[role] || 'Unknown';
                },

                formatDate(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        day: 'numeric', month: 'long', year: 'numeric'
                    });
                },
                
                confirmDelete(user) {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: `User "${user.name}" akan dihapus secara permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.deleteUser(user);
                        }
                    });
                },

                async deleteUser(user) {
                    try {
                        const response = await fetch(`/api/users/${user.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            this.fetchUsers();
                            this.showSuccess(result.message);
                        } else {
                            this.showError(result.message);
                        }
                    } catch (error) {
                        console.error('Error deleting user:', error);
                        this.showError('Gagal menghapus user');
                    }
                },

                async toggleUserStatus(user) {
                    try {
                        const response = await fetch(`/api/users/${user.id}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            this.fetchUsers();
                            this.showSuccess(result.message);
                        } else {
                            this.showError(result.message);
                        }
                    } catch (error) {
                        console.error('Error toggling user status:', error);
                        this.showError('Gagal mengubah status user');
                    }
                },

                showSuccess(message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },

                showError(message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                },

                // Pagination methods
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

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
                    <button @click="openCreateModal()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 flex items-center gap-2">
                        <i class="ri-add-line"></i>
                        Tambah User
                    </button>
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
                                    Jabatan</th>
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
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" :src="user.foto_url" :alt="user.name">
                                            </div>
                                            <div class="ml-4">
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
                                        x-text="user.jabatan?.nama_jabatan || 'Tidak ada jabatan'"></td>
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
                                            <button @click="editUser(user)"
                                                class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                                <i class="ri-edit-line"></i>
                                            </button>
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

        <!-- Modal -->
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                    x-text="formMode === 'create' ? 'Tambah User Baru' : 'Edit User'"></h3>
                                <form @submit.prevent="formMode === 'create' ? createUser() : updateUser()">
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Name -->
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
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
                                            <input type="email" x-model="formData.email"
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

                                        <!-- Jabatan -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                                            <div class="relative" x-data="{ open: false, search: '' }">
                                                <button type="button" @click="open = !open"
                                                    class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent flex justify-between items-center">
                                                    <span x-text="getSelectedJabatanName() || 'Pilih Jabatan'"></span>
                                                    <i class="ri-arrow-down-s-line" :class="{ 'rotate-180': open }"></i>
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" x-cloak
                                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-hidden">
                                                    <!-- Search Input -->
                                                    <div class="p-2 border-b border-gray-200">
                                                        <input type="text" x-model="search" placeholder="Cari jabatan..."
                                                            class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                                    </div>
                                                    
                                                    <!-- Jabatan List -->
                                                    <div class="max-h-48 overflow-y-auto">
                                                        <template x-for="jabatan in jabatanList.filter(j => j.nama_jabatan.toLowerCase().includes(search.toLowerCase()))" :key="jabatan.id">
                                                            <button type="button" @click="selectJabatan(jabatan); open = false"
                                                                class="w-full px-4 py-2 text-sm text-left hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                                                                :class="{ 'bg-green-50 text-green-700': formData.jabatan_id == jabatan.id }">
                                                                <span x-text="jabatan.nama_jabatan"></span>
                                                            </button>
                                                        </template>
                                                        
                                                        <!-- No results message -->
                                                        <div x-show="jabatanList.filter(j => j.nama_jabatan.toLowerCase().includes(search.toLowerCase())).length === 0" 
                                                            class="px-4 py-2 text-sm text-gray-500 text-center">
                                                            Tidak ada jabatan yang ditemukan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Manager (hanya untuk staff) -->
                                        <div x-show="formData.role == 0" class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Manager</label>
                                            <select x-model="formData.manager_id"
                                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                <option value="">Pilih Manager</option>
                                                <template x-for="manager in managers" :key="manager.id">
                                                    <option :value="manager.id" x-text="manager.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- General Manager (hanya untuk manager) -->
                                        <div x-show="formData.role == 4" class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                General Manager
                                                <span class="text-sm text-gray-500">(Opsional - kosongkan jika manager independen)</span>
                                            </label>
                                            <select x-model="formData.general_manager_id"
                                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                <option value="">Pilih General Manager (Opsional)</option>
                                                <template x-for="generalManager in generalManagers" :key="generalManager.id">
                                                    <option :value="generalManager.id" x-text="generalManager.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- General Manager untuk Manager Keuangan -->
                                        <div x-show="formData.role == 7" class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                General Manager
                                                <span class="text-sm text-gray-500">(Opsional - kosongkan jika manager keuangan independen)</span>
                                            </label>
                                            <select x-model="formData.general_manager_id"
                                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                <option value="">Pilih General Manager (Opsional)</option>
                                                <template x-for="generalManager in generalManagers" :key="generalManager.id">
                                                    <option :value="generalManager.id" x-text="generalManager.name"></option>
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

                                    <div class="mt-6 flex justify-end space-x-3">
                                        <button type="button" @click="closeModal()"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <span x-text="formMode === 'create' ? 'Tambah' : 'Update'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
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
                jabatanList: [],
                managers: @json($managers),
                generalManagers: @json($generalManagers),
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
                    manager_id: '',
                    general_manager_id: '',
                    status_akun: 'aktif'
                },
                currentPage: 1,
                itemsPerPage: 10,

                async init() {
                    console.log('Initializing userManagement...');
                    await Promise.all([
                        this.fetchUsers(),
                        this.fetchJabatan()
                    ]);
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

                async fetchJabatan() {
                    try {
                        const response = await fetch('/jabatan/data', {
                            method: 'GET',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        const result = await response.json();
                        if (result.status === 'success') {
                            this.jabatanList = result.data.filter(j => j.status === 'aktif');
                            if (this.jabatanList.length > 0 && !this.formData.jabatan_id) {
                                this.formData.jabatan_id = this.jabatanList[0].id;
                            }
                        }
                    } catch (error) {
                        console.error('Error fetching jabatan:', error);
                        this.showError('Gagal memuat data jabatan.');
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

                openCreateModal() {
                    console.log('Opening create modal...');
                    this.formMode = 'create';
                    this.resetForm();
                    this.showModal = true;
                    console.log('Modal should be visible:', this.showModal);
                },

                editUser(user) {
                    console.log('Opening edit modal for user:', user);
                    this.formMode = 'edit';
                    this.formData = {
                        id: user.id,
                        name: user.name,
                        username: user.username,
                        email: user.email,
                        password: '',
                        role: user.role,
                        jabatan_id: user.jabatan_id,
                        manager_id: user.manager_id || '',
                        general_manager_id: user.general_manager_id || '',
                        status_akun: user.status_akun
                    };
                    this.showModal = true;
                    console.log('Modal should be visible:', this.showModal);
                },

                resetForm() {
                    this.formData = {
                        id: null,
                        name: '',
                        username: '',
                        email: '',
                        password: '',
                        role: 0,
                        jabatan_id: this.jabatanList.length > 0 ? this.jabatanList[0].id : '',
                        manager_id: '',
                        general_manager_id: '',
                        status_akun: 'aktif'
                    };
                },

                async submitForm(url, method) {
                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();

                        if (response.ok && result.status === 'success') {
                            this.showModal = false;
                            this.fetchUsers();
                            this.showSuccess(result.message);
                        } else {
                            // Handle validation errors or other server errors
                            let errorMessage = result.message || 'Terjadi kesalahan.';
                            if(result.errors) {
                                errorMessage = Object.values(result.errors).flat().join(' ');
                            }
                            this.showError(errorMessage);
                        }
                    } catch (error) {
                        console.error('Error submitting form:', error);
                        this.showError('Gagal mengirim data ke server.');
                    }
                },

                createUser() {
                    this.submitForm('/api/users', 'POST');
                },

                updateUser() {
                    this.submitForm(`/api/users/${this.formData.id}`, 'PUT');
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
                },
                closeModal() {
                    this.showModal = false;
                    this.resetForm();
                },
                getSelectedJabatanName() {
                    const selectedJabatan = this.jabatanList.find(j => j.id === this.formData.jabatan_id);
                    return selectedJabatan ? selectedJabatan.nama_jabatan : null;
                },
                selectJabatan(jabatan) {
                    this.formData.jabatan_id = jabatan.id;
                }
            }));
        });
    </script>
@endpush

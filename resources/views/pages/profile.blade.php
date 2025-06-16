@extends('home')

@section('title', 'Profile - SISM Azra')

@section('content')
    <div x-data="profileApp" class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <!-- Cover Image -->
            <div class="h-48 bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&h=400&auto=format&fit=crop')">
            </div>

            <!-- Profile Info -->
            <div class="px-8 pb-8 relative">
                <!-- Avatar -->
                <div class="relative -mt-16 mb-4 w-32">
                    <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-md bg-white overflow-hidden">
                        <img src="{{ Auth::user()->foto_url }}" alt="Profile" class="w-full h-full object-cover"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF'">
                    </div>
                    <label
                        class="absolute bottom-2 right-2 bg-white rounded-lg w-8 h-8 shadow-md flex items-center justify-center text-gray-600 hover:text-green-600 transition-colors cursor-pointer">
                        <i class="ri-camera-line"></i>
                        <input type="file" @change="handlePhotoUpload" class="hidden" accept="image/*">
                    </label>
                </div>

                <!-- User Info -->
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $user->name }}</h1>
                        <p class="text-green-600 font-medium">{{ $user->jabatanName }}</p>
                    </div>
                    <button @click="openEditProfile"
                        class="bg-green-50 text-green-600 hover:bg-green-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="md:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Informasi Pribadi</h2>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama -->
                        <div class="flex items-start">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-sm font-medium text-gray-500">Nama</p>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $user->name }}</p>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="flex items-start">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-sm font-medium text-gray-500">Username</p>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $user->username }}</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-sm font-medium text-gray-500">Email</p>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $user->email }}</p>
                            </div>
                        </div>

                        <!-- Jabatan -->
                        <div class="flex items-start">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-sm font-medium text-gray-500">Jabatan</p>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $user->jabatanName }}</p>
                            </div>
                        </div>

                        <!-- Status Akun -->
                        <div class="flex items-start">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-sm font-medium text-gray-500">Status</p>
                            </div>
                            <div class="flex-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status_akun === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($user->status_akun) }}
                                </span>
                            </div>
                        </div>

                        <!-- Tanggal Bergabung -->
                        <div class="flex items-start">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-sm font-medium text-gray-500">Bergabung</p>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $user->created_at->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Account Security -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Keamanan Akun</h2>

                    <div class="space-y-4">
                        <button @click="openChangePassword"
                            class="w-full flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                    <i class="ri-lock-line text-gray-600"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-800">Ubah Password</p>
                                    <p class="text-xs text-gray-500">Amankan akun Anda</p>
                                </div>
                            </div>
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Last Login Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Info Login</h2>
                    <div class="text-sm text-gray-600">
                        <p>Terakhir login:</p>
                        <p class="font-medium text-gray-800 mt-1">{{ $user->updated_at->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div x-show="showEditProfile" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditProfile = false">
                </div>

                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Edit Profile</h3>
                    </div>

                    <form @submit.prevent="updateProfile" class="p-6">
                        <div class="space-y-4">
                            <!-- Nama -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Nama</label>
                                <input type="text" x-model="editForm.name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Username -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Username</label>
                                <input type="text" x-model="editForm.username"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Email</label>
                                <input type="email" x-model="editForm.email"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" @click="showEditProfile = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700">
                                    Simpan
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div x-show="showChangePassword" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showChangePassword = false">
                </div>

                <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Ubah Password</h3>
                    </div>

                    <form @submit.prevent="updatePassword" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Password Saat Ini</label>
                                <input type="password" x-model="passwordForm.current_password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Password Baru</label>
                                <input type="password" x-model="passwordForm.password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Konfirmasi Password
                                    Baru</label>
                                <input type="password" x-model="passwordForm.password_confirmation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="showChangePassword = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700">
                                Simpan
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
            Alpine.data('profileApp', () => ({
                showEditProfile: false,
                showChangePassword: false,
                user: @json($user),
                editForm: {
                    name: '{{ $user->name }}',
                    username: '{{ $user->username }}',
                    email: '{{ $user->email }}'
                },
                passwordForm: {
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                },

                openEditProfile() {
                    this.editForm = {
                        name: this.user.name,
                        username: this.user.username,
                        email: this.user.email
                    };
                    this.showEditProfile = true;
                },

                openChangePassword() {
                    this.showChangePassword = true;
                },

                async handlePhotoUpload(event) {
                    try {
                        const file = event.target.files[0];
                        if (!file) return;

                        const formData = new FormData();
                        formData.append('foto_profile', file);
                        formData.append('_method', 'PUT');
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                            .content);

                        Swal.fire({
                            title: 'Mengupload foto...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const response = await fetch('/profile/update', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            body: formData
                        });

                        if (!response.ok) throw new Error('Gagal mengupload foto');

                        const data = await response.json();

                        if (data.status === 'success') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Foto profile berhasil diperbarui'
                            });
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message
                        });
                    }
                },

                async updateProfile() {
                    try {
                        const formData = new FormData();
                        formData.append('_method', 'PUT');
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                            .content);
                        Object.keys(this.editForm).forEach(key => {
                            formData.append(key, this.editForm[key]);
                        });

                        const response = await fetch('/profile/update', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            body: formData
                        });

                        if (!response.ok) throw new Error('Gagal memperbarui profile');

                        const data = await response.json();

                        if (data.status === 'success') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Profile berhasil diperbarui'
                            });
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message
                        });
                    }
                },

                async updatePassword() {
                    try {
                        const formData = new FormData();
                        formData.append('_method', 'PUT');
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                            .content);
                        Object.keys(this.passwordForm).forEach(key => {
                            formData.append(key, this.passwordForm[key]);
                        });

                        const response = await fetch('/profile/password', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            body: formData
                        });

                        if (!response.ok) throw new Error('Gagal memperbarui password');

                        const data = await response.json();

                        if (data.status === 'success') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Password berhasil diperbarui'
                            });

                            this.showChangePassword = false;
                            this.passwordForm = {
                                current_password: '',
                                password: '',
                                password_confirmation: ''
                            };
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message
                        });
                    }
                }
            }));
        });
    </script>
@endpush

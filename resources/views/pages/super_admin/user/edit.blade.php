@extends('home')

@section('title', 'Edit User - SISM Azra')

@section('content')
    <div class="w-full" x-data="userForm">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi user</p>
            </div>
            <a href="{{ route('manageuser.index') }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="ri-arrow-left-line mr-1"></i> Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Info Section -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Informasi Pribadi</h3>
                        </div>

                        <!-- Name -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.name" required placeholder="Masukkan nama lengkap"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>

                        <!-- NIK -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK <span class="text-xs text-gray-500 font-normal">(Opsional)</span></label>
                            <input type="text" x-model="formData.nik" placeholder="Masukkan NIK"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>

                        <!-- Username -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.username" required placeholder="Masukkan username"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>

                        <!-- Email -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" x-model="formData.email" placeholder="contoh@email.com"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>

                        <!-- Password -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                                <span class="text-xs text-gray-500 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                            </label>
                            <input type="password" x-model="formData.password" placeholder="Masukkan password baru"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>

                        <!-- Role & Organization Section -->
                        <div class="md:col-span-2 mt-4">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Peran & Organisasi</h3>
                        </div>

                        <!-- Role -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                            <select x-model="formData.role" required
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
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

                        <!-- Organization Unit (Searchable) -->
                        <div class="col-span-2 md:col-span-1">
                            <x-searchable-dropdown 
                                name="organization_unit_id" 
                                label="Unit Organisasi" 
                                :options="$organizationUnits" 
                                :selected="$user->organization_unit_id"
                                valueField="id" 
                                labelField="name"
                                placeholder="Pilih Unit Organisasi"
                            />
                        </div>

                        <!-- Manager (Conditional) -->
                        <div class="col-span-2 md:col-span-1" x-show="formData.role == 0" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Manager</label>
                            <select x-model="formData.manager_id"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                <option value="">Pilih Manager</option>
                                <template x-for="manager in managers" :key="manager.id">
                                    <option :value="manager.id" x-text="manager.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- General Manager (Conditional) -->
                        <div class="col-span-2 md:col-span-1" x-show="formData.role == 4 || formData.role == 7" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                General Manager
                                <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                            </label>
                            <select x-model="formData.general_manager_id"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                <option value="">Pilih General Manager (Opsional)</option>
                                <template x-for="gm in generalManagers" :key="gm.id">
                                    <option :value="gm.id" x-text="gm.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Akun</label>
                            <div class="flex gap-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" x-model="formData.status_akun" value="aktif" class="form-radio text-green-600 w-4 h-4">
                                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" x-model="formData.status_akun" value="nonaktif" class="form-radio text-red-600 w-4 h-4">
                                    <span class="ml-2 text-sm text-gray-700">Non-Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('manageuser.index') }}"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                            Batal
                        </a>
                        <button type="submit" :disabled="isLoading"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                            <i class="ri-loader-4-line animate-spin" x-show="isLoading"></i>
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userForm', () => ({
                formData: {
                    id: {{ $user->id }},
                    name: @json($user->name),
                    nik: @json($user->nik),
                    username: @json($user->username),
                    email: @json($user->email),
                    password: '',
                    role: {{ $user->role }},
                    organization_unit_id: @json($user->organization_unit_id),
                    manager_id: @json($user->manager_id ?? ''),
                    general_manager_id: @json($user->general_manager_id ?? ''),
                    status_akun: @json($user->status_akun)
                },
                managers: @json($managers),
                generalManagers: @json($generalManagers),
                isLoading: false,

                init() {
                    this.$watch('formData.role', (value) => {
                        // Reset dependent fields if role changes
                        // Only reset if it's a user interaction, but here we just watch.
                        // Be careful not to reset on initial load if we were to set it dynamically.
                        // Since we init with correct values, this watch will only trigger on change.
                        
                        // However, if we change role, we might want to clear invalid selections.
                        if (value != 0) this.formData.manager_id = '';
                        if (value != 4 && value != 7) this.formData.general_manager_id = '';
                    });
                },

                async submitForm(e) {
                    this.isLoading = true;
                    
                    // Get the value from the searchable dropdown hidden input
                    const orgInput = document.querySelector('input[name="organization_unit_id"]');
                    if (orgInput) {
                        this.formData.organization_unit_id = orgInput.value;
                    }

                    // Prepare payload - remove empty password
                    const payload = { ...this.formData };
                    if (!payload.password) {
                        delete payload.password;
                    }

                    try {
                        // Send as POST with method spoofing to bypass Apache PUT restrictions
                        const response = await fetch("{{ route('api.users.update', $user->id) }}?_method=PUT", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();

                        if (response.ok && result.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: result.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = "{{ route('manageuser.index') }}";
                            });
                        } else {
                            let errorMessage = result.message || 'Terjadi kesalahan.';
                            if (result.errors) {
                                errorMessage = Object.values(result.errors).flat().join('\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghubungi server.'
                        });
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
@endpush

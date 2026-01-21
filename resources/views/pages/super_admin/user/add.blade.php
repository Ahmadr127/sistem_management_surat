@extends('home')

@section('title', 'Tambah User - SISM Azra')

@section('content')
    <div class="w-full" x-data="userForm">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah User Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Buat akun user baru untuk mengakses sistem</p>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" x-model="formData.password" required placeholder="Masukkan password"
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
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan User'"></span>
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
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    role: 0,
                    organization_unit_id: '',
                    manager_id: '',
                    general_manager_id: '',
                    status_akun: 'aktif'
                },
                managers: @json($managers),
                generalManagers: @json($generalManagers),
                isLoading: false,

                init() {
                    // Watch for changes in the searchable dropdown hidden input if needed
                    // But since we are using x-model on the component which binds to the hidden input, 
                    // we might need to listen to the change event or just rely on the form submission reading the input value.
                    // However, x-searchable-dropdown doesn't support x-model directly on the component tag in the way standard inputs do unless implemented.
                    // The component I saw uses `name` attribute for hidden input.
                    // So `formData.organization_unit_id` won't automatically update unless I bind it.
                    
                    // Let's fix the binding. The component has a hidden input with name="organization_unit_id".
                    // I should listen to changes on that input or manually update formData.
                    
                    // Actually, the component implementation I saw:
                    // <input type="hidden" name="name" :value="selected" ...>
                    // It uses internal `selected` state.
                    // To sync with my `formData`, I can use an event listener or just grab the value on submit.
                    // But to be cleaner, I will modify the component usage slightly or just grab form data on submit.
                    
                    // Wait, I can just use `new FormData(formElement)` on submit, which is easier.
                    // But I'm using x-model for other fields.
                    // Let's just update formData.organization_unit_id when the hidden input changes.
                    // Or better, let's use a custom event from the component if possible, but I can't change the component easily without verifying.
                    // The component has `x-data="{ selected: ... }"`.
                    // I will add a listener for the hidden input.
                    
                    this.$watch('formData.role', (value) => {
                        // Reset dependent fields if role changes
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

                    try {
                        const response = await fetch("{{ route('api.users.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
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

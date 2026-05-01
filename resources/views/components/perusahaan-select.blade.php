@props([
    'name'               => 'perusahaan',
    'selectedKode'       => null,
    'selectedNama'       => '',
    'jenisSuratSelector' => 'select[name="jenis_surat"]',
    'userRole'           => 0,
    'required'           => false,
])

{{--
    Component: x-perusahaan-select
    --------------------------------
    Props:
      name               — name attribute for the hidden input (default: 'perusahaan')
      selectedKode       — kode perusahaan yang sudah dipilih (untuk mode edit)
      selectedNama       — nama perusahaan yang sudah dipilih (untuk mode edit)
      jenisSuratSelector — CSS selector untuk <select name="jenis_surat">
      userRole           — role integer dari user yang login
      required           — apakah field wajib diisi
--}}

<div
    x-data="perusahaanSelect({
        name:               {{ json_encode($name) }},
        initKode:           {{ json_encode($selectedKode) }},
        initNama:           {{ json_encode($selectedNama) }},
        jenisSuratSelector: {{ json_encode($jenisSuratSelector) }},
        userRole:           {{ (int) $userRole }},
        csrfToken:          {{ json_encode(csrf_token()) }}
    })"
    x-init="init()"
    x-show="isVisible"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-1"
    class="space-y-2 perusahaan-field"
    @click.away="open = false"
    style="display: none;"
>
    <label class="text-sm font-semibold text-gray-800">
        Perusahaan Tujuan
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    {{-- Hidden input yang dikirim bersama form --}}
    <input
        type="hidden"
        name="{{ $name }}"
        :value="selectedKode"
        {{ $required ? 'required' : '' }}
    >

    <div class="relative">
        {{-- Trigger Button --}}
        <button
            type="button"
            @click="toggleOpen()"
            class="w-full px-4 py-3 text-left rounded-lg border transition-all duration-200 bg-white flex items-center justify-between"
            :class="{
                'border-green-500 ring ring-green-200': open,
                'border-gray-200 hover:border-gray-300': !open,
                'text-gray-800': selectedKode,
                'text-gray-400': !selectedKode
            }"
        >
            <span x-text="selectedKode ? selectedNama : 'Cari atau pilih perusahaan...'" class="block truncate text-sm"></span>
            <span class="flex items-center gap-2 ml-2 flex-shrink-0">
                <template x-if="selectedKode">
                    <button
                        type="button"
                        @click.stop="clearSelection()"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                        title="Hapus pilihan"
                    >
                        <i class="ri-close-line text-sm"></i>
                    </button>
                </template>
                <i class="ri-arrow-down-s-line text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </span>
        </button>

        {{-- Dropdown Panel --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-[9999] mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
            style="display: none;"
        >
            {{-- Search Box --}}
            <div class="p-3 border-b border-gray-100">
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        x-model.debounce.300ms="search"
                        @input="onSearch()"
                        @click.stop
                        x-ref="searchInput"
                        placeholder="Ketik nama atau kode perusahaan..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                    >
                </div>
            </div>

            {{-- Loading Spinner --}}
            <div x-show="loading" class="flex items-center justify-center py-6 gap-2 text-sm text-gray-500">
                <svg class="animate-spin h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>

            {{-- Options List --}}
            <div x-show="!loading" class="overflow-y-auto max-h-52">
                <template x-if="options.length > 0">
                    <div>
                        <template x-for="option in options" :key="option.kode">
                            <button
                                type="button"
                                @click="selectOption(option)"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-green-50 transition-colors flex items-center justify-between group"
                                :class="{ 'bg-green-50 text-green-700': option.kode === selectedKode }"
                            >
                                <span>
                                    <span class="font-medium block" x-text="option.nama_perusahaan"></span>
                                    <span class="text-xs text-gray-400 group-hover:text-green-500" x-text="option.kode"></span>
                                </span>
                                <template x-if="option.kode === selectedKode">
                                    <i class="ri-check-line text-green-600 flex-shrink-0"></i>
                                </template>
                            </button>
                        </template>
                    </div>
                </template>

                {{-- No Results + Add New Trigger --}}
                <template x-if="options.length === 0 && !showAddForm">
                    <div class="px-4 py-4 text-center">
                        <p class="text-sm text-gray-500 mb-3" x-text="search ? 'Tidak ada hasil untuk &quot;' + search + '&quot;' : 'Tidak ada perusahaan aktif.'"></p>
                        <button
                            type="button"
                            @click="showAddForm = true; $nextTick(() => $refs.newNameInput?.focus())"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors"
                        >
                            <i class="ri-add-circle-line"></i>
                            Tambah "<span x-text="search || 'Perusahaan Baru'"></span>"
                        </button>
                    </div>
                </template>

                {{-- "Add New" button at bottom of list --}}
                <template x-if="options.length > 0 && !showAddForm">
                    <div class="border-t border-gray-100 px-3 py-2">
                        <button
                            type="button"
                            @click="showAddForm = true; $nextTick(() => $refs.newNameInput?.focus())"
                            class="w-full text-left text-xs text-green-600 hover:text-green-700 flex items-center gap-1.5 py-1 transition-colors"
                        >
                            <i class="ri-add-circle-line"></i>
                            Tambah perusahaan baru...
                        </button>
                    </div>
                </template>
            </div>

            {{-- Quick Add Form (inline) --}}
            <div
                x-show="showAddForm"
                x-transition
                class="border-t border-gray-200 bg-gray-50 p-3"
                style="display: none;"
            >
                <p class="text-xs font-semibold text-gray-600 mb-2 flex items-center gap-1">
                    <i class="ri-building-line"></i> Tambah Perusahaan Baru
                </p>
                <div class="flex gap-2">
                    <input
                        type="text"
                        x-model="newCompanyName"
                        x-ref="newNameInput"
                        @keydown.enter.prevent="quickAdd()"
                        @keydown.escape="showAddForm = false"
                        placeholder="Nama perusahaan..."
                        class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-green-500 focus:ring focus:ring-green-200 transition-all"
                    >
                    <button
                        type="button"
                        @click="quickAdd()"
                        :disabled="quickAdding || !newCompanyName.trim()"
                        class="px-3 py-2 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-1"
                    >
                        <template x-if="quickAdding">
                            <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!quickAdding">
                            <span>Simpan</span>
                        </template>
                    </button>
                    <button
                        type="button"
                        @click="showAddForm = false; newCompanyName = ''"
                        class="px-3 py-2 text-xs text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        Batal
                    </button>
                </div>
                <p x-show="quickAddError" x-text="quickAddError" class="mt-1 text-xs text-red-500"></p>
            </div>
        </div>
    </div>

    @error($name)
        <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
            <i class="ri-error-warning-line"></i> {{ $message }}
        </p>
    @enderror
</div>

<script>
if (typeof perusahaanSelect === 'undefined') {
    function perusahaanSelect(props) {
        return {
            // Props
            name:               props.name,
            jenisSuratSelector: props.jenisSuratSelector,
            userRole:           props.userRole,
            csrfToken:          props.csrfToken,

            // State
            open:           false,
            loaded:         false,
            loading:        false,
            options:        [],
            search:         '',
            selectedKode:   props.initKode   || null,
            selectedNama:   props.initNama   || '',
            isVisible:      false,

            // Quick-add state
            showAddForm:    false,
            newCompanyName: '',
            quickAdding:    false,
            quickAddError:  '',

            /** Called by x-init */
            init() {
                const jenisSuratEl = document.querySelector(this.jenisSuratSelector);

                const updateVisibility = (value) => {
                    const isEksternal = value === 'eksternal';
                    this.isVisible = isEksternal;
                    if (!isEksternal) {
                        this.open = false;
                        this._setInternalDefault();
                    }
                };

                if (jenisSuratEl) {
                    updateVisibility(jenisSuratEl.value);
                    jenisSuratEl.addEventListener('change', (e) => updateVisibility(e.target.value));
                }
            },

            /** Set default perusahaan value for internal letters based on role */
            _setInternalDefault() {
                const aspRoles = [5, 8]; // Sekretaris ASP, Direktur ASP
                this.selectedKode = aspRoles.includes(this.userRole) ? 'ASP' : 'RSAZRA';
                this.selectedNama = '';  // clear display name; backend uses kode
            },

            /** Toggle dropdown open/close; lazy-load on first open */
            async toggleOpen() {
                this.open = !this.open;
                if (this.open) {
                    await this.$nextTick();
                    this.$refs.searchInput?.focus();
                    if (!this.loaded) await this.fetchOptions();
                }
            },

            /** Triggered by x-model.debounce on search input */
            async onSearch() {
                await this.fetchOptions();
            },

            /** Fetch options from API */
            async fetchOptions() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({ search: this.search, limit: 50 });
                    const res    = await fetch(`/api/perusahaan/select?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
                    });
                    const data = await res.json();
                    this.options = data.success ? data.data : [];
                    this.loaded  = true;
                } catch (e) {
                    console.error('PerusahaanSelect: fetch error', e);
                    this.options = [];
                } finally {
                    this.loading = false;
                }
            },

            /** Select a company option */
            selectOption(option) {
                this.selectedKode = option.kode;
                this.selectedNama = option.nama_perusahaan;
                this.open         = false;
                this.search       = '';
                this.showAddForm  = false;
            },

            /** Clear current selection */
            clearSelection() {
                this.selectedKode = null;
                this.selectedNama = '';
            },

            /** Quick-add a new company via API */
            async quickAdd() {
                this.quickAddError = '';
                const nama = this.newCompanyName.trim();
                if (!nama) return;

                this.quickAdding = true;
                try {
                    const res = await fetch('/api/perusahaan/quick-store', {
                        method:  'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     this.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept:             'application/json',
                        },
                        body: JSON.stringify({ nama_perusahaan: nama }),
                    });
                    const data = await res.json();

                    if (data.success) {
                        // Add to top of list and auto-select
                        this.options.unshift(data.data);
                        this.selectOption(data.data);
                        this.showAddForm    = false;
                        this.newCompanyName = '';

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success', title: 'Berhasil',
                                text: 'Perusahaan berhasil ditambahkan',
                                toast: true, position: 'top-end',
                                showConfirmButton: false, timer: 2000, timerProgressBar: true,
                            });
                        }
                    } else {
                        this.quickAddError = data.message || 'Gagal menyimpan perusahaan.';
                    }
                } catch (e) {
                    console.error('PerusahaanSelect: quickAdd error', e);
                    this.quickAddError = 'Terjadi kesalahan, silakan coba lagi.';
                } finally {
                    this.quickAdding = false;
                }
            },
        };
    }
}
</script>

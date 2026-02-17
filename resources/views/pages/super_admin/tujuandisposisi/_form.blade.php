<div x-data="{
    sourceType: '{{ $disposisiAssignment->source_type ?? (old('source_type', 'role')) }}',
    sourceRole: '{{ old('source_role', $disposisiAssignment->source_role ?? '') }}',
    sourceUser: '{{ old('source_user_id', $disposisiAssignment->source_user_id ?? '') }}',
    init() {
        this.$watch('sourceType', value => {
            if (value === 'role') this.sourceUser = '';
            else this.sourceRole = '';
        });
    }
}" class="mb-6 space-y-6">

    {{-- Source Type Selection --}}
    <div class="space-y-2">
        <label class="text-sm font-semibold text-gray-800">Tipe Pengirim</label>
        <div class="flex gap-6">
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="source_type" value="role" x-model="sourceType" class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                <span class="ml-2 text-sm text-gray-700">Role (Jabatan)</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="radio" name="source_type" value="user" x-model="sourceType" class="form-radio text-green-600 focus:ring-green-500 h-4 w-4">
                <span class="ml-2 text-sm text-gray-700">User Spesifik</span>
            </label>
        </div>
        @error('source_type')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Source Role Selection --}}
    <div x-show="sourceType === 'role'" class="space-y-2">
        <label for="source_role" class="text-sm font-semibold text-gray-800">Role Pengirim</label>
        {{-- Use simple searchable select or reusing your custom logic if desired, but native select with search libraries is usually better. 
             Since no libraries, I'll use a simple select for now as per user request for 'searchable' I will implement a simple alpine search. 
        --}}
        <div x-data="singleSelect({
            options: {{ json_encode(collect($roles)->map(fn($n, $id) => ['id' => $id, 'name' => $n])->values()) }},
            selected: '{{ old('source_role', $disposisiAssignment->source_role ?? '') }}',
            name: 'source_role',
            placeholder: 'Pilih Role Pengirim'
        })" class="relative">
            <input type="hidden" name="source_role" :value="selected">
            
            <div @click="open = !open" @click.outside="open = false"
                 class="w-full px-4 py-3 rounded-lg border border-gray-200 focus-within:border-green-500 focus-within:ring focus-within:ring-green-200 bg-white cursor-pointer flex justify-between items-center">
                <span x-text="getSelectedName() || placeholder" :class="{'text-gray-500': !selected, 'text-gray-900': selected}"></span>
                <i class="ri-arrow-down-s-line text-gray-400" :class="{'rotate-180': open}"></i>
            </div>

            <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto" style="display: none;">
                <div class="p-2 sticky top-0 bg-white border-b border-gray-100">
                    <input x-model="search" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm focus:outline-none focus:border-green-500" placeholder="Cari...">
                </div>
                <ul>
                    <template x-for="option in filteredOptions" :key="option.id">
                        <li @click="select(option.id)" 
                            class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-700"
                            :class="{'bg-green-50 text-green-800': selected == option.id}">
                            <span x-text="option.name"></span>
                        </li>
                    </template>
                    <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada hasil</li>
                </ul>
            </div>
        </div>
        @error('source_role')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Source User Selection --}}
    <div x-show="sourceType === 'user'" class="space-y-2" style="display: none;">
        <label for="source_user_id" class="text-sm font-semibold text-gray-800">User Pengirim</label>
        <div x-data="singleSelect({
            options: {{ json_encode($users ?? []) }},
            selected: '{{ old('source_user_id', $disposisiAssignment->source_user_id ?? '') }}',
            name: 'source_user_id',
            placeholder: 'Pilih User Pengirim'
        })" class="relative">
            <input type="hidden" name="source_user_id" :value="selected">
            
            <div @click="open = !open" @click.outside="open = false"
                 class="w-full px-4 py-3 rounded-lg border border-gray-200 focus-within:border-green-500 focus-within:ring focus-within:ring-green-200 bg-white cursor-pointer flex justify-between items-center">
                <span x-text="getSelectedName() || placeholder" :class="{'text-gray-500': !selected, 'text-gray-900': selected}"></span>
                <i class="ri-arrow-down-s-line text-gray-400" :class="{'rotate-180': open}"></i>
            </div>

            <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto" style="display: none;">
                <div class="p-2 sticky top-0 bg-white border-b border-gray-100">
                    <input x-model="search" type="text" class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm focus:outline-none focus:border-green-500" placeholder="Cari user...">
                </div>
                <ul>
                    <template x-for="option in filteredOptions" :key="option.id">
                        <li @click="select(option.id)" 
                            class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm text-gray-700"
                            :class="{'bg-green-50 text-green-800': selected == option.id}">
                            <span x-text="option.name"></span>
                        </li>
                    </template>
                    <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada hasil</li>
                </ul>
            </div>
        </div>
        @error('source_user_id')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('singleSelect', (config) => ({
                options: config.options,
                selected: config.selected,
                name: config.name,
                placeholder: config.placeholder,
                open: false,
                search: '',
                
                get filteredOptions() {
                    if (!this.search) return this.options;
                    return this.options.filter(opt => opt.name.toLowerCase().includes(this.search.toLowerCase()));
                },

                getSelectedName() {
                    if (!this.selected) return null;
                    // Loose comparison for string/int match
                    const opt = this.options.find(o => o.id == this.selected);
                    return opt ? opt.name : null;
                },

                select(id) {
                    this.selected = id;
                    this.open = false;
                    this.search = '';
                }
            }));
        });
    </script>

    <div class="space-y-2" x-data="roleSelect()">
        <label class="text-sm font-semibold text-gray-800">Role Penerima</label>
        
        {{-- Hidden inputs for form submission --}}
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="target_roles[]" :value="id">
        </template>

        <div class="relative" @click.outside="open = false">
            {{-- Trigger / Selected Tags Area --}}
            <div @click="open = !open" 
                 class="w-full min-h-[50px] px-3 py-2 rounded-lg border border-gray-200 focus-within:border-green-500 focus-within:ring focus-within:ring-green-200 transition-all duration-200 bg-white cursor-text flex flex-wrap gap-2 items-center">
                
                <template x-for="id in selected" :key="id">
                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                        <span x-text="getRoleName(id)"></span>
                        <button type="button" @click.stop="remove(id)" class="ml-1.5 text-green-600 hover:text-green-800 focus:outline-none">
                            <i class="ri-close-line"></i>
                        </button>
                    </span>
                </template>

                <input type="text" 
                       x-model="search" 
                       placeholder="Cari role..." 
                       class="flex-1 min-w-[120px] bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-400 focus:ring-0 p-0"
                       @focus="open = true">
                
                <div class="text-gray-400">
                    <i class="ri-arrow-down-s-line" :class="{'rotate-180': open}"></i>
                </div>
            </div>

            {{-- Dropdown Menu --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"
                 style="display: none;">
                
                <ul class="py-1">
                    <template x-for="role in filteredRoles" :key="role.id">
                        <li @click="toggle(role.id)" 
                            class="px-4 py-2 hover:bg-gray-50 cursor-pointer flex justify-between items-center text-sm text-gray-700"
                            :class="{'bg-green-50 text-green-800': selected.includes(role.id)}">
                            <span x-text="role.name"></span>
                            <i x-show="selected.includes(role.id)" class="ri-check-line text-green-600"></i>
                        </li>
                    </template>
                    <li x-show="filteredRoles.length === 0" class="px-4 py-3 text-center text-sm text-gray-500">
                        Tidak ada role ditemukan
                    </li>
                </ul>
            </div>
        </div>
        
        @error('target_roles')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('roleSelect', () => ({
                open: false,
                search: '',
                selected: @json(old('target_roles', $selectedTargets ?? [])).map(Number),
                roles: @json(collect($roles)->map(fn($name, $id) => ['id' => (int)$id, 'name' => $name])->values()),
                
                get filteredRoles() {
                    const term = this.search.toLowerCase();
                    return this.roles.filter(r => r.name.toLowerCase().includes(term));
                },
                
                getRoleName(id) {
                    const role = this.roles.find(r => r.id === id);
                    return role ? role.name : 'Unknown Role';
                },

                toggle(id) {
                    if (this.selected.includes(id)) {
                        this.remove(id);
                    } else {
                        this.selected.push(id);
                        this.search = ''; 
                        // this.open = false; // Optional: close on select? User wants multi-select so keep open.
                        this.$nextTick(() => {
                           // Keep focus on input if needed?
                        });
                    }
                },

                remove(id) {
                    this.selected = this.selected.filter(i => i !== id);
                }
            }));
        });
    </script>
</div>

<div class="mt-6 flex justify-end">
    <button type="submit" class="px-6 py-2.5 bg-green-600 text-white font-medium text-sm leading-tight uppercase rounded shadow-md hover:bg-green-700 hover:shadow-lg focus:bg-green-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-green-800 active:shadow-lg transition duration-150 ease-in-out">
        Simpan
    </button>
</div>

@props(['users', 'selectedUsers' => []])

<div class="space-y-2 mt-4" x-data="tujuanDisposisiHandler(@json($selectedUsers))" @click.outside="closeDropdown">
    <label class="text-sm font-semibold text-gray-800 label-tujuan-disposisi">Tujuan Disposisi</label>
    
    <div class="relative">
        <!-- Main Input Container (Tags + Search) -->
        <div class="min-h-[46px] w-full px-2 py-1.5 rounded-lg border border-gray-200 focus-within:border-green-500 focus-within:ring focus-within:ring-green-200 transition-all duration-200 bg-white flex flex-wrap items-center gap-1 cursor-text"
             @click="$refs.searchInput.focus()">
            
            <!-- Selected Tags -->
            <template x-for="user in selectedUsersList" :key="user.id">
                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-800 text-white text-xs font-medium animate-fadeIn">
                    <span x-text="user.name"></span>
                    <button type="button" @click.stop="removeUser(user.id)" class="ml-1.5 text-gray-400 hover:text-white focus:outline-none">
                        <i class="ri-close-line"></i>
                    </button>
                </span>
            </template>

            <!-- Search Input -->
            <input type="text" 
                   x-ref="searchInput"
                   x-model="search"
                   @focus="openDropdown"
                   @keydown.enter.prevent="selectFirstVisible"
                   @keydown.backspace="handleBackspace"
                   class="flex-1 min-w-[120px] bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-400 focus:ring-0 p-1"
                   placeholder="Cari tujuan disposisi..."
                   autocomplete="off">
            
            <!-- Icons -->
            <div class="flex items-center text-gray-400 pr-2">
                <i class="ri-search-line" x-show="!search && selectedUsersList.length === 0"></i>
                <button type="button" x-show="selectedUsersList.length > 0" @click.stop="clearAll" class="hover:text-red-500 transition-colors" title="Hapus Semua">
                    <i class="ri-close-circle-line"></i>
                </button>
            </div>
        </div>

        <!-- Dropdown List -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="absolute z-50 mt-1 w-full bg-white rounded-lg border border-gray-200 shadow-lg max-h-60 overflow-hidden flex flex-col"
             style="display: none;">
            
            <!-- Actions Bar -->
            <div class="px-3 py-2 bg-gray-50 border-b border-gray-100 flex justify-between items-center text-xs">
                <span class="text-gray-500"><span x-text="filteredCount"></span> ditemukan</span>
                <div class="space-x-2">
                    <button type="button" @click="selectAllFiltered" class="text-blue-600 hover:text-blue-800 font-medium">
                        Pilih Semua
                    </button>
                    <button type="button" @click="clearAll" class="text-red-600 hover:text-red-800 font-medium">
                        Hapus Semua
                    </button>
                </div>
            </div>

            <!-- User List -->
            <div class="overflow-y-auto p-1 custom-scrollbar">
                <div class="space-y-0.5" id="user-list-container">
                    @php
                        $allowedRolesAsp = [1, 2, 6, 7, 8];
                        $authUser = auth()->user();
                    @endphp
                    
                    @foreach ($users as $user)
                        @php
                            $shouldShow = ($authUser->role === 5 && in_array($user->role, $allowedRolesAsp)) || 
                                          ($authUser->role !== 5 && in_array($user->role, [1,2,4,5,7,8]));
                            $jabatanName = $user->jabatan_name ?? 'Tidak ada jabatan';
                        @endphp
                        
                        @if ($shouldShow)
                            <div class="user-item flex items-center px-3 py-2 rounded-md cursor-pointer hover:bg-gray-50 transition-colors group"
                                 :class="{'bg-green-50': selectedIds.includes({{ $user->id }})}"
                                 @click="toggleUser('{{ $user->id }}', '{{ $user->name }}')"
                                 data-name="{{ strtolower($user->name) }}"
                                 data-jabatan="{{ strtolower($jabatanName) }}">
                                
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           id="user-{{ $user->id }}" 
                                           value="{{ $user->id }}"
                                           name="tujuan_disposisi[]"
                                           class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                           :checked="selectedIds.includes({{ $user->id }})"
                                           @click.stop="toggleUser('{{ $user->id }}', '{{ $user->name }}')">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="user-{{ $user->id }}" class="font-medium text-gray-700 group-hover:text-gray-900 cursor-pointer">
                                        {{ $user->name }}
                                    </label>
                                    <span class="text-gray-500 text-xs block">{{ $jabatanName }}</span>
                                </div>
                                <div class="ml-auto" x-show="selectedIds.includes({{ $user->id }})">
                                    <i class="ri-check-line text-green-600"></i>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- GM Logic --}}
                    @php
                        $gmUser = null;
                        if ($authUser->role === 4 && $authUser->general_manager_id) {
                            $gmUser = $users->first(function($u) use ($authUser) {
                                return $u->role === 6 && $u->id == $authUser->general_manager_id;
                            });
                        }
                    @endphp
                    @if ($gmUser)
                        @php $gmJabatanName = $gmUser->jabatan_name ?? 'General Manager'; @endphp
                        <div class="user-item flex items-center px-3 py-2 rounded-md cursor-pointer hover:bg-gray-50 transition-colors group"
                             :class="{'bg-green-50': selectedIds.includes({{ $gmUser->id }})}"
                             @click="toggleUser('{{ $gmUser->id }}', '{{ $gmUser->name }}')"
                             data-name="{{ strtolower($gmUser->name) }}"
                             data-jabatan="{{ strtolower($gmJabatanName) }}">
                            
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       id="user-{{ $gmUser->id }}" 
                                       value="{{ $gmUser->id }}"
                                       name="tujuan_disposisi[]"
                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                       :checked="selectedIds.includes({{ $gmUser->id }})"
                                       @click.stop="toggleUser('{{ $gmUser->id }}', '{{ $gmUser->name }}')">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="user-{{ $gmUser->id }}" class="font-medium text-gray-700 group-hover:text-gray-900 cursor-pointer">
                                    {{ $gmUser->name }}
                                </label>
                                <span class="text-gray-500 text-xs block">{{ $gmJabatanName }}</span>
                            </div>
                            <div class="ml-auto" x-show="selectedIds.includes({{ $gmUser->id }})">
                                <i class="ri-check-line text-green-600"></i>
                            </div>
                        </div>
                    @endif

                    <div x-show="filteredCount === 0" class="px-3 py-4 text-center text-gray-500 text-sm">
                        <i class="ri-user-unfollow-line text-2xl mb-1 block"></i>
                        Tidak ada user ditemukan
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tujuanDisposisiHandler', (initialSelected = []) => ({
                search: '',
                selectedIds: initialSelected.map(id => parseInt(id)),
                selectedUsersList: [], 
                isOpen: false,
                filteredCount: 0,

                init() {
                    this.$nextTick(() => {
                        // Initialize selected users list
                        this.selectedIds.forEach(id => {
                            const checkbox = document.querySelector(`input[value="${id}"]`);
                            if (checkbox) {
                                const container = checkbox.closest('.user-item');
                                const label = container.querySelector('label');
                                if (label) {
                                    this.selectedUsersList.push({ 
                                        id: id, 
                                        name: label.textContent.trim() 
                                    });
                                }
                            }
                        });
                        this.filterUsers();
                    });

                    this.$watch('search', () => {
                        this.isOpen = true;
                        this.filterUsers();
                    });
                },

                openDropdown() {
                    this.isOpen = true;
                    this.filterUsers();
                },

                closeDropdown() {
                    this.isOpen = false;
                },

                filterUsers() {
                    const term = this.search.toLowerCase();
                    const items = document.querySelectorAll('.user-item');
                    let count = 0;
                    
                    items.forEach(item => {
                        const name = item.dataset.name;
                        const jabatan = item.dataset.jabatan;
                        if (name.includes(term) || jabatan.includes(term)) {
                            item.style.display = 'flex';
                            count++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    this.filteredCount = count;
                },

                toggleUser(id, name) {
                    id = parseInt(id);
                    const index = this.selectedIds.indexOf(id);
                    
                    if (index === -1) {
                        this.selectedIds.push(id);
                        this.selectedUsersList.push({ id: id, name: name });
                        this.search = ''; // Clear search on select
                        this.$refs.searchInput.focus();
                    } else {
                        this.removeUser(id);
                    }
                },

                removeUser(id) {
                    id = parseInt(id);
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                    this.selectedUsersList = this.selectedUsersList.filter(u => u.id !== id);
                },

                handleBackspace() {
                    if (this.search === '' && this.selectedUsersList.length > 0) {
                        this.removeUser(this.selectedUsersList[this.selectedUsersList.length - 1].id);
                    }
                },

                selectAllFiltered() {
                    const visibleItems = Array.from(document.querySelectorAll('.user-item'))
                        .filter(el => el.style.display !== 'none');
                    
                    visibleItems.forEach(item => {
                        const checkbox = item.querySelector('input[type="checkbox"]');
                        const id = parseInt(checkbox.value);
                        if (!this.selectedIds.includes(id)) {
                            const label = item.querySelector('label');
                            this.toggleUser(id, label.textContent.trim());
                        }
                    });
                },

                clearAll() {
                    this.selectedIds = [];
                    this.selectedUsersList = [];
                },

                selectFirstVisible() {
                    if (!this.isOpen) return;
                    const visibleItem = Array.from(document.querySelectorAll('.user-item'))
                        .find(el => el.style.display !== 'none');
                    
                    if (visibleItem) {
                        const checkbox = visibleItem.querySelector('input[type="checkbox"]');
                        const id = parseInt(checkbox.value);
                        const label = visibleItem.querySelector('label');
                        
                        if (!this.selectedIds.includes(id)) {
                            this.toggleUser(id, label.textContent.trim());
                        }
                    }
                }
            }));
        });
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out;
        }
    </style>
</div>

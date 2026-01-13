<div x-data="{
    open: false,
    search: '',
    selected: {{ $selected ? "'" . $selected . "'" : 'null' }},
    selectedLabel: '',
    options: {{ json_encode($options) }},
    valueField: '{{ $valueField }}',
    labelField: '{{ $labelField }}',
    groupField: {{ $groupField ? "'" . $groupField . "'" : 'null' }},
    
    get filteredOptions() {
        if (!this.search) return this.options;
        return this.options.filter(option => {
            const label = option[this.labelField].toLowerCase();
            return label.includes(this.search.toLowerCase());
        });
    },
    
    get groupedOptions() {
        if (!this.groupField) return { '': this.filteredOptions };
        
        return this.filteredOptions.reduce((groups, option) => {
            const group = option[this.groupField] || 'Lainnya';
            if (!groups[group]) groups[group] = [];
            groups[group].push(option);
            return groups;
        }, {});
    },
    
    selectOption(option) {
        this.selected = option ? option[this.valueField] : null;
        this.selectedLabel = option ? option[this.labelField] : '';
        this.open = false;
        this.search = '';
    },
    
    init() {
        if (this.selected) {
            const option = this.options.find(opt => opt[this.valueField] == this.selected);
            if (option) {
                this.selectedLabel = option[this.labelField];
            }
        }
    }
}" @click.away="open = false" class="relative">
    
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <!-- Hidden Input -->
    <input type="hidden" name="{{ $name }}" :value="selected" {{ $required ? 'required' : '' }}>
    
    <!-- Dropdown Button -->
    <button 
        type="button"
        @click="open = !open"
        class="w-full px-3 py-2 text-left border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm flex items-center justify-between"
        :class="{ 'ring-2 ring-green-500': open }">
        <span x-text="selectedLabel || '{{ $placeholder }}'" class="block truncate" :class="{ 'text-gray-400': !selectedLabel }"></span>
        <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-hidden"
        style="display: none;">
        
        <!-- Search Input -->
        <div class="p-2 border-b border-gray-200">
            <input 
                type="text"
                x-model="search"
                @click.stop
                placeholder="Cari..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
        </div>
        
        <!-- Options List -->
        <div class="overflow-y-auto max-h-48">
            <!-- Empty Option -->
            @if($emptyOption)
                <div 
                    @click="selectOption(null)"
                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm"
                    :class="{ 'bg-green-50 text-green-700': selected === null }">
                    {{ $emptyOption }}
                </div>
            @endif
            
            <!-- Grouped or Flat Options -->
            <template x-if="!groupField">
                <template x-for="option in filteredOptions" :key="option[valueField]">
                    <div 
                        @click="selectOption(option)"
                        class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm"
                        :class="{ 'bg-green-50 text-green-700': selected == option[valueField] }"
                        x-text="option[labelField]">
                    </div>
                </template>
            </template>
            
            <template x-if="groupField">
                <template x-for="(groupOptions, groupName) in groupedOptions" :key="groupName">
                    <div>
                        <div class="px-3 py-1 bg-gray-100 text-xs font-semibold text-gray-600 uppercase" x-text="groupName"></div>
                        <template x-for="option in groupOptions" :key="option[valueField]">
                            <div 
                                @click="selectOption(option)"
                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm pl-6"
                                :class="{ 'bg-green-50 text-green-700': selected == option[valueField] }"
                                x-text="option[labelField]">
                            </div>
                        </template>
                    </div>
                </template>
            </template>
            
            <!-- No Results -->
            <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">
                Tidak ada hasil
            </div>
        </div>
    </div>
    
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
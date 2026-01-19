@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => null,
    'valueField' => 'id',
    'labelField' => 'name',
    'groupField' => null,
    'placeholder' => 'Pilih...',
    'emptyOption' => null,
    'required' => false,
    'multiple' => false,
])

<div x-data="{
    open: false,
    search: '',
    selected: {{ $multiple ? json_encode($selected ? (array)$selected : []) : ($selected ? "'" . $selected . "'" : 'null') }},
    selectedLabel: '',
    options: {{ json_encode($options) }},
    valueField: '{{ $valueField }}',
    labelField: '{{ $labelField }}',
    groupField: {{ $groupField ? "'" . $groupField . "'" : 'null' }},
    multiple: {{ $multiple ? 'true' : 'false' }},
    
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
        if (this.multiple) {
            const value = option[this.valueField];
            const index = this.selected.indexOf(value);
            if (index > -1) {
                this.selected.splice(index, 1);
            } else {
                this.selected.push(value);
            }
        } else {
            this.selected = option ? option[this.valueField] : null;
            this.selectedLabel = option ? option[this.labelField] : '';
            this.open = false;
            this.search = '';
        }
    },
    
    isSelected(option) {
        if (this.multiple) {
            return this.selected.includes(option[this.valueField]);
        }
        return this.selected == option[this.valueField];
    },
    
    getSelectedLabels() {
        if (!this.multiple) return '';
        return this.options
            .filter(opt => this.selected.includes(opt[this.valueField]))
            .map(opt => opt[this.labelField]);
    },
    
    removeSelected(value) {
        const index = this.selected.indexOf(value);
        if (index > -1) {
            this.selected.splice(index, 1);
        }
    },
    
    init() {
        if (!this.multiple && this.selected) {
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
    
    <!-- Hidden Input(s) -->
    @if($multiple)
        <template x-for="(value, index) in selected" :key="index">
            <input type="hidden" :name="'{{ $name }}[]'" :value="value">
        </template>
    @else
        <input type="hidden" name="{{ $name }}" :value="selected" {{ $required ? 'required' : '' }}>
    @endif
    
    <!-- Selected Badges (for multiple) -->
    <template x-if="multiple && selected.length > 0">
        <div class="flex flex-wrap gap-2 mb-2">
            <template x-for="value in selected" :key="value">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                    <span x-text="options.find(opt => opt[valueField] == value)?.[labelField]" class="mr-2"></span>
                    <button type="button" @click="removeSelected(value)" class="hover:text-green-900 focus:outline-none">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            </template>
        </div>
    </template>
    
    <!-- Dropdown Button -->
    <button 
        type="button"
        @click="open = !open"
        class="w-full px-3 py-2 text-left border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm flex items-center justify-between"
        :class="{ 'ring-2 ring-green-500': open }">
        <span x-show="!multiple" x-text="selectedLabel || '{{ $placeholder }}'" class="block truncate" :class="{ 'text-gray-400': !selectedLabel }"></span>
        <span x-show="multiple" class="block truncate" x-text="(!selected || selected.length === 0) ? '{{ $placeholder }}' : selected.length + ' dipilih'" :class="{ 'text-gray-400': !selected || selected.length === 0 }"></span>
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
            @if($emptyOption && !$multiple)
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
                        class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm flex items-center"
                        :class="{ 'bg-green-50 text-green-700': isSelected(option) }">
                        <template x-if="multiple">
                            <input type="checkbox" :checked="isSelected(option)" class="mr-2" @click.stop>
                        </template>
                        <span x-text="option[labelField]"></span>
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
                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm pl-6 flex items-center"
                                :class="{ 'bg-green-50 text-green-700': isSelected(option) }">
                                <template x-if="multiple">
                                    <input type="checkbox" :checked="isSelected(option)" class="mr-2" @click.stop>
                                </template>
                                <span x-text="option[labelField]"></span>
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
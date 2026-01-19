@props([
    'name' => 'search',
    'label' => 'Cari Surat',
    'placeholder' => 'Cari nomor surat, perihal...',
    'value' => '',
])

<div x-data="{
    open: false,
    search: '{{ $value }}',
    results: [],
    loading: false,
    selectedId: null,
    
    async searchSurat() {
        if (this.search.length < 2) {
            this.results = [];
            this.open = false;
            return;
        }
        
        this.loading = true;
        this.open = true;
        
        try {
            const response = await fetch(`/api/surat-keluar?search=${encodeURIComponent(this.search)}`);
            const data = await response.json();
            
            this.results = data.slice(0, 10); // Limit to 10 results
            this.loading = false;
        } catch (error) {
            console.error('Error searching:', error);
            this.results = [];
            this.loading = false;
        }
    },
    
    selectSurat(surat) {
        this.selectedId = surat.id;
        this.search = surat.nomor_surat + ' - ' + surat.perihal;
        this.open = false;
        
        // Trigger detail modal
        if (window.showDetailSurat) {
            window.showDetailSurat(surat.id);
        }
    },
    
    clearSearch() {
        this.search = '';
        this.selectedId = null;
        this.results = [];
        this.open = false;
    }
}" @click.away="open = false" class="relative">
    
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative">
        <input 
            type="text" 
            x-model="search"
            @input.debounce.500ms="searchSurat()"
            @focus="if(search.length >= 2) open = true"
            placeholder="{{ $placeholder }}"
            class="w-full px-3 py-2 pr-20 rounded-lg border border-gray-200 focus:border-green-500 focus:ring focus:ring-green-200 text-sm"
            autocomplete="off">
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
            <!-- Loading Spinner -->
            <div x-show="loading" class="animate-spin h-4 w-4 border-2 border-green-500 border-t-transparent rounded-full"></div>
            
            <!-- Clear Button -->
            <button 
                x-show="search.length > 0 && !loading"
                @click="clearSearch()"
                type="button"
                class="p-1 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>
            
            <!-- Search Icon -->
            <div x-show="!loading && search.length === 0" class="text-gray-400">
                <i class="ri-search-line text-lg"></i>
            </div>
        </div>
    </div>
    
    <!-- Dropdown Results -->
    <div 
        x-show="open && results.length > 0"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-96 overflow-y-auto"
        style="display: none;">
        
        <div class="py-1">
            <template x-for="surat in results" :key="surat.id">
                <button
                    type="button"
                    @click="selectSurat(surat)"
                    class="w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="surat.nomor_surat"></p>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="surat.perihal"></p>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                      :class="surat.jenis_surat === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                      x-text="surat.jenis_surat === 'internal' ? 'Internal' : 'Eksternal'">
                                </span>
                                <span class="text-xs text-gray-500" x-text="new Date(surat.tanggal_surat).toLocaleDateString('id-ID')"></span>
                            </div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            <i class="ri-arrow-right-line text-gray-400"></i>
                        </div>
                    </div>
                </button>
            </template>
        </div>
    </div>
    
    <!-- No Results Message -->
    <div 
        x-show="open && !loading && search.length >= 2 && results.length === 0"
        x-transition
        class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg p-4 text-center"
        style="display: none;">
        <i class="ri-inbox-line text-gray-300 text-3xl mb-2"></i>
        <p class="text-sm text-gray-500">Tidak ada surat ditemukan</p>
    </div>
</div>

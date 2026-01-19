@props([
    'data', // Paginated data object
    'columns' => [], // Array of column definitions: ['key' => 'field_name', 'label' => 'Display Name', 'sortable' => true]
    'actions' => null, // Slot for action buttons
    'emptyMessage' => 'Tidak ada data tersedia',
    'searchable' => true,
    'searchPlaceholder' => 'Cari data...',
])

<div x-data="{
    search: '',
    sortColumn: '',
    sortDirection: 'asc',
    
    get filteredData() {
        if (!this.search) return {{ json_encode($data->items()) }};
        
        return {{ json_encode($data->items()) }}.filter(item => {
            return Object.values(item).some(value => {
                if (value === null || value === undefined) return false;
                return String(value).toLowerCase().includes(this.search.toLowerCase());
            });
        });
    },
    
    get sortedData() {
        if (!this.sortColumn) return this.filteredData;
        
        return [...this.filteredData].sort((a, b) => {
            let aVal = a[this.sortColumn];
            let bVal = b[this.sortColumn];
            
            if (aVal === null || aVal === undefined) return 1;
            if (bVal === null || bVal === undefined) return -1;
            
            if (this.sortDirection === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
    },
    
    toggleSort(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
    }
}" class="w-full">
    
    @if($searchable)
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if($column['sortable'] ?? false)
                                <button 
                                    type="button"
                                    @click="toggleSort('{{ $column['key'] }}')"
                                    class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none">
                                    <span>{{ $column['label'] }}</span>
                                    <i class="ri-arrow-up-down-line text-xs" 
                                       :class="{
                                           'text-green-600': sortColumn === '{{ $column['key'] }}',
                                           'rotate-180': sortColumn === '{{ $column['key'] }}' && sortDirection === 'desc'
                                       }"></i>
                                </button>
                            @else
                                {{ $column['label'] }}
                            @endif
                        </th>
                    @endforeach
                    
                    @if($actions)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="sortedData.length === 0">
                    <tr>
                        <td :colspan="{{ count($columns) + ($actions ? 1 : 0) }}" class="px-6 py-8 text-center text-gray-500">
                            <i class="ri-inbox-line text-4xl mb-2 block text-gray-300"></i>
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                </template>
                
                <template x-for="(item, index) in sortedData" :key="index">
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($column['render']))
                                    {!! $column['render'] !!}
                                @else
                                    <span x-text="item['{{ $column['key'] }}']"></span>
                                @endif
                            </td>
                        @endforeach
                        
                        @if($actions)
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                {{ $actions }}
                            </td>
                        @endif
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($data->hasPages())
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan <span class="font-medium">{{ $data->firstItem() }}</span> 
                sampai <span class="font-medium">{{ $data->lastItem() }}</span> 
                dari <span class="font-medium">{{ $data->total() }}</span> data
            </div>
            
            <div class="flex space-x-2">
                {{-- Previous Page Link --}}
                @if ($data->onFirstPage())
                    <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                        <i class="ri-arrow-left-s-line"></i>
                    </span>
                @else
                    <a href="{{ $data->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($data->getUrlRange(1, $data->lastPage()) as $page => $url)
                    @if ($page == $data->currentPage())
                        <span class="px-3 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-lg">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($data->hasMorePages())
                    <a href="{{ $data->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                @else
                    <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                        <i class="ri-arrow-right-s-line"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

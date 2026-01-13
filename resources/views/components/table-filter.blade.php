@props(['name', 'label' => null, 'searchPlaceholder' => 'Cari...'])

<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
    <form method="GET" class="flex flex-wrap gap-4 items-end" x-data="{ search: '{{ request('search') }}' }">
        <div class="flex-1 min-w-[200px]">
            @if($label)
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
            @endif
            <input 
                type="text" 
                name="search" 
                x-model="search"
                value="{{ request('search') }}" 
                placeholder="{{ $searchPlaceholder }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
        </div>
        
        {{ $slot }}
        
        <div class="flex gap-2">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <a href="{{ url()->current() }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm">
                <i class="fas fa-redo mr-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<script>
function tableFilter(initialData = {}) {
    return {
        search: initialData.search || '',
        dateFrom: initialData.dateFrom || '',
        dateTo: initialData.dateTo || '',
        type_id: initialData.type_id || '',
        is_active: initialData.is_active || '',
    }
}
</script>
@extends('layouts.app')

@section('title', 'Kelola Roles')

@section('content')
<div class="w-full mx-auto" x-data="{
    ...tableFilter({
        search: '{{ request('search') }}'
    })
}">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Kelola Roles</h2>
                <a href="{{ route('roles.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>Tambah Role
                </a>
            </div>
        </div>

        <!-- Table Filter Component -->
        <x-table-filter 
            search-placeholder="Cari nama role atau display name..."
        />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Display Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Users
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $role->display_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions->take(3) as $permission)
                                    <span class="inline-block bg-blue-200 text-blue-800 text-xs px-2 py-1 rounded">
                                        {{ $permission->display_name }}
                                    </span>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                    <span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded">
                                        +{{ $role->permissions->count() - 3 }} lainnya
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                {{ $role->users->count() }} users
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('roles.show', $role) }}" class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus role ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data role.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

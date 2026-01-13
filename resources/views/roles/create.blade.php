@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Tambah Role Baru</h2>
            <a href="{{ route('roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Role <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                               placeholder="contoh: manager, staff">
                        <p class="mt-1 text-sm text-gray-500">Gunakan format snake_case (contoh: manager, super_admin)</p>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name <span class="text-red-500">*</span></label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                               placeholder="contoh: Manager, Super Admin">
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                              placeholder="Deskripsi singkat tentang role ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                    <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($permissions as $permission)
                                <label class="flex items-start space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $permission->name }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @error('permissions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded">
                        Batal
                    </a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i>Simpan Role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

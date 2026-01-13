@extends('layouts.app')

@section('title', 'Detail Role')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $role->display_name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $role->name }}</p>
                    @if($role->description)
                        <p class="text-gray-600 mt-2">{{ $role->description }}</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('roles.edit', $role) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Permissions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    <i class="fas fa-key text-blue-600 mr-2"></i>Permissions ({{ $role->permissions->count() }})
                </h3>
                
                @if($role->permissions->count() > 0)
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach($role->permissions as $permission)
                            <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $permission->display_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $permission->name }}</p>
                                    @if($permission->description)
                                        <p class="text-xs text-gray-400 mt-1">{{ $permission->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">
                        <i class="fas fa-info-circle text-4xl text-gray-300 mb-2"></i>
                        <br>Belum ada permission yang diberikan ke role ini.
                    </p>
                @endif
            </div>
        </div>

        <!-- Users -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-white border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    <i class="fas fa-users text-indigo-600 mr-2"></i>Users dengan Role Ini ({{ $role->users->count() }})
                </h3>
                
                @if($role->users->count() > 0)
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($role->users as $user)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email ?? $user->username }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($user->status_akun === 'aktif')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                        <br>Belum ada user yang memiliki role ini.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

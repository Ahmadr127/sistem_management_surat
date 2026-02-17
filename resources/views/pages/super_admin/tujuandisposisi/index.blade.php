@extends('layouts.app')

@section('title', 'Aturan Disposisi')

@section('content')
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Aturan Disposisi</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola aturan pengiriman disposisi antar role</p>
            </div>
            <a href="{{ route('disposisi-assignments.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                <i class="ri-add-line mr-1"></i> Tambah Aturan
            </a>
        </div>

        <div class="p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Role Pengirim</th>
                            <th scope="col" class="px-6 py-3">Role Penerima</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $key => $group)
                        @php
                            $first = $group->first();
                            $sourceName = \App\Models\DisposisiAssignment::getSourceName($first->source_role, $first->source_user_id);
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $sourceName }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($group as $item)
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                            {{ \App\Models\DisposisiAssignment::getRoleName($item->target_role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('disposisi-assignments.edit', $key) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="ri-pencil-line text-lg"></i>
                                    </a>
                                    <form action="{{ route('disposisi-assignments.destroy', $key) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua aturan untuk pengirim ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Belum ada aturan disposisi yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

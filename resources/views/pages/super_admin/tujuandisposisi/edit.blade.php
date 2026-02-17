@extends('layouts.app')

@section('title', 'Edit Aturan Disposisi')

@section('content')
    <div class="bg-white rounded-lg shadow-sm w-full mx-auto">
        <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Edit Aturan Disposisi</h2>
                <p class="text-xs text-gray-500 mt-1">Ubah aturan pengiriman disposisi</p>
            </div>
            <a href="{{ route('disposisi-assignments.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="ri-close-line text-xl"></i>
            </a>
        </div>

        <div class="p-8">
            <form action="{{ route('disposisi-assignments.update', $key) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.super_admin.tujuandisposisi._form')
            </form>
        </div>
    </div>
@endsection

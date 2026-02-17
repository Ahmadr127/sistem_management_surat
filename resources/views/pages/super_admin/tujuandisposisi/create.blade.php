@extends('layouts.app')

@section('title', 'Tambah Aturan Disposisi')

@section('content')
    <div class="bg-white rounded-lg shadow-sm w-full mx-auto">
        <div class="px-8 py-6 border-b border-gray-100 bg-white flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Tambah Aturan Disposisi</h2>
                <p class="text-xs text-gray-500 mt-1">Buat aturan baru untuk pengiriman disposisi</p>
            </div>
            <a href="{{ route('disposisi-assignments.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="ri-close-line text-xl"></i>
            </a>
        </div>

        <div class="p-8">
            <form action="{{ route('disposisi-assignments.store') }}" method="POST">
                @csrf
                @include('pages.super_admin.tujuandisposisi._form')
            </form>
        </div>
    </div>
@endsection

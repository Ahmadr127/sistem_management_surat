@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome Section --}}
    <div class="bg-gradient-to-br from-green-600 via-green-700 to-emerald-800 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>
        
        <div class="relative flex items-center justify-between">
            <div>
                <p class="text-green-200 text-sm font-medium">Selamat Datang Kembali,</p>
                <h1 class="text-2xl md:text-3xl font-bold mt-1">{{ auth()->user()->name }} 👋</h1>
                <p class="text-green-100 mt-2 flex items-center flex-wrap gap-2">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                        {{ auth()->user()->jabatan_name ?? auth()->user()->role_display_name }}
                    </span>
                    @if(auth()->user()->organizationUnit)
                    <span class="bg-white/10 px-3 py-1 rounded-full text-sm">
                        {{ auth()->user()->organizationUnit->name }}
                    </span>
                    @endif
                </p>
            </div>
            <div class="hidden md:flex flex-col items-end">
                <div class="text-right">
                    <p class="text-green-200 text-sm">{{ now()->translatedFormat('l') }}</p>
                    <p class="text-2xl font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards Row --}}
    <div class="flex flex-wrap gap-4">
        {{-- Surat Masuk Card --}}
        @if(auth()->user()->hasPermission('manage_surat_masuk') && isset($stats['surat_masuk']))
        <a href="{{ url('/suratmasuk') }}" class="group flex-1 min-w-[280px]">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 hover:-translate-y-1 h-full">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-inbox text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-3xl font-bold text-gray-800">{{ number_format($stats['surat_masuk']['total']) }}</span>
                        <span class="text-xs text-gray-500">Total</span>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">Surat Masuk</h3>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center text-sm">
                            @if($stats['surat_masuk']['belum_dibaca'] > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                {{ $stats['surat_masuk']['belum_dibaca'] }} perlu ditindaklanjuti
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Semua sudah ditindaklanjuti
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>Bulan ini: {{ $stats['surat_masuk']['bulan_ini'] }}</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
        @endif

        {{-- Surat Keluar Card --}}
        @if(auth()->user()->hasPermission('manage_surat_keluar') && isset($stats['surat_keluar']))
        <a href="{{ route('suratkeluar.index') }}" class="group flex-1 min-w-[280px]">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg hover:border-green-200 transition-all duration-300 hover:-translate-y-1 h-full">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-paper-plane text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-3xl font-bold text-gray-800">{{ number_format($stats['surat_keluar']['total']) }}</span>
                        <span class="text-xs text-gray-500">Total</span>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Surat Keluar</h3>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center text-sm">
                            @if($stats['surat_keluar']['menunggu_persetujuan'] > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>
                                {{ $stats['surat_keluar']['menunggu_persetujuan'] }} menunggu approval
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Semua sudah diproses
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>Bulan ini: {{ $stats['surat_keluar']['bulan_ini'] }}</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
        @endif

        {{-- Surat Unit Manager Card --}}
        @if(auth()->user()->hasPermission('create_surat_unit') && isset($stats['surat_unit']))
        <a href="{{ route('surat-unit-manager.index') }}" class="group flex-1 min-w-[280px]">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg hover:border-purple-200 transition-all duration-300 hover:-translate-y-1 h-full">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-3xl font-bold text-gray-800">{{ number_format($stats['surat_unit']['total']) }}</span>
                        <span class="text-xs text-gray-500">Total</span>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">Surat Unit Manager</h3>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center text-sm">
                            @if($stats['surat_unit']['menunggu_manager'] > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1.5 animate-pulse"></span>
                                {{ $stats['surat_unit']['menunggu_manager'] }} menunggu approval
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Tidak ada pending
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>Disetujui: {{ $stats['surat_unit']['disetujui'] }}</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
        @endif

        {{-- Persetujuan Surat Card --}}
        @if(auth()->user()->hasPermission('approve_surat_unit') && isset($stats['persetujuan']))
        @php
            $approvalUrl = route('surat-unit-manager.manager.index'); // default
            $user = auth()->user();
            
            if ($user->hasPermission('manage_pum')) { // Manager Keuangan
                $approvalUrl = route('surat-unit-manager.manager-keuangan.index');
            } elseif ($user->hasPermission('manage_surat_keluar')) { // Sekretaris
                $approvalUrl = route('surat-unit-manager.sekretaris.index');
            } elseif ($user->hasPermission('approve_disposisi')) { // Direktur
                $approvalUrl = route('surat-unit-manager.dirut.index');
            }
        @endphp
        <a href="{{ $approvalUrl }}" class="group flex-1 min-w-[280px]">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg hover:border-red-200 transition-all duration-300 hover:-translate-y-1 h-full">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-check-double text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-3xl font-bold text-gray-800">{{ number_format($stats['persetujuan']['total_disetujui'] + $stats['persetujuan']['total_ditolak'] + $stats['persetujuan']['menunggu_approval']) }}</span>
                        <span class="text-xs text-gray-500">Total</span>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-800 group-hover:text-red-600 transition-colors">Persetujuan Surat</h3>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center text-sm">
                            @if($stats['persetujuan']['menunggu_approval'] > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                {{ $stats['persetujuan']['menunggu_approval'] }} perlu disetujui
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Tidak ada pending
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span><i class="fas fa-check text-green-500 mr-1"></i>{{ $stats['persetujuan']['total_disetujui'] }} <i class="fas fa-times text-red-500 ml-2 mr-1"></i>{{ $stats['persetujuan']['total_ditolak'] }}</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
        @endif
    </div>

    {{-- Quick Actions & Recent Activities Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Quick Actions --}}
        @if(count($quickActions) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-yellow-500/30">
                    <i class="fas fa-bolt text-white text-sm"></i>
                </div>
                Aksi Cepat
            </h2>
            <div class="grid grid-cols-2 gap-3">
                @foreach($quickActions as $action)
                <a href="{{ $action['url'] }}" 
                   class="group flex items-center p-3 bg-gray-50 hover:bg-gradient-to-r hover:from-{{ $action['color'] }}-50 hover:to-{{ $action['color'] }}-100 rounded-xl transition-all duration-200 hover:shadow-md border border-transparent hover:border-{{ $action['color'] }}-200">
                    <div class="w-10 h-10 bg-{{ $action['color'] }}-100 group-hover:bg-{{ $action['color'] }}-200 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <i class="fas {{ $action['icon'] }} text-{{ $action['color'] }}-600"></i>
                    </div>
                    <div class="ml-3 min-w-0">
                        <h3 class="font-medium text-gray-800 text-sm truncate">{{ $action['title'] }}</h3>
                        <p class="text-xs text-gray-500 truncate">{{ $action['description'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recent Activities --}}
        @if(count($recentActivities) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-lg shadow-blue-500/30">
                    <i class="fas fa-clock text-white text-sm"></i>
                </div>
                Aktivitas Terbaru
            </h2>
            <div class="space-y-3">
                @foreach($recentActivities as $activity)
                <a href="{{ $activity['url'] }}" 
                   class="group flex items-start p-3 hover:bg-gray-50 rounded-xl transition-colors">
                    <div class="w-10 h-10 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-800 text-sm">{{ $activity['title'] }}</h3>
                            <span class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-500 truncate mt-0.5">{{ $activity['description'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- No Permissions Message --}}
    @if(empty($stats))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-lock text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Akses</h3>
        <p class="text-gray-500 max-w-md mx-auto">
            Anda belum memiliki akses ke fitur surat menyurat. Silakan hubungi administrator untuk mendapatkan akses.
        </p>
    </div>
    @endif
</div>
@endsection

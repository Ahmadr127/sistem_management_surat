@extends('home')

@section('title', 'Dashboard - SISM Azra')

@section('content')
    <div x-data="dashboardStats">
        <!-- Welcome Section -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ Auth::user()->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Berikut ringkasan aktivitas surat menyurat Anda</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-lg shadow-sm">
                <i class="ri-calendar-line"></i>
                <span>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Surat -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="ri-mail-line text-xl text-green-600"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800" x-text="stats.totalSurat || 0"></h3>
                <p class="text-sm text-gray-500 mt-1">Total Surat Masuk</p>
            </div>

            <!-- Total Disposisi -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="ri-send-plane-line text-xl text-blue-600"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800" x-text="stats.totalDisposisi || 0"></h3>
                <p class="text-sm text-gray-500 mt-1">Total Disposisi</p>
            </div>

            <!-- Disposisi Belum Selesai -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="ri-time-line text-xl text-yellow-600"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800" x-text="stats.disposisiBelumSelesai || 0"></h3>
                <p class="text-sm text-gray-500 mt-1">Disposisi Belum Selesai</p>
            </div>

            <!-- Disposisi Selesai -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="ri-check-double-line text-xl text-purple-600"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800" x-text="stats.disposisiSelesai || 0"></h3>
                <p class="text-sm text-gray-500 mt-1">Disposisi Selesai</p>
            </div>
        </div>

        <!-- Recent Activity & Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex-1">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h2>
                    <a href="{{ url('/suratmasuk') }}" class="text-sm text-green-600 hover:text-green-700">
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    <template x-for="activity in recentActivities" :key="activity.id">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                                <i class="ri-mail-send-line text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0 max-w-xs md:max-w-md">
                                <h4 class="text-sm font-medium text-gray-800" x-text="activity.nomor_surat"></h4>
                                <p class="text-xs text-gray-500 block w-full truncate" x-text="activity.perihal"></p>
                            </div>
                            <span class="text-xs text-gray-400" x-text="formatDate(activity.created_at)"></span>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <template x-if="recentActivities.length === 0">
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">Belum ada aktivitas terbaru</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex-1">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Aksi Cepat</h2>
                <div class="grid grid-cols-2 gap-4">
                    @if (Auth::user()->role === 'admin')
                        <a href="{{ url('/surat-keluar') }}"
                            class="flex items-center justify-center p-4 rounded-xl bg-green-50 hover:bg-green-100 transition-colors">
                            <i class="ri-add-line mr-2 text-green-600"></i>
                            <span class="text-sm font-medium text-green-600">Buat Surat Baru</span>
                        </a>
                    @endif

                    @if (Auth::user()->role === 'admin' || Auth::user()->role === 'staff')
                        <a href="{{ url('/disposisi/create') }}"
                            class="flex items-center justify-center p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors">
                            <i class="ri-send-plane-line mr-2 text-blue-600"></i>
                            <span class="text-sm font-medium text-blue-600">Buat Disposisi</span>
                        </a>
                    @endif

                    <a href="{{ url('/arsip') }}"
                        class="flex items-center justify-center p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors">
                        <i class="ri-search-line mr-2 text-purple-600"></i>
                        <span class="text-sm font-medium text-purple-600">Arsip Surat</span>
                    </a>

                    <a href="{{ url('/laporan') }}"
                        class="flex items-center justify-center p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                        <i class="ri-file-chart-line mr-2 text-gray-600"></i>
                        <span class="text-sm font-medium text-gray-600">Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardStats', () => ({
                stats: {
                    totalSurat: 0,
                    totalDisposisi: 0,
                    disposisiBelumSelesai: 0,
                    disposisiSelesai: 0
                },
                recentActivities: [],
                loading: true,
                error: null,

                init() {
                    this.loadStats();
                    this.loadRecentActivities();
                },

                async loadStats() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const response = await fetch('/api/dashboard/stats');
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal mengambil data statistik');
                        }

                        console.log('Stats data:', data); // Debug log
                        
                        if (data.error) {
                            throw new Error(data.message);
                        }

                        this.stats = {
                            totalSurat: parseInt(data.totalSurat) || 0,
                            totalDisposisi: parseInt(data.totalDisposisi) || 0,
                            disposisiBelumSelesai: parseInt(data.disposisiBelumSelesai) || 0,
                            disposisiSelesai: parseInt(data.disposisiSelesai) || 0
                        };
                    } catch (error) {
                        console.error('Error loading stats:', error);
                        this.error = error.message;
                        // Set default values on error
                        this.stats = {
                            totalSurat: 0,
                            totalDisposisi: 0,
                            disposisiBelumSelesai: 0,
                            disposisiSelesai: 0
                        };
                    } finally {
                        this.loading = false;
                    }
                },

                async loadRecentActivities() {
                    try {
                        const response = await fetch('/api/dashboard/recent-activities');
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal mengambil data aktivitas');
                        }

                        console.log('Activities data:', data); // Debug log
                        this.recentActivities = Array.isArray(data) ? data : [];
                    } catch (error) {
                        console.error('Error loading activities:', error);
                        this.recentActivities = [];
                    }
                },

                formatDate(date) {
                    try {
                        return new Date(date).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch (error) {
                        console.error('Error formatting date:', error);
                        return 'Invalid date';
                    }
                }
            }));
        });
    </script>
@endpush

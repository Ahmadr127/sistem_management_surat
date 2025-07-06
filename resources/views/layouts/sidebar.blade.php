<aside
    class="w-64 bg-white shadow-lg border-r border-gray-100 flex-shrink-0 fixed top-0 left-0 h-screen font-[Poppins] flex flex-col z-50 md:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out">
    <!-- Header/Logo Section -->
    <div class="p-6 border-b border-gray-100 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div class="relative group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="relative w-32 h-16 object-contain transform group-hover:scale-105 transition duration-200">
            </div>
            <!-- Close Button (Mobile Only) -->
            <button id="close-sidebar" class="md:hidden text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <p class="text-center text-gray-500 text-xs mt-1 font-medium tracking-wide">Sistem Informasi Surat Menyurat</p>
    </div>

    <!-- Navigation Section dengan overflow yang tepat -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 scrollbar-thin">
        <div class="px-4 mb-4">
            <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Menu Utama</p>
        </div>

        @if (Auth::user()->role === 5)
            <!-- Sidebar khusus Sekretaris ASP -->
            <div class="space-y-2.5">
                <a href="{{ url('/suratmasuk') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('suratmasuk*') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="ri-mail-download-line text-xl {{ Request::is('suratmasuk*') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3 font-medium">Surat Masuk</span>
                    @if (Request::is('suratmasuk*'))
                        <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                    @endif
                </a>
                <a href="{{ route('suratkeluar.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('suratkeluar*') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="ri-send-plane-line text-xl {{ Request::is('suratkeluar*') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3 font-medium">Surat Keluar</span>
                    @if (Request::is('suratkeluar*'))
                        <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                    @endif
                </a>
                <a href="{{ url('/arsip') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('arsip') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="ri-archive-line text-xl {{ Request::is('arsip') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3 font-medium">Arsip</span>
                    @if (Request::is('arsip'))
                        <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                    @endif
                </a>
                <a href="{{ route('laporan') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('laporan') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i class="ri-file-chart-line text-xl {{ Request::is('laporan') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3 font-medium">Laporan</span>
                    @if (Request::is('laporan'))
                        <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                    @endif
                </a>
            </div>
        @else
        <div class="space-y-2.5">
            <!-- Dashboard (Semua Role) -->
            <a href="{{ url('/dashboard') }}"
                class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <i
                    class="ri-dashboard-3-line text-xl {{ Request::is('dashboard') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                <span class="ml-3 font-medium">Dashboard</span>
                @if (Request::is('dashboard'))
                    <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                @endif
            </a>

            <!-- Manage User (Hanya Super Admin) -->
            @if (Auth::user()->role === 1 || Auth::user()->role === 3)
                <div class="relative" x-data="{ open: {{ Request::is('manageuser*', 'managejabatan*', 'manageperusahaan*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center py-3 px-4 rounded-xl transition-all duration-200 group text-gray-600 hover:bg-gray-50">
                        <i class="ri-settings-2-line text-xl text-gray-400 group-hover:text-gray-600"></i>
                        <span class="ml-3 font-medium">Manajemen</span>
                        <i class="ri-arrow-down-s-line ml-auto transition-transform"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0" class="pl-10 pr-4 space-y-1 mt-1">
                        @if (Auth::user()->role === 3)
                        <a href="{{ route('manageuser.index') }}"
                            class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ Request::routeIs('manageuser.*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="ri-user-settings-line text-lg mr-3"></i>
                            <span class="font-medium">Manage User</span>
                            @if (Request::routeIs('manageuser.*'))
                                <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                            @endif
                        </a>

                        <a href="{{ route('managejabatan.index') }}"
                            class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ Request::routeIs('managejabatan.*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="ri-profile-line text-lg mr-3"></i>
                            <span class="font-medium">Manage Jabatan</span>
                            @if (Request::routeIs('managejabatan.*'))
                                <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                            @endif
                        </a>
                        @endif

                        @if (Auth::user()->role === 1 || Auth::user()->role === 3)
                        <a href="{{ route('manageperusahaan.index') }}"
                            class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ Request::routeIs('manageperusahaan.*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="ri-building-line text-lg mr-3"></i>
                            <span class="font-medium">Manage Perusahaan</span>
                            @if (Request::routeIs('manageperusahaan.*'))
                                <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Transaksi Dropdown (Untuk Staff, Admin, Direktur, Super Admin, dan Manager) -->
            @if (in_array(Auth::user()->role, [0,1,2,3,4,6,7]))
                <div class="relative" x-data="{ open: {{ Request::is('disposisi*', 'suratkeluar*', 'jadwal*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center py-3 px-4 rounded-xl transition-all duration-200 group text-gray-600 hover:bg-gray-50">
                        <i class="ri-folder-line text-xl text-gray-400 group-hover:text-gray-600"></i>
                        <span class="ml-3 font-medium">Surat Menyurat</span>
                        <i class="ri-arrow-down-s-line ml-auto transition-transform"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0" class="pl-10 pr-4 space-y-1 mt-1">

                        <!-- Surat Masuk untuk semua role -->
                        <a href="{{ url('/suratmasuk') }}"
                            class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ Request::is('suratmasuk*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="ri-mail-download-line text-lg mr-3"></i>
                            <span class="font-medium">Surat Masuk</span>
                            @if (Request::is('suratmasuk*'))
                                <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                            @endif
                        </a>

                        <!-- Surat Keluar untuk Staff, Admin, Super Admin, dan Manager (bukan Direktur dan Unit) -->
                        @if (in_array(Auth::user()->role, [1, 3, 4, 6, 7]))
                            <a href="{{ route('suratkeluar.index') }}"
                                class="flex items-center py-2 px-4 rounded-lg transition-all duration-200 {{ Request::is('suratkeluar*') ? 'text-green-600 bg-green-50' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i class="ri-send-plane-line text-lg mr-3"></i>
                                <span class="font-medium">Surat Keluar</span>
                                @if (Request::is('suratkeluar*'))
                                    <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                                @endif
                            </a>
                        @endif

                        <!-- Surat Unit Manager untuk Unit dan Manager -->
                        @if (Auth::user()->role === 0)
                            <a href="{{ route('surat-unit-manager.index') }}"
                                class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('surat-unit-manager*') && !Request::is('surat-unit-manager/manager*') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i class="ri-briefcase-4-line text-xl {{ Request::is('surat-unit-manager*') && !Request::is('surat-unit-manager/manager*') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                                <span class="ml-3 font-medium">Surat Unit Manager</span>
                                @if (Request::is('surat-unit-manager*') && !Request::is('surat-unit-manager/manager*'))
                                    <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                                @endif
                            </a>
                        @elseif (Auth::user()->role === 4)
                            <a href="{{ route('surat-unit-manager.manager.index') }}"
                                class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('surat-unit-manager/manager*') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i class="ri-briefcase-4-line text-xl {{ Request::is('surat-unit-manager/manager*') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                                <span class="ml-3 font-medium">Surat Unit Manager</span>
                                @if (Request::is('surat-unit-manager/manager*'))
                                    <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                                @endif
                            </a>
                        @elseif (Auth::user()->role === 1)
                        @endif

                        <!-- Jadwal untuk Admin dan Direktur -->

                    </div>
                </div>
            @endif
        </div>

        <!-- Menu Lainnya (untuk semua role) -->
        @if (in_array(Auth::user()->role, [0,1,2,3,4,6,7]))
            <div class="px-4 mb-4 mt-8">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Lainnya</p>
            </div>

            <div class="space-y-2.5">
                <a href="{{ route('laporan') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('laporan') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i
                        class="ri-file-chart-line text-xl {{ Request::is('laporan') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3 font-medium">Laporan</span>
                    @if (Request::is('laporan'))
                        <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                    @endif
                </a>

                <!-- Arsip (untuk semua role) -->
                <a href="{{ url('/arsip') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 group {{ Request::is('arsip') ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i
                        class="ri-archive-line text-xl {{ Request::is('arsip') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3 font-medium">Arsip</span>
                    @if (Request::is('arsip'))
                        <div class="ml-auto h-2 w-2 rounded-full bg-green-600"></div>
                    @endif
                </a>
            </div>
        @endif
        @endif
    </nav>
</aside>

<!-- Background Overlay for Mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden transition-opacity duration-300" onclick="closeSidebar()"></div>

<!-- Tambahkan margin kiri pada main content di home.blade.php -->
<style>
    /* Custom Scrollbar untuk sidebar */
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 20px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #cbd5e0;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-thin {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: thin;
        /* Firefox */
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .ml-64 {
            margin-left: 0 !important;
            width: 100% !important;
        }
        aside.show-mobile {
            transform: translateX(0) !important;
        }
        body.sidebar-open {
            overflow: hidden;
        }
    }
</style>

<script>
    function closeSidebar() {
        const sidebar = document.querySelector('aside');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('show-mobile');
            overlay.classList.add('hidden');
            document.body.classList.remove('sidebar-open');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('close-sidebar');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }
    });
</script>


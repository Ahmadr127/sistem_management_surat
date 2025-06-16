<header
    class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center font-[Poppins] header-fixed">
    <!-- Left Section -->
    <div class="flex items-center space-x-8">
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-toggle" class="md:hidden text-gray-500 focus:outline-none">
            <i class="ri-menu-line text-2xl"></i>
        </button>
        
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Selamat datang kembali</p>
        </div>

        <!-- Quick Stats -->
        <div class="hidden md:flex items-center space-x-3">
            <div class="flex items-center space-x-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                <i class="ri-mail-line text-gray-400"></i>
                <span class="text-sm text-gray-600">12</span>
            </div>
            <div class="flex items-center space-x-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                <i class="ri-time-line text-gray-400"></i>
                <span class="text-sm text-gray-600">5</span>
            </div>
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-4">
        <!-- Search -->
        <div class="relative hidden sm:flex items-center">
            <i class="ri-search-line absolute left-3 text-gray-400 pointer-events-none"></i>
            <input type="text" placeholder="Cari..."
                class="w-48 h-9 px-3 pl-9 text-sm bg-gray-50 border-0 rounded-lg focus:bg-white focus:ring-1 focus:ring-gray-200 transition-all">
        </div>

        <!-- Notifications -->
        <button class="relative p-1.5 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="ri-notification-3-line text-gray-400"></i>
            <div class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full"></div>
        </button>

        <!-- Profile Menu -->
        <div x-data="{ isOpen: false }" class="relative">
            <button @click="isOpen = !isOpen" @click.away="isOpen = false"
                class="flex items-center space-x-3 p-1.5 hover:bg-gray-50 rounded-lg transition-all">
                <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                    <img src="{{ Auth::user()->foto_url }}" alt="Profile" class="w-full h-full object-cover"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF'">
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-sm text-gray-700 font-medium leading-none">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ Auth::user()->jabatanName }}
                    </p>
                </div>
                <i class="ri-arrow-down-s-line text-gray-400 hidden sm:block" :class="{ 'transform rotate-180': isOpen }"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 top-full mt-1 w-48 py-2 bg-white rounded-lg shadow-sm border border-gray-100 z-50">

                <a href="{{ route('profile') }}"
                    class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="ri-user-line w-4 h-4 mr-2"></i>
                    Profil
                </a>
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="ri-settings-4-line w-4 h-4 mr-2"></i>
                    Pengaturan
                </a>
                <div class="h-px bg-gray-100 my-2"></div>

                <!-- Logout Form -->
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="button" onclick="logout()"
                        class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="ri-logout-box-r-line w-4 h-4 mr-2"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tambahkan CSS kustom untuk SweetAlert -->
    <style>
        .swal2-border-radius {
            border-radius: 1rem !important;
        }

        .swal2-title-custom {
            font-family: 'Poppins', sans-serif !important;
            font-size: 1.25rem !important;
            color: #1f2937 !important;
        }

        .swal2-html-container-custom {
            font-family: 'Poppins', sans-serif !important;
            font-size: 0.875rem !important;
            color: #6b7280 !important;
        }

        .swal2-confirm {
            padding: 0.5rem 1.5rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.5rem !important;
            font-family: 'Poppins', sans-serif !important;
        }

        .swal2-cancel {
            padding: 0.5rem 1.5rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.5rem !important;
            font-family: 'Poppins', sans-serif !important;
        }
    </style>
</header>

<script>
    function logout() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari aplikasi",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal2-border-radius',
                title: 'swal2-title-custom',
                htmlContainer: 'swal2-html-container-custom',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
    
    // Mobile menu toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.querySelector('aside');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (mobileMenuToggle && sidebar) {
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show-mobile');
                document.body.classList.toggle('sidebar-open');
                
                if (overlay) {
                    overlay.classList.toggle('hidden');
                }
            });
        }
    });
</script>

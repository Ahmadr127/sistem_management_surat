<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'SISM Azra')</title>
    <link rel="icon" type="image/x-icon" href="images/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>

<body class="bg-gray-100">
    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Wrapper -->
        <div class="flex-1 ml-64 flex flex-col min-w-0 transition-all duration-300">
            <!-- Header -->
            @include('layouts.header')

            <!-- Main Content -->
            <main class="p-6 flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on mobile and init mobile view if needed
            function checkMobileView() {
                if (window.innerWidth < 768) {
                    document.querySelector('.ml-64')?.classList.add('mobile-view');
                } else {
                    document.querySelector('.ml-64')?.classList.remove('mobile-view');
                }
            }
            
            // Run on page load
            checkMobileView();
            
            // Run on resize
            window.addEventListener('resize', checkMobileView);
        });
    </script>

    @stack('scripts')
</body>

</html>

<style>
    /* Pastikan header tetap di atas */
    .header-fixed {
        position: sticky;
        top: 0;
        z-index: 40;
        width: 100%;
    }
    
    /* Fix for horizontal scrolling issues */
    body {
        overflow-x: hidden;
    }
    
    /* Improved sidebar and main content layout */
    .overflow-x-auto {
        overflow-x: auto;
    }
    
    /* Main content container */
    .min-w-fit {
        min-width: max-content;
        width: 100%;
    }
    
    /* Ensure that the content doesn't overlap with sidebar */
    @media (min-width: 768px) {
        .ml-64 {
            margin-left: 16rem;
            transition: margin-left 0.3s ease;
            width: calc(100% - 16rem);
        }
    }
    
    /* Mobile adjustments */
    @media (max-width: 767px) {
        .ml-64.mobile-view {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

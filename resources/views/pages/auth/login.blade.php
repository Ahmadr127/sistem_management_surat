<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SISM - RS Azra</title>
    <link rel="icon" type="image/x-icon" href="images/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: url("images/bg.png") center center / cover no-repeat fixed;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div
        class="flex flex-col md:flex-row bg-white shadow-2xl rounded-2xl overflow-hidden max-w-5xl mx-4 md:mx-auto w-full md:w-auto sm:w-4/5 sm:mx-auto">
        <div
            class="hidden md:block md:w-1/2 bg-gradient-to-br from-green-600 to-green-400 p-8 md:p-12 text-white relative">
            <div class="absolute inset-0 opacity-40 bg-cover bg-center backdrop-blur-sm"
                style="background-image: url('images/azra.jpg');"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-4xl font-bold mb-4 md:mb-6">Selamat Datang di SISM Azra</h1>
                <p class="text-lg md:text-xl mb-6 md:mb-10 font-light">Sistem Informasi Surat Menyurat Rumah Sakit Azra
                </p>
                <ul class="space-y-4 md:space-y-6">
                    <li class="flex items-center transform hover:translate-x-2 transition-transform duration-300">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-white/25 rounded-full flex items-center justify-center mr-3 md:mr-4 shadow-lg">
                            <i class="fa fa-calendar text-white text-lg md:text-xl"></i>
                        </div>
                        <span class="text-base md:text-lg">Manajemen Disposisi Surat</span>
                    </li>
                    <li class="flex items-center transform hover:translate-x-2 transition-transform duration-300">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-white/25 rounded-full flex items-center justify-center mr-3 md:mr-4 shadow-lg">
                            <i class="fa fa-tasks text-white text-lg md:text-xl"></i>
                        </div>
                        <span class="text-base md:text-lg">Manajemen Surat Keluar</span>
                    </li>
                    <li class="flex items-center transform hover:translate-x-2 transition-transform duration-300">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-white/25 rounded-full flex items-center justify-center mr-3 md:mr-4 shadow-lg">
                            <i class="fa fa-file-text text-white text-lg md:text-xl"></i>
                        </div>
                        <span class="text-base md:text-lg">Laporan Disposisi Surat</span>
                    </li>
                    <li class="flex items-center transform hover:translate-x-2 transition-transform duration-300">
                        <div
                            class="w-10 h-10 md:w-12 md:h-12 bg-white/25 rounded-full flex items-center justify-center mr-3 md:mr-4 shadow-lg">
                            <i class="fa fa-clock-o text-white text-lg md:text-xl"></i>
                        </div>
                        <span class="text-base md:text-lg">Akses Informasi Real-time</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex-1 p-4 md:p-12 bg-white">
            <div class="text-center mb-4 md:mb-10">
                <img src="images/logo.png" alt="Logo"
                    class="w-32 h-16 md:w-48 md:h-24 mx-auto mb-3 md:mb-6 drop-shadow-lg object-contain">
                <p class="text-sm md:text-lg text-gray-600">Sistem Informasi Surat Menyurat Rumah Sakit Azra</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-100 border border-red-400 text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-100 border border-green-400 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-3 md:space-y-6">
                @csrf

                <div class="relative group">
                    <i
                        class="fa fa-user absolute left-3 md:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-green-500 transition-colors"></i>
                    <input type="text" name="username" required
                        class="w-full pl-10 md:pl-12 pr-3 md:pr-4 py-3 md:py-4 text-sm md:text-base border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 transition-colors"
                        placeholder="Masukkan Username">
                </div>

                <div class="relative group">
                    <i
                        class="fa fa-lock absolute left-3 md:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-green-500 transition-colors"></i>
                    <input type="password" name="password" required
                        class="w-full pl-10 md:pl-12 pr-3 md:pr-4 py-3 md:py-4 text-sm md:text-base border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 transition-colors"
                        placeholder="Masukkan Password">
                </div>

                <button type="submit"
                    class="w-full py-3 md:py-4 bg-green-600 text-white text-base md:text-lg font-semibold rounded-xl hover:bg-green-700 transform hover:scale-[1.02] transition-all duration-300 shadow-lg">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>

</html>

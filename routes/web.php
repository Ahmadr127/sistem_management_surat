<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\DisposisiCommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JabatanController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\SuratUnitManagerController;
use App\Http\Controllers\SuratUnitManagerApprovalController;

// Redirect root ke login jika belum login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

// Route untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Route untuk semua user yang sudah login
Route::middleware(['auth', 'checkUserStatus'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Home & Profile
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    Route::get('/profile', function () {
        return view('pages.profile');
    })->name('profile');
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Comments API
    Route::prefix('api/disposisi')->name('api.disposisi.')->group(function () {
        Route::get('{disposisi}/comments', [DisposisiCommentController::class, 'index'])->name('comments.index');
        Route::post('{disposisi}/comments', [DisposisiCommentController::class, 'store'])->name('comments.store');
    });

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/disposisi/create', [DisposisiController::class, 'create'])->name('disposisi.create');
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::get('/arsip', [SuratKeluarController::class, 'arsip'])->name('arsip');
    Route::get('/suratmasuk', [SuratMasukController::class, 'index'])->name('suratmasuk.index');

    // API Dashboard
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/api/dashboard/recent-activities', [DashboardController::class, 'getRecentActivities']);
});

// Route untuk admin, staff, direktur, super admin dan manager
Route::middleware(['auth', 'checkRole:0,1,2,3,4,5'])->group(function () {
    // Laporan routes should use LaporanController
    Route::get('/laporan/disposisi-status/{disposisi}', [DisposisiController::class, 'updateStatus'])
        ->name('laporan.disposisi.status.update');

    // Surat Keluar
    Route::prefix('suratkeluar')->name('suratkeluar.')->group(function () {
        Route::get('/', [SuratKeluarController::class, 'index'])->name('index');
        Route::get('/create', [SuratKeluarController::class, 'create'])->name('create');
        Route::post('/', [SuratKeluarController::class, 'store'])->name('store');
        Route::get('/get-last-number', [SuratKeluarController::class, 'getLastNumber'])->name('getLastNumber');
        Route::get('/{suratKeluar}', [SuratKeluarController::class, 'show'])->name('show');
        Route::delete('/{suratKeluar}', [SuratKeluarController::class, 'destroy'])->name('destroy');
        Route::get('/{suratKeluar}/edit', [SuratKeluarController::class, 'edit'])->name('edit');
        Route::put('/{suratKeluar}', [SuratKeluarController::class, 'update'])->name('update');
        Route::get('/{suratKeluar}/download', [SuratKeluarController::class, 'download'])->name('download');
        
        // Routes for individual file handling
        Route::get('/{suratId}/download-file/{fileId}', [SuratKeluarController::class, 'downloadFile'])->name('download-file');
        Route::get('/{suratId}/preview-file/{fileId}', [SuratKeluarController::class, 'previewFile'])->name('preview-file');

        // Route untuk soft delete
        Route::get('/trashed/list', [SuratKeluarController::class, 'trashed'])->name('trashed');
        Route::post('/{id}/restore', [SuratKeluarController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [SuratKeluarController::class, 'forceDelete'])->name('forceDelete');
    });

    // Surat Unit Manager - Hanya untuk Staff (role 0)
    Route::prefix('surat-unit-manager')->name('surat-unit-manager.')->group(function () {
        Route::get('/', [SuratUnitManagerController::class, 'index'])->name('index');
        Route::get('/create', [SuratUnitManagerController::class, 'create'])->name('create');
        
        // Letakkan route spesifik di atas route dinamis
        Route::prefix('manager')->name('manager.')->group(function () {
            Route::get('/', [SuratUnitManagerApprovalController::class, 'managerIndex'])->name('index');
            Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'managerShow'])->name('show');
            Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'managerApproval'])->name('approval');
        });

        Route::prefix('sekretaris')->name('sekretaris.')->group(function () {
            Route::get('/', [SuratUnitManagerApprovalController::class, 'sekretarisIndex'])->name('index');
            Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'sekretarisShow'])->name('show');
            Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'sekretarisApproval'])->name('approval');
        });

        Route::prefix('dirut')->name('dirut.')->group(function () {
            Route::get('/', [SuratUnitManagerApprovalController::class, 'dirutIndex'])->name('index');
            Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'dirutShow'])->name('show');
            Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'dirutApproval'])->name('approval');
        });

        Route::post('/', [SuratUnitManagerController::class, 'store'])->name('store');
        Route::get('/{suratUnitManager}', [SuratUnitManagerController::class, 'show'])->name('show');
        Route::get('/{suratUnitManager}/edit', [SuratUnitManagerController::class, 'edit'])->name('edit');
        Route::put('/{suratUnitManager}', [SuratUnitManagerController::class, 'update'])->name('update');
        Route::delete('/{suratUnitManager}', [SuratUnitManagerController::class, 'destroy'])->name('destroy');
        Route::get('/{suratUnitManager}/download', [SuratUnitManagerController::class, 'download'])->name('download');
        Route::get('/{suratUnitManager}/preview', [SuratUnitManagerController::class, 'preview'])->name('preview');
        Route::get('/{suratUnitManager}/download-file/{fileId}', [SuratUnitManagerController::class, 'downloadFile'])->name('download-file');
        Route::get('/{suratUnitManager}/preview-file/{fileId}', [SuratUnitManagerController::class, 'previewFile'])->name('preview-file');
    });

    // Pengaturan & Jadwal
    Route::view('/pengaturan', 'pages.pengaturan')->name('pengaturan');
    Route::view('/jadwal', 'pages.jadwal')->name('jadwal');

    // Route untuk arsip
    Route::get('/arsip', [SuratKeluarController::class, 'arsip'])->name('arsip');
});

// Route khusus untuk admin dan direktur
Route::middleware(['auth', 'checkRole:1,2,5'])->group(function () {
    // App
    Route::get('/app', function () {
        return view('home');
    })->name('app');
    
    // Disposisi
    Route::prefix('disposisi')->name('disposisi.')->group(function () {
        Route::get('/', [DisposisiController::class, 'index'])->name('index');
        Route::post('/', [DisposisiController::class, 'store'])->name('store');
        Route::get('/{disposisi}', [DisposisiController::class, 'show'])->name('show');
        Route::put('/{disposisi}/status', [DisposisiController::class, 'updateStatus'])->name('updateStatus');
        
        // Route untuk komentar disposisi
        Route::post('/{disposisi}/comments', [DisposisiCommentController::class, 'store'])->name('comments.store');
        Route::get('/{disposisi}/comments', [DisposisiCommentController::class, 'index'])->name('comments.index');
    });

    // Persetujuan Surat Unit Manager - Manager (role 4)
    // Route::prefix('surat-unit-manager/manager')->name('surat-unit-manager.manager.')->group(function () {
    //     Route::get('/', [SuratUnitManagerApprovalController::class, 'managerIndex'])->name('index');
    //     Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'managerShow'])->name('show');
    //     Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'managerApproval'])->name('approval');
    // });

    // Persetujuan Surat Unit Manager - Sekretaris (role 1)
    // Route::prefix('surat-unit-manager/sekretaris')->name('surat-unit-manager.sekretaris.')->group(function () {
    //     Route::get('/', [SuratUnitManagerApprovalController::class, 'sekretarisIndex'])->name('index');
    //     Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'sekretarisShow'])->name('show');
    //     Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'sekretarisApproval'])->name('approval');
    // });

    // Persetujuan Surat Unit Manager - Direktur (role 2)
    // Route::prefix('surat-unit-manager/dirut')->name('surat-unit-manager.dirut.')->group(function () {
    //     Route::get('/', [SuratUnitManagerApprovalController::class, 'dirutIndex'])->name('index');
    //     Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'dirutShow'])->name('show');
    //     Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'dirutApproval'])->name('approval');
    // });
});

// Route khusus untuk super admin
Route::middleware(['auth', 'checkRole:3'])->group(function () {
    Route::get('/app', function () {
        return view('home');
    })->name('app');
    
    // User Management - View
    Route::get('/manageuser', [UserController::class, 'index'])->name('manageuser.index');
    
    // Manage Jabatan
    Route::get('/managejabatan', [JabatanController::class, 'index'])->name('managejabatan.index');
});

// Route untuk admin, sekretaris, dan super admin
Route::middleware(['auth', 'checkRole:0,1,3,5'])->group(function () {
    // Manage Perusahaan
    Route::get('/manageperusahaan', [PerusahaanController::class, 'index'])->name('manageperusahaan.index');
});

// Routes untuk Perusahaan
Route::prefix('api/perusahaan')->middleware(['auth'])->group(function () {
    Route::get('/', [PerusahaanController::class, 'getForDropdown'])->name('api.perusahaan.dropdown');
    Route::post('/', [PerusahaanController::class, 'store'])->middleware('checkRole:0,1,3,5')->name('api.perusahaan.store');
    Route::put('/{id}', [PerusahaanController::class, 'update'])->middleware('checkRole:0,1,3,5')->name('api.perusahaan.update');
    Route::delete('/{id}', [PerusahaanController::class, 'destroy'])->middleware('checkRole:0,1,3,5')->name('api.perusahaan.destroy');
    Route::get('/search', [PerusahaanController::class, 'search'])->name('perusahaan.search');
    Route::post('/quick-store', [PerusahaanController::class, 'quickStore'])->name('perusahaan.quickStore');
});

// Routes untuk Jabatan
Route::prefix('jabatan')->group(function () {
    Route::get('/', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/data', [JabatanController::class, 'getJabatan'])->name('jabatan.data');
    Route::post('/', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::put('/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
    Route::put('/{id}/toggle-status', [JabatanController::class, 'toggleStatus'])->name('jabatan.toggleStatus');
});

// API Routes
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Surat Keluar routes
    Route::get('/surat-keluar', [SuratKeluarController::class, 'getSuratKeluar'])->name('surat-keluar');
    Route::get('/surat-keluar/trashed', [SuratKeluarController::class, 'getTrashedSurat'])->name('surat-keluar.trashed');
    
    // Disposisi routes - order matters for route resolution!
    Route::get('/disposisi/surat/{suratId}', [DisposisiController::class, 'getDisposisiBySurat'])->name('disposisi.by-surat');
    Route::get('/disposisi/{id}/tujuan', [DisposisiController::class, 'getTujuanDisposisi'])->name('disposisi.tujuan');
    Route::get('/disposisi/{id}', [DisposisiController::class, 'show'])->name('disposisi.show');
    Route::post('/disposisi/{id}/update', [DisposisiController::class, 'update'])->name('disposisi.update');
    
    // User routes untuk disposisi (tidak untuk management)
    Route::get('/users/disposisi', [UserController::class, 'getForDisposisi'])->name('users.disposisi');
    
    // Route untuk users yang digunakan di disposisi (semua role)
    Route::get('/users/disposisi-list', function () {
        return User::with('jabatan')
                ->where('status_akun', 'aktif')
                ->where('id', '!=', auth()->id())
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'jabatan' => $user->jabatan ? $user->jabatan->nama_jabatan : ($user->name == 'Direktur Utama' ? 'DIRUT' : 'Tidak ada jabatan'),
                        'email' => $user->email
                    ];
                })
                ->sortBy('name')
                ->values();
    })->name('users.disposisi-list');
    
    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities']);
    });
    
    // Laporan routes
    Route::get('/laporan', [LaporanController::class, 'getData'])->name('laporan');
    
    // Surat Masuk routes
    Route::get('/surat-masuk', [SuratMasukController::class, 'getSuratMasuk'])->name('suratmasuk');
    Route::post('/surat-masuk/{id}/read', [SuratMasukController::class, 'markAsRead']);
    
    // Get Direktur ID
    Route::get('/get-direktur-id', [SuratKeluarController::class, 'getDirekturId'])->name('get-direktur-id');
});

// User Management API Routes (khusus super admin) - HARUS SETELAH route umum
Route::middleware(['auth', 'checkRole:3'])->prefix('api')->name('api.')->group(function () {
    Route::get('/users', [UserController::class, 'getUsers'])->name('users.get');
    Route::get('/users/managers', [UserController::class, 'getManagers'])->name('users.managers');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

Route::put('/api/surat-keluar/{id}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
Route::post('/api/surat-keluar/{id}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update.with-file');

Route::post('/suratkeluar/get-last-number', [SuratKeluarController::class, 'getLastNumber'])
    ->name('suratkeluar.getLastNumber');

Route::get('/suratkeluar/create', [SuratKeluarController::class, 'create'])->name('suratkeluar.create');

Route::get('/suratkeluar/{suratKeluar}/preview', [SuratKeluarController::class, 'preview'])->name('suratkeluar.preview');

// API untuk disposisi
// Route::get('/api/disposisi/{disposisi}/tujuan', [DisposisiController::class, 'getTujuanDisposisi']);

// API routes untuk disposisi (tambahkan role 2)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::post('/disposisi/{id}/update', [DisposisiController::class, 'update'])->name('api.disposisi.update');
    Route::get('/disposisi/{id}', [DisposisiController::class, 'show'])->name('api.disposisi.show');
    // Removing duplicate route
    // Route::get('/disposisi/{id}/tujuan', [DisposisiController::class, 'getTujuanDisposisi'])->name('api.disposisi.tujuan');
    Route::get('/users/disposisi', [UserController::class, 'getForDisposisi'])->name('api.users.disposisi');
    Route::get('/surat-keluar', [SuratKeluarController::class, 'getSuratKeluar'])->name('api.surat.index');
});

// API routes untuk disposisi
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Tambahkan route yang mengembalikan available users dan tujuan yang sudah dipilih dalam satu endpoint
    Route::get('/disposisi/{id}/tujuan', [DisposisiController::class, 'getTujuanDisposisiWithUsers'])->name('api.disposisi.tujuan.with-users');
});

// Routes untuk Surat Masuk
Route::middleware(['auth'])->group(function () {
    Route::get('/suratmasuk', [SuratMasukController::class, 'index'])->name('suratmasuk.index');
    Route::get('/api/surat-masuk', [SuratMasukController::class, 'getSuratMasuk'])->name('api.suratmasuk');
    Route::post('/api/surat-masuk/{id}/read', [SuratMasukController::class, 'markAsRead']);
});

// Tambahkan route API untuk surat masuk
Route::get('/api/surat-keluar', [App\Http\Controllers\SuratKeluarController::class, 'getSuratKeluar']);

// Route untuk API surat keluar
Route::get('/api/surat-keluar', [SuratKeluarController::class, 'getSuratKeluar'])
    ->name('api.suratkeluar')
    ->middleware(['auth']);

// Tambahkan route untuk update disposisi
Route::post('/api/disposisi/{id}/update', [DisposisiController::class, 'update'])->name('api.disposisi.update');

// Add these routes for the reporting feature
Route::middleware(['auth'])->group(function() {
    // API endpoint for fetching report data
    Route::get('/api/laporan', [LaporanController::class, 'getLaporan'])->name('laporan.export');
    // Route for exporting report as Excel or PDF
    Route::get('/export-laporan', [LaporanController::class, 'exportLaporan'])->name('export-laporan');
});

Route::delete('/suratkeluar/{surat}/file/{file}', [App\Http\Controllers\SuratKeluarController::class, 'deleteFile'])->name('suratkeluar.file.delete');

// Route khusus untuk manager (role 4)
Route::middleware(['auth', 'checkRole:4'])->group(function () {
    Route::prefix('surat-unit-manager/manager')->name('surat-unit-manager.manager.')->group(function () {
        Route::get('/', [SuratUnitManagerApprovalController::class, 'managerIndex'])->name('index');
        Route::get('/{suratUnitManager}', [SuratUnitManagerApprovalController::class, 'managerShow'])->name('show');
        Route::post('/{suratUnitManager}/approval', [SuratUnitManagerApprovalController::class, 'managerApproval'])->name('approval');
    });
});



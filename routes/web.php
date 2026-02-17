<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\DisposisiCommentController;
use App\Http\Controllers\UserController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::get('/arsip', [SuratKeluarController::class, 'arsip'])->name('arsip');
    Route::get('/suratmasuk', [SuratMasukController::class, 'index'])->name('suratmasuk.index');

    // API Dashboard
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/api/dashboard/recent-activities', [DashboardController::class, 'getRecentActivities']);
});

// Route untuk admin, staff, direktur, super admin dan manager
Route::middleware(['auth', 'checkRole:0,1,2,3,4,5,7,8'])->group(function () {
    // Laporan routes should use LaporanController
    Route::get('/laporan/disposisi-status/{disposisi}', [DisposisiController::class, 'updateStatus'])
        ->name('laporan.disposisi.status.update');

    // Surat Keluar
    Route::prefix('suratkeluar')->name('suratkeluar.')->group(function () {
        Route::get('/', [SuratKeluarController::class, 'index'])->name('index');
        Route::get('/create', [SuratKeluarController::class, 'create'])->name('create');
        Route::post('/', [SuratKeluarController::class, 'store'])->name('store');

        Route::get('/{suratKeluar}/file/{fileId}/view', [SuratKeluarController::class, 'viewFile'])->name('file.view');
        Route::get('/{suratKeluar}/file/{fileId}/download', [SuratKeluarController::class, 'downloadFile'])->name('file.download');
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
        
        // Route universal untuk persetujuan surat
        Route::prefix('approval')->name('approval.')->group(function () {
            Route::get('/', [SuratUnitManagerApprovalController::class, 'approvalIndex'])->name('index');
            Route::get('/{id}', [SuratUnitManagerApprovalController::class, 'approvalShow'])->name('show');
            Route::post('/{id}', [SuratUnitManagerApprovalController::class, 'processApproval'])->name('process');
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
Route::middleware(['auth', 'checkRole:1,2,3,5,7,8'])->group(function () {
    // App
    Route::get('/app', function () {
        return view('home');
    })->name('app');
    
    // Generate Nomor Surat (hanya untuk Sekretaris)
    Route::get('/nomor/generate', function () {
        return view('pages.nomor.generate');
    })->name('nomor.generate');
    
    // Disposisi
    Route::prefix('disposisi')->name('disposisi.')->group(function () {
        Route::get('/', [DisposisiController::class, 'index'])->name('index');
        Route::get('/create', [DisposisiController::class, 'create'])->name('create');
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
    Route::get('/manageuser/create', [UserController::class, 'create'])->name('manageuser.create');
    Route::get('/manageuser/{user}/edit', [UserController::class, 'edit'])->name('manageuser.edit');
    
    
    // Permission Management
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    
    // Role Management
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    
    // Organization Type Management
    Route::resource('organization-types', App\Http\Controllers\OrganizationTypeController::class);
    
    // Organization Unit Management
    Route::resource('organization-units', App\Http\Controllers\OrganizationUnitController::class);
    
    // Organization Unit - Additional Routes
    Route::patch('organization-units/{organizationUnit}/update-head', [App\Http\Controllers\OrganizationUnitController::class, 'updateHead'])
        ->name('organization-units.update-head');
    Route::post('organization-units/{organizationUnit}/add-member', [App\Http\Controllers\OrganizationUnitController::class, 'addMember'])
        ->name('organization-units.add-member');
    Route::delete('organization-units/{organizationUnit}/remove-member/{user}', [App\Http\Controllers\OrganizationUnitController::class, 'removeMember'])
        ->name('organization-units.remove-member');

    // Disposisi Assignment Management - Custom Routes for Grouped Logic
    Route::get('disposisi-assignments', [App\Http\Controllers\DisposisiAssignmentController::class, 'index'])->name('disposisi-assignments.index');
    Route::get('disposisi-assignments/create', [App\Http\Controllers\DisposisiAssignmentController::class, 'create'])->name('disposisi-assignments.create');
    Route::post('disposisi-assignments', [App\Http\Controllers\DisposisiAssignmentController::class, 'store'])->name('disposisi-assignments.store');
    Route::get('disposisi-assignments/{source_role}/edit', [App\Http\Controllers\DisposisiAssignmentController::class, 'edit'])->name('disposisi-assignments.edit');
    Route::put('disposisi-assignments/{source_role}', [App\Http\Controllers\DisposisiAssignmentController::class, 'update'])->name('disposisi-assignments.update');
    Route::delete('disposisi-assignments/{source_role}', [App\Http\Controllers\DisposisiAssignmentController::class, 'destroy'])->name('disposisi-assignments.destroy');
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


// API Routes
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Surat Keluar routes
    Route::get('/surat-keluar', [SuratKeluarController::class, 'getSuratKeluar'])->name('surat-keluar');
    Route::get('/surat-keluar/trashed', [SuratKeluarController::class, 'getTrashedSurat'])->name('surat-keluar.trashed');
    Route::get('/surat-keluar/by-format', [SuratKeluarController::class, 'getByFormat'])->name('suratkeluar.byformat');
    Route::get('/surat-keluar/{id}', [SuratKeluarController::class, 'getDetail'])->name('surat-keluar.detail');
    
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
    Route::get('/users/independent-managers', [UserController::class, 'getIndependentManagers'])->name('users.independent-managers');
    Route::get('/users/connected-managers/{generalManagerId}', [UserController::class, 'getConnectedManagers'])->name('users.connected-managers');
    Route::get('/users/general-managers', [UserController::class, 'getGeneralManagers'])->name('users.general-managers');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Organization Units dropdown
    Route::get('/organization-units/list', [App\Http\Controllers\OrganizationUnitController::class, 'getUnits'])->name('organization-units.list');
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

// Route khusus untuk approval sudah ditangani di dalam group checkRole:0,1,2,3,4,5,7,8
// Manager (role 4) -> surat-unit-manager.manager.index
// Sekretaris (role 1, 5) -> surat-unit-manager.sekretaris.index  
// Direktur (role 2, 8) -> surat-unit-manager.dirut.index
// Manager Keuangan (role 7) -> surat-unit-manager.manager-keuangan.index


Route::post('/api/disposisi/{id}/keterangan-pengirim', [App\Http\Controllers\DisposisiController::class, 'updateKeteranganPengirim'])->name('api.disposisi.keterangan-pengirim');



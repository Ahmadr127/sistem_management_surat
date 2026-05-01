<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratUnitManager;
use App\Models\User;
use App\Models\Perusahaan;
use App\Services\SuratUnitToSuratKeluarService;
use Illuminate\Support\Facades\Validator;

class SuratUnitConversionController extends Controller
{
    protected $conversionService;

    public function __construct(SuratUnitToSuratKeluarService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    public function showConvertForm($id)
    {
        $suratUnit = SuratUnitManager::with('files')->findOrFail($id);

        if ($suratUnit->status_manager !== 'approved') {
            return redirect()->back()->with('error', 'Hanya surat yang sudah disetujui Manager yang dapat diajukan sebagai Surat Keluar.');
        }

        if ($suratUnit->surat_keluar_id) {
            return redirect()->route('suratkeluar.index')
                ->with('info', 'Surat ini sudah dikonversi menjadi Surat Keluar.');
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get allowed target roles for disposisi (borrowed from SuratKeluarController@create)
        $userRole = $user->role;
        $userId = $user->id;

        $allowedTargetRoles = \App\Models\DisposisiAssignment::where('source_user_id', $userId)
            ->pluck('target_role')
            ->toArray();
        
        if (empty($allowedTargetRoles)) {
            $allowedTargetRoles = \App\Models\DisposisiAssignment::where('source_role', $userRole)
                ->pluck('target_role')
                ->toArray();
        }

        $users = User::with('organizationUnit')
            ->where('status_akun', 'aktif')
            ->where('id', '!=', auth()->id())
            ->whereIn('role', $allowedTargetRoles)
            ->get();

        $perusahaans = Perusahaan::where('status', 'aktif')
            ->orderBy('nama_perusahaan')
            ->get();

        $userPerusahaan = $suratUnit->perusahaan;

        return view('pages.surat_unit_manager.convert', compact('suratUnit', 'users', 'perusahaans', 'userPerusahaan'));
    }

    public function storeConversion(Request $request, $id)
    {
        // Copying validation rules from SuratKeluarController@store
        $activeCodes = Perusahaan::where('status', 'aktif')
            ->pluck('kode')
            ->toArray();
        
        if (!in_array('RSAZRA', $activeCodes)) {
            $activeCodes[] = 'RSAZRA';
        }

        $validator = Validator::make($request->all(), [
            'nomor_surat' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (strpos($value, '-') !== false) {
                        return true;
                    }
                    $exists = \App\Models\SuratKeluar::where('nomor_surat', $value)->exists();
                    if ($exists) {
                        $fail('Nomor surat sudah digunakan. Gunakan tanda strip (-) jika ingin menggunakan nomor yang sama.');
                    }
                }
            ],
            'tanggal_surat' => 'required|date',
            'perihal' => 'required',
            'jenis_surat' => 'required|in:internal,eksternal',
            'sifat_surat' => 'required|in:normal,urgent',
            'file' => 'nullable|array',
            'file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png',
            'perusahaan' => 'required|in:' . implode(',', $activeCodes)
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        try {
            $user = auth()->user();
            $suratKeluar = $this->conversionService->convert($id, $request->all(), $user);

            return redirect()->route('suratkeluar.index')
                ->with('success', 'Surat Unit berhasil diajukan dan dikonversi menjadi Surat Keluar resmi.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengkonversi surat: ' . $e->getMessage());
        }
    }
}

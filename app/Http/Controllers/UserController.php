<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::where('status', 'aktif')->get();
        return view('pages.super_admin.manageuser', compact('jabatan'));
    }

    public function getUsers()
    {
        $users = User::with('jabatan')
            ->select('id', 'name', 'username', 'email', 'role', 'jabatan_id', 'status_akun', 'foto_profile', 'created_at')
            ->get()
            ->map(function ($user) {
                $user->foto_url = $user->foto_url; // Ensure foto_url is included
                return $user;
            });
        
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:3',
            'role' => 'required|integer|in:0,1,2,3',
            'jabatan_id' => 'required|exists:tbl_jabatan,id',
            'status_akun' => 'required|in:aktif,nonaktif'
        ]);

        try {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);

            User::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:3',
            'role' => 'required|integer|in:0,1,2,3',
            'jabatan_id' => 'required|exists:tbl_jabatan,id',
            'status_akun' => 'required|in:aktif,nonaktif'
        ]);

        try {
            $data = $request->except('password');
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(User $user)
    {
        try {
            $user->update([
                'status_akun' => $user->status_akun === 'aktif' ? 'nonaktif' : 'aktif'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status user berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah status user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->foto_profile) {
                Storage::delete('public/' . $user->foto_profile);
            }
            
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        $user = Auth::user()->load('jabatan');
        return view('pages.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if ($request->hasFile('foto_profile')) {
            $request->validate([
                'foto_profile' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            try {
                // Hapus foto lama jika ada
                if ($user->foto_profile) {
                    $oldPath = public_path('uploads/' . $user->foto_profile);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Upload foto baru
                $file = $request->file('foto_profile');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $fileName);
                
                $user->update(['foto_profile' => $fileName]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Foto profile berhasil diperbarui'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengupload foto: ' . $e->getMessage()
                ], 500);
            }
        }

        // Jika request untuk update profile (bukan foto)
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,'.$user->id,
        ]);

        try {
            $user->update($request->only(['name', 'username', 'email']));

            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar user untuk disposisi
     */
    public function getUsersForDisposisi()
    {
        try {
            $users = User::select('id', 'name', 'username', 'email', 'jabatan', 'role')
                         ->where('role', '!=', 3) // Exclude super admin
                         ->orderBy('name')
                         ->get();
            
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getForDisposisi()
    {
        try {
            // Ambil semua user kecuali super admin (role 0) dan user yang login saat ini
            $users = User::where('role', '!=', 0)
                         ->where('id', '!=', auth()->id())
                         ->select('id', 'name', 'username')
                         ->get();
            
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

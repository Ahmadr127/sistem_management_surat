<?php

namespace App\Http\Controllers;

use App\Models\OrganizationUnit;
use App\Models\OrganizationType;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizationUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizationUnit::with(['type', 'parent', 'head']);

        // Filters
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(code) LIKE ?', ["%{$search}%"]);
            });
        }

        if ($request->has('type_id') && $request->type_id != '') {
            $query->where('type_id', $request->type_id);
        }

        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        $units = $query->orderBy('name')->paginate(15);
        $types = OrganizationType::orderBy('level')->get();

        return view('organization-units.index', compact('units', 'types'));
    }

    public function create()
    {
        $types = OrganizationType::orderBy('level')->get();
        $parentUnits = OrganizationUnit::active()->orderBy('name')->get();
        $users = User::where('status_akun', 'aktif')->orderBy('name')->get();

        return view('organization-units.create', compact('types', 'parentUnits', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organization_units,code',
            'type_id' => 'required|exists:organization_types,id',
            'parent_id' => 'nullable|exists:organization_units,id',
            'head_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        OrganizationUnit::create($validated);

        return redirect()->route('organization-units.index')
            ->with('success', 'Unit organisasi berhasil ditambahkan!');
    }

    public function show(OrganizationUnit $organizationUnit)
    {
        $organizationUnit->load(['type', 'parent', 'head', 'children', 'members.roleModel']);
        $allUsers = User::where('status_akun', 'aktif')->orderBy('name')->get();
        $availableUsers = User::where('status_akun', 'aktif')
            ->where(function($q) use ($organizationUnit) {
                $q->whereNull('organization_unit_id')
                  ->orWhere('organization_unit_id', '!=', $organizationUnit->id);
            })
            ->orderBy('name')
            ->get();

        return view('organization-units.show', compact('organizationUnit', 'allUsers', 'availableUsers'));
    }

    public function edit(OrganizationUnit $organizationUnit)
    {
        $types = OrganizationType::orderBy('level')->get();
        $parentUnits = OrganizationUnit::active()
            ->where('id', '!=', $organizationUnit->id)
            ->orderBy('name')
            ->get();
        $users = User::where('status_akun', 'aktif')->orderBy('name')->get();

        return view('organization-units.edit', compact('organizationUnit', 'types', 'parentUnits', 'users'));
    }

    public function update(Request $request, OrganizationUnit $organizationUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organization_units,code,' . $organizationUnit->id,
            'type_id' => 'required|exists:organization_types,id',
            'parent_id' => 'nullable|exists:organization_units,id',
            'head_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $organizationUnit->update($validated);

        return redirect()->route('organization-units.index')
            ->with('success', 'Unit organisasi berhasil diupdate!');
    }

    public function destroy(OrganizationUnit $organizationUnit)
    {
        try {
            if ($organizationUnit->children()->count() > 0) {
                return redirect()->route('organization-units.index')
                    ->with('error', 'Tidak dapat menghapus unit yang masih memiliki sub-unit!');
            }

            if ($organizationUnit->members()->count() > 0) {
                return redirect()->route('organization-units.index')
                    ->with('error', 'Tidak dapat menghapus unit yang masih memiliki anggota!');
            }

            $organizationUnit->delete();
            return redirect()->route('organization-units.index')
                ->with('success', 'Unit organisasi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('organization-units.index')
                ->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }

    // Additional methods for member management
    public function updateHead(Request $request, OrganizationUnit $organizationUnit)
    {
        $validated = $request->validate([
            'head_id' => 'nullable|exists:users,id',
        ]);

        $organizationUnit->update($validated);

        return redirect()->route('organization-units.show', $organizationUnit)
            ->with('success', 'Kepala unit berhasil diupdate!');
    }

    public function addMember(Request $request, OrganizationUnit $organizationUnit)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->organization_unit_id = $organizationUnit->id;
        $user->save();

        return redirect()->route('organization-units.show', $organizationUnit)
            ->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function removeMember(OrganizationUnit $organizationUnit, User $user)
    {
        if ($user->id === $organizationUnit->head_id) {
            return redirect()->route('organization-units.show', $organizationUnit)
                ->with('error', 'Tidak dapat menghapus kepala unit dari anggota!');
        }

        $user->organization_unit_id = null;
        $user->save();

        return redirect()->route('organization-units.show', $organizationUnit)
            ->with('success', 'Anggota berhasil dihapus!');
    }

    /**
     * API: Get active units for dropdowns
     */
    public function getUnits(Request $request)
    {
        $units = OrganizationUnit::active()
            ->orderBy('name')
            ->select('id', 'name', 'code', 'type_id')
            ->with('type:id,name')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $units
        ]);
    }
}

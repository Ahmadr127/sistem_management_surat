<?php

namespace App\Http\Controllers;

use App\Models\OrganizationType;
use Illuminate\Http\Request;

class OrganizationTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizationType::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        $types = $query->orderBy('level')->paginate(15);

        return view('organization-types.index', compact('types'));
    }

    public function create()
    {
        return view('organization-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organization_types,name',
            'display_name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        OrganizationType::create($validated);

        return redirect()->route('organization-types.index')
            ->with('success', 'Tipe organisasi berhasil ditambahkan!');
    }

    public function edit(OrganizationType $organizationType)
    {
        return view('organization-types.edit', compact('organizationType'));
    }

    public function update(Request $request, OrganizationType $organizationType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organization_types,name,' . $organizationType->id,
            'display_name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $organizationType->update($validated);

        return redirect()->route('organization-types.index')
            ->with('success', 'Tipe organisasi berhasil diupdate!');
    }

    public function destroy(OrganizationType $organizationType)
    {
        try {
            if ($organizationType->organizationUnits()->count() > 0) {
                return redirect()->route('organization-types.index')
                    ->with('error', 'Tidak dapat menghapus tipe yang masih memiliki unit!');
            }

            $organizationType->delete();
            return redirect()->route('organization-types.index')
                ->with('success', 'Tipe organisasi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('organization-types.index')
                ->with('error', 'Gagal menghapus tipe: ' . $e->getMessage());
        }
    }
}

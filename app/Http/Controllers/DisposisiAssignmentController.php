<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposisiAssignment;

class DisposisiAssignmentController extends Controller
{
    public function index()
    {
        $assignments = DisposisiAssignment::all()->groupBy(function ($item) {
            return $item->source_user_id ? 'user_' . $item->source_user_id : 'role_' . $item->source_role;
        });

        return view('pages.super_admin.tujuandisposisi.index', compact('assignments'));
    }

    public function create()
    {
        $roles = DisposisiAssignment::getRoles();
        $users = \App\Models\User::where('status_akun', 'aktif')
                    ->orderBy('name')
                    ->get()
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name . ' - ' . ($user->jabatan->nama_jabatan ?? $user->role_name ?? 'User')
                        ];
                    });
        
        $selectedTargets = [];
        return view('pages.super_admin.tujuandisposisi.create', compact('roles', 'users', 'selectedTargets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_type' => 'required|in:role,user',
            'source_role' => 'required_if:source_type,role|nullable|integer',
            'source_user_id' => 'required_if:source_type,user|nullable|integer',
            'target_roles' => 'required|array',
            'target_roles.*' => 'integer',
        ]);

        $sourceRole = $request->source_type === 'role' ? $request->source_role : null;
        $sourceUserId = $request->source_type === 'user' ? $request->source_user_id : null;

        // Check duplicates
        $exists = false;
        if ($sourceRole) {
            $exists = DisposisiAssignment::where('source_role', $sourceRole)->exists();
        } elseif ($sourceUserId) {
            $exists = DisposisiAssignment::where('source_user_id', $sourceUserId)->exists();
        }

        if ($exists) {
            return redirect()->back()->with('error', 'Rule for this source already exists. Please edit instead.');
        }

        foreach ($request->target_roles as $targetRole) {
            DisposisiAssignment::create([
                'source_role' => $sourceRole,
                'source_user_id' => $sourceUserId,
                'target_role' => $targetRole,
            ]);
        }

        return redirect()->route('disposisi-assignments.index')
            ->with('success', 'Disposisi rules created successfully.');
    }

    public function edit($key)
    {
        $roles = DisposisiAssignment::getRoles();
        $users = \App\Models\User::where('status_akun', 'aktif')
                    ->orderBy('name')
                    ->get()
                    ->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name . ' - ' . ($user->jabatan->nama_jabatan ?? $user->role_name ?? 'User')
                        ];
                    });

        // Parse key (role_X or user_Y)
        $isUser = str_starts_with($key, 'user_');
        $id = (int) substr($key, strpos($key, '_') + 1);

        $query = DisposisiAssignment::query();
        if ($isUser) {
            $query->where('source_user_id', $id);
        } else {
            $query->where('source_role', $id);
        }
        
        $assignments = $query->get();
        
        if ($assignments->isEmpty()) {
            return redirect()->route('disposisi-assignments.index')->with('error', 'Rule not found.');
        }

        $disposisiAssignment = new DisposisiAssignment();
        if ($isUser) {
            $disposisiAssignment->source_user_id = $id;
            $disposisiAssignment->source_type = 'user';
        } else {
            $disposisiAssignment->source_role = $id;
            $disposisiAssignment->source_type = 'role';
        }
        
        $selectedTargets = $assignments->pluck('target_role')->toArray();

        return view('pages.super_admin.tujuandisposisi.edit', compact('disposisiAssignment', 'roles', 'users', 'selectedTargets', 'key'));
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'source_type' => 'required|in:role,user',
            'source_role' => 'required_if:source_type,role|nullable|integer',
            'source_user_id' => 'required_if:source_type,user|nullable|integer',
            'target_roles' => 'required|array',
            'target_roles.*' => 'integer',
        ]);

        // 1. Determine New Source
        $newSourceRole = $request->source_type === 'role' ? $request->source_role : null;
        $newSourceUserId = $request->source_type === 'user' ? $request->source_user_id : null;
        
        // 2. Parse Old Source Key
        $isUserOld = str_starts_with($key, 'user_');
        $oldId = (int) substr($key, strpos($key, '_') + 1);
        $oldSourceRole = !$isUserOld ? $oldId : null;
        $oldSourceUserId = $isUserOld ? $oldId : null;

        // 3. Check if Source Changed
        $sourceChanged = false;
        if ($isUserOld && $request->source_type === 'role') $sourceChanged = true;
        elseif (!$isUserOld && $request->source_type === 'user') $sourceChanged = true;
        elseif ($isUserOld && $request->source_type === 'user' && $oldId != $newSourceUserId) $sourceChanged = true;
        elseif (!$isUserOld && $request->source_type === 'role' && $oldId != $newSourceRole) $sourceChanged = true;

        // 4. If Changed, Check Conflict
        if ($sourceChanged) {
            $exists = false;
            if ($newSourceRole !== null) {
                $exists = DisposisiAssignment::where('source_role', $newSourceRole)->exists();
            } elseif ($newSourceUserId !== null) {
                $exists = DisposisiAssignment::where('source_user_id', $newSourceUserId)->exists();
            }

            if ($exists) {
                return redirect()->back()->with('error', 'Rule for this new source already exists. Please delete the old one or edit the existing new source.');
            }
        }

        // 5. Delete Old Rules
        if ($isUserOld) {
            DisposisiAssignment::where('source_user_id', $oldId)->delete();
        } else {
            DisposisiAssignment::where('source_role', $oldId)->delete();
        }

        // 6. Create New Rules
        foreach ($request->target_roles as $targetRole) {
            DisposisiAssignment::create([
                'source_role' => $newSourceRole,
                'source_user_id' => $newSourceUserId,
                'target_role' => $targetRole,
            ]);
        }

        return redirect()->route('disposisi-assignments.index')
            ->with('success', 'Disposisi rules updated successfully.');
    }

    public function destroy($key)
    {
        $isUser = str_starts_with($key, 'user_');
        $id = (int) substr($key, strpos($key, '_') + 1);

        if ($isUser) {
            DisposisiAssignment::where('source_user_id', $id)->delete();
        } else {
            DisposisiAssignment::where('source_role', $id)->delete();
        }

        return redirect()->route('disposisi-assignments.index')
            ->with('success', 'Disposisi rules deleted successfully.');
    }
}

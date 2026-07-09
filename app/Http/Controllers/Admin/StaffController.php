<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members.
     */
    public function index(Request $request)
    {
        $query = Staff::with('role');

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Paginate results (10 per page)
        $staffs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        // Fetch only administrative roles for the creation/editing forms and role filters
        $roles = Role::whereIn('name', ['admin', 'staff'])->get();

        return view('admin.staff.index', compact('staffs', 'roles'));
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
        ]);

        $newStaff = Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);
        ActivityLogger::log('create', "Created staff account for '{$newStaff->name}' (Role ID: {$newStaff->role_id}, ID: {$newStaff->id}).");

        return redirect()->route('admin.staff.index')->with('success', 'Staff account created successfully.');
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ];

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);
        ActivityLogger::log('update', "Updated staff account details for '{$staff->name}' (ID: {$staff->id}).");

        return redirect()->route('admin.staff.index')->with('success', 'Staff account updated successfully.');
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        
        $currentUserId = auth()->guard('staff')->id();

        // Self-deletion check to prevent locking out the admin
        if ($staff->id === $currentUserId) {
            return redirect()->route('admin.staff.index')->with('error', 'You cannot delete your own logged-in administrator account.');
        }

        $staffName = $staff->name;
        $staffId = $staff->id;
        $staff->delete();
        ActivityLogger::log('delete', "Deleted staff account '{$staffName}' (ID: {$staffId}).");

        return redirect()->route('admin.staff.index')->with('success', 'Staff account deleted successfully.');
    }
}

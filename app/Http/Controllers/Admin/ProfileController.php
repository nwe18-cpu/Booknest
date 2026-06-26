<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the staff profile edit form.
     */
    public function show()
    {
        $staff = Auth::guard('staff')->user();
        return view('admin.profile.index', compact('staff'));
    }

    /**
     * Update the staff profile details.
     */
    public function update(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:staff,email,' . $staff->id,
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address has already been taken by another staff.',
            'phone.required' => 'Phone number is required.',
            'image.max' => 'Profile picture size must not exceed 2MB.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Handle password update if requested
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $staff->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
            $staff->password = Hash::make($request->new_password);
        }

        // Handle avatar upload
        if ($request->hasFile('image')) {
            // Delete old avatar if exists
            if ($staff->image && Storage::disk('public')->exists($staff->image)) {
                Storage::disk('public')->delete($staff->image);
            }

            $path = $request->file('image')->store('staff/avatars', 'public');
            $staff->image = $path;
        }

        // Update other fields
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        
        $staff->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }
}

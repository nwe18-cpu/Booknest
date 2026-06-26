<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    /**
     * Show the login/registration form.
     */
    public function showAuthForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle customer login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $customer = Auth::guard('customer')->user();
            
            if ($customer->status !== 'active') {
                Auth::guard('customer')->logout();
                return back()->withErrors([
                    'email' => 'Your account is currently inactive.',
                ])->withInput($request->only('email'))->with('form_type', 'login');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('customer.store.home'))->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'))->with('form_type', 'login');
    }

    /**
     * Handle customer registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'reg_name' => 'required|string|max:255',
            'reg_email' => 'required|string|email|max:255|unique:customers,email',
            'reg_phone' => ['required', 'string', 'max:20', 'regex:/^(09|\+959|959)[0-9]{7,9}$/'],
            'reg_password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9])/'],
        ], [
            'reg_name.required' => 'The full name field is required.',
            'reg_email.required' => 'The email address field is required.',
            'reg_email.email' => 'The email address must be a valid email address.',
            'reg_email.unique' => 'The email address has already been taken.',
            'reg_phone.required' => 'The phone number field is required.',
            'reg_phone.regex' => 'The phone number format is invalid. It must be a valid Myanmar phone number (e.g. 09xxxxxxxxx).',
            'reg_password.required' => 'The password field is required.',
            'reg_password.min' => 'The password must be at least 8 characters.',
            'reg_password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        // Dynamically find or create the customer role to prevent DB constraint errors
        $role = Role::firstOrCreate(['name' => 'customer']);

        $customer = Customer::create([
            'role_id' => $role->id,
            'name' => $validated['reg_name'],
            'email' => $validated['reg_email'],
            'phone' => $validated['reg_phone'],
            'password' => Hash::make($validated['reg_password']),
            'status' => 'active',
        ]);

        // Create welcome notification
        \App\Models\Notification::create([
            'customer_id' => $customer->id,
            'title' => 'Welcome to Booknest!',
            'message' => 'Thank you for registering with Booknest online bookstore.',
            'is_read' => false,
        ]);

        // Auto-login the new customer
        Auth::guard('customer')->login($customer);

        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Log the customer out.
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

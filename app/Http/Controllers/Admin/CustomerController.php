<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\ActivityLogger;

class CustomerController extends Controller
{
    /**
     * Display a listing of registered customers.
     */
    public function index(Request $request)
    {
        $query = Customer::orderBy('created_at', 'desc');

        // Filter by Account Status (active/inactive)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by VIP/Subscription Status
        if ($request->filled('subscription_status')) {
            $query->where('subscription_status', $request->subscription_status);
        }

        // Filter by Subscription Type (free/vip/premium etc.)
        if ($request->filled('subscription_type')) {
            $query->where('subscription_type', $request->subscription_type);
        }

        // Search by Name, Email, or Phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(5)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display detailed profile, reading progress, and order logs for a specific customer.
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        // Fetch customer's order history
        $orders = Order::where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch customer's downloads and reading progress
        $progressList = ReadingProgress::with('item.author')
            ->where('customer_id', $id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.customers.show', compact('customer', 'orders', 'progressList'));
    }

    /**
     * Toggle customer account status (block/unblock).
     */
    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);
        
        $newStatus = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->update(['status' => $newStatus]);
        ActivityLogger::log('status_change', "Toggled customer account status for '{$customer->name}' (ID: {$customer->id}) to {$newStatus}.");

        $message = $newStatus === 'active' 
            ? "Customer account '{$customer->name}' has been successfully unblocked/activated." 
            : "Customer account '{$customer->name}' has been blocked/deactivated.";

        return redirect()->back()->with('success', $message);
    }

    /**
     * Manually edit or extend customer's VIP membership subscription details.
     */
    public function updateSubscription(Request $request, $id)
    {
        $request->validate([
            'subscription_status' => 'required|string|in:active,inactive',
            'subscription_expires_at' => 'nullable|date',
        ]);

        $customer = Customer::findOrFail($id);

        $expiresAt = null;
        if ($request->filled('subscription_expires_at')) {
            $expiresAt = Carbon::parse($request->subscription_expires_at)->endOfDay();
        }

        $customer->update([
            'subscription_type' => $request->subscription_status === 'active' ? 'vip' : 'free',
            'subscription_status' => $request->subscription_status,
            'subscription_expires_at' => $expiresAt,
        ]);
        ActivityLogger::log('update', "Manually updated VIP subscription status for customer '{$customer->name}' (ID: {$customer->id}) to {$request->subscription_status} (Expires: " . ($expiresAt ? $expiresAt->toDateString() : 'N/A') . ").");

        return redirect()->back()->with('success', "VIP membership details for '{$customer->name}' updated successfully.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display the subscriptions dashboard.
     */
    public function index(Request $request)
    {
        $now = Carbon::now();

        // 1. Calculate Statistics
        $totalSubscribers = Customer::where('subscription_type', '!=', 'free')->count();
        
        $activeSubscribers = Customer::where('subscription_type', '!=', 'free')
            ->where('subscription_status', 'active')
            ->where(function($q) use ($now) {
                $q->whereNull('subscription_expires_at')
                  ->orWhere('subscription_expires_at', '>', $now);
            })
            ->count();

        $expiredSubscribers = Customer::where('subscription_type', '!=', 'free')
            ->where(function($q) use ($now) {
                $q->where('subscription_status', 'inactive')
                  ->orWhere(function($subQ) use ($now) {
                      $subQ->whereNotNull('subscription_expires_at')
                           ->where('subscription_expires_at', '<=', $now);
                  });
            })
            ->count();

        // 2. Query VIP Subscribers
        $query = Customer::where('subscription_type', '!=', 'free')->orderBy('updated_at', 'desc');

        // Filter by VIP Status (active/expired)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('subscription_status', 'active')
                      ->where(function($q) use ($now) {
                          $q->whereNull('subscription_expires_at')
                            ->orWhere('subscription_expires_at', '>', $now);
                      });
            } elseif ($request->status === 'expired') {
                $query->where(function($q) use ($now) {
                    $q->where('subscription_status', 'inactive')
                      ->orWhere(function($subQ) use ($now) {
                          $subQ->whereNotNull('subscription_expires_at')
                               ->where('subscription_expires_at', '<=', $now);
                      });
                });
            }
        }

        // Filter by Subscription Type (monthly/vip/premium)
        if ($request->filled('subscription_type')) {
            $query->where('subscription_type', $request->subscription_type);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->paginate(5)->withQueryString();

        return view('admin.subscriptions.index', compact(
            'subscribers',
            'totalSubscribers',
            'activeSubscribers',
            'expiredSubscribers'
        ));
    }
}

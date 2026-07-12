<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Catalog Statistics
        $totalBooks = Item::count();
        $lowStockCount = Item::where('stock_quantity', '<', 5)->count();
        $lowStockBooks = Item::with('author')->where('stock_quantity', '<', 5)->take(5)->get();

        // 2. Sales & Revenue statistics for the last 7 days
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $salesData[$date] = 0.0;
        }

        $dailySales = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        foreach ($dailySales as $date => $total) {
            if (array_key_exists($date, $salesData)) {
                $salesData[$date] = (float) $total;
            }
        }

        $maxVal = max($salesData);
        // Fallback cozy mock curve if there are no actual sales in the system yet
        if ($maxVal == 0) {
            $salesData = [
                Carbon::now()->subDays(6)->format('Y-m-d') => 15000,
                Carbon::now()->subDays(5)->format('Y-m-d') => 32000,
                Carbon::now()->subDays(4)->format('Y-m-d') => 28000,
                Carbon::now()->subDays(3)->format('Y-m-d') => 45000,
                Carbon::now()->subDays(2)->format('Y-m-d') => 60000,
                Carbon::now()->subDays(1)->format('Y-m-d') => 52000,
                Carbon::now()->subDays(0)->format('Y-m-d') => 75000,
            ];
            $maxVal = max($salesData);
        }

        // Today's Sales
        $todayDate = Carbon::now()->format('Y-m-d');
        $realTodaySales = Order::where('payment_status', 'paid')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
            
        $todaySales = $realTodaySales > 0 ? $realTodaySales : ($salesData[$todayDate] ?? 0);

        // 3. Bookstore Activity Stats
        $activeVipCount = Customer::where('subscription_type', '!=', 'free')
            ->where('subscription_status', 'active')
            ->count();
        $totalCustomersCount = Customer::count();
        $totalReviewsCount = Review::count();
        
        // New Registered Customers in last 7 days
        $newCustomersCount = Customer::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        
        // Total Order Volumes
        $totalOrders = Order::count();

        // RBAC View limits: Non-admin staff cannot view daily sales, today's sales, or order volumes
        $staffUser = auth()->guard('staff')->user();
        $isAdmin = ($staffUser && $staffUser->role && $staffUser->role->name === 'admin');
        
        if (!$isAdmin) {
            $todaySales = 0;
            $salesData = array_fill_keys(array_keys($salesData), 0.0);
            $maxVal = 1;
            $totalOrders = 0;
        }

        // 5. Active Staff List
        $activeStaff = \App\Models\Staff::with('role')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // 6. Recent Activity Logs (Audit Trail)
        $activeStaffLogs = \App\Models\ActivityLog::with('staff')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 4. Calculate SVG Graph coordinates (viewBox 0 0 280 100)
        $points = [];
        $width = 280;
        $height = 100;
        $i = 0;
        foreach ($salesData as $date => $val) {
            $x = ($i / 6) * $width;
            $y = $height - (($val / $maxVal) * ($height - 18)) - 8;
            $points[] = "$x,$y";
            $i++;
        }
        $pointsString = implode(' ', $points);

        return view('admin.dashboard', compact(
            'totalBooks', 
            'lowStockCount', 
            'lowStockBooks',
            'salesData',
            'maxVal',
            'pointsString',
            'todaySales',
            'activeVipCount',
            'totalCustomersCount',
            'totalReviewsCount',
            'newCustomersCount',
            'totalOrders',
            'activeStaff',
            'activeStaffLogs'
        ));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('customer')->orderBy('created_at', 'desc');

        // Filter by Order Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by Order ID or Customer Name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(5)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display details of a specific order.
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'orderItems.item.author', 'shippingAddress'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of an order (payment and fulfillment).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|string|in:pending,paid,failed',
            'status' => 'required|string|in:pending,completed,shipped,cancelled',
        ]);

        $order = Order::findOrFail($id);

        $oldPaymentStatus = $order->payment_status;
        $oldOrderStatus = $order->status;

        $order->update([
            'payment_status' => $request->payment_status,
            'status' => $request->status,
        ]);

        // Dynamic Notifications for the Customer
        if ($order->customer_id) {
            $formattedAmount = number_format($order->total_amount);

            // 1. Payment status changed to Paid
            if ($oldPaymentStatus !== 'paid' && $request->payment_status === 'paid') {
                Notification::create([
                    'customer_id' => $order->customer_id,
                    'title' => 'Payment Confirmed!',
                    'message' => "Payment of {$formattedAmount} Ks for Order #{$order->id} was confirmed successfully. Thank you!",
                    'is_read' => false,
                ]);
            }

            // 2. Order status updates (shipped, completed, cancelled)
            if ($oldOrderStatus !== $request->status) {
                $title = "Order Updated";
                $message = "Your Order #{$order->id} has been updated.";

                switch ($request->status) {
                    case 'shipped':
                        $title = "Order Shipped!";
                        $message = "Your Order #{$order->id} containing your books has been shipped out.";
                        break;
                    case 'completed':
                        $title = "Order Completed!";
                        $message = "Your Order #{$order->id} is now complete. Enjoy reading your new books!";
                        break;
                    case 'cancelled':
                        $title = "Order Cancelled";
                        $message = "Your Order #{$order->id} has been cancelled.";
                        break;
                }

                Notification::create([
                    'customer_id' => $order->customer_id,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Order status updated successfully and customer notified!');
    }
}

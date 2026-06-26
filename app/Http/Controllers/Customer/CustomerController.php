<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ReadingProgress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    /**
     * Show the customer dashboard showing downloaded books on the 3D bookshelf.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        
        // Fetch classifications
        $classifications = \App\Models\Classification::where('status', 'active')->get();
        
        // Fetch only books that the customer has downloaded
        $downloadedBookIds = \App\Models\CustomerDownload::where('customer_id', $customer->id)
            ->pluck('item_id')
            ->toArray();

        $books = Item::with(['author', 'classifications'])
            ->whereIn('id', $downloadedBookIds)
            ->where('status', 'active')
            ->get();

        // Fetch reading progress records for downloaded books
        $progress = ReadingProgress::where('customer_id', $customer->id)
            ->whereIn('item_id', $downloadedBookIds)
            ->get()
            ->keyBy('item_id');

        // Calculate Statistics
        $totalDownloaded = $books->count();
        $myProgressList = ReadingProgress::where('customer_id', $customer->id)
            ->whereIn('item_id', $downloadedBookIds)
            ->get();
        
        $completedCount = $myProgressList->where('completed', true)->count();
        $inProgressCount = $myProgressList->where('completed', false)->count();
        
        // Calculate average progress percentage
        $avgProgress = 0;
        if ($myProgressList->count() > 0) {
            $totalPct = 0;
            foreach ($myProgressList as $itemProgress) {
                $book = $books->firstWhere('id', $itemProgress->item_id);
                if ($book && $book->pages > 0) {
                    $totalPct += min(100, round(($itemProgress->current_page / $book->pages) * 100));
                }
            }
            $avgProgress = round($totalPct / $myProgressList->count());
        }

        // Get the last read book
        $lastReadProgress = ReadingProgress::where('customer_id', $customer->id)
            ->whereIn('item_id', $downloadedBookIds)
            ->orderBy('updated_at', 'desc')
            ->first();
        
        $lastReadBook = null;
        if ($lastReadProgress) {
            $lastReadBook = $books->firstWhere('id', $lastReadProgress->item_id);
        }

        $stats = [
            'total_books' => $totalDownloaded,
            'completed' => $completedCount,
            'reading' => $inProgressCount,
            'avg_progress' => $avgProgress,
            'last_read_book' => $lastReadBook,
            'last_read_progress' => $lastReadProgress,
        ];

        return view('customer.dashboard', compact('classifications', 'books', 'progress', 'stats'));
    }

    /**
     * Download book PDF file and track in customer_downloads table.
     */
    public function downloadPdf($id)
    {
        $customer = Auth::guard('customer')->user();

        // Gate download: Only active subscription is allowed
        if (!$customer->hasActiveSubscription()) {
            return redirect()->route('customer.subscription.index')->with('error', 'Please subscribe to a membership plan to download and read books as PDF.');
        }

        $book = Item::findOrFail($id);

        if (!$book->pdf_file) {
            return redirect()->back()->with('error', 'This book does not have a PDF version available.');
        }

        // Record the download
        \App\Models\CustomerDownload::firstOrCreate([
            'customer_id' => $customer->id,
            'item_id' => $book->id,
        ]);

        // Get file path
        $filePath = storage_path('app/public/' . $book->pdf_file);
        if (!file_exists($filePath)) {
            $filePath = public_path('storage/' . $book->pdf_file);
        }

        if (file_exists($filePath)) {
            return response()->download($filePath, $book->name . '.pdf');
        }

        return redirect()->back()->with('error', 'PDF file not found on server.');
    }

    /**
     * Stream book PDF file inline without triggering download behavior.
     */
    public function streamPdf($id)
    {
        $customer = Auth::guard('customer')->user();

        // Gate access: Only active subscription is allowed
        if (!$customer->hasActiveSubscription()) {
            abort(403, 'Please subscribe to a membership plan to read books.');
        }

        $book = Item::findOrFail($id);

        if (!$book->pdf_file) {
            abort(404, 'This book does not have a PDF version available.');
        }

        // Get file path
        $filePath = storage_path('app/public/' . $book->pdf_file);
        if (!file_exists($filePath)) {
            $filePath = public_path('storage/' . $book->pdf_file);
        }

        if (file_exists($filePath)) {
            // Serve raw file contents directly to bypass Symfony's automatic Content-Disposition header
            return response(file_get_contents($filePath), 200, [
                'Content-Type' => 'application/octet-stream',
            ]);
        }

        abort(404, 'PDF file not found on server.');
    }

    /**
     * Save customer's reading progress via AJAX.
     */
    public function saveProgress(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'current_page' => 'required|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();
        $book = Item::findOrFail($request->item_id);

        $currentPage = min($request->current_page, $book->pages);
        $completed = ($currentPage >= $book->pages);

        $progress = ReadingProgress::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'item_id' => $book->id,
            ],
            [
                'current_page' => $currentPage,
                'completed' => $completed,
            ]
        );

        $progressPercent = min(100, round(($currentPage / $book->pages) * 100));

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully.',
            'current_page' => $currentPage,
            'completed' => $completed,
            'progress_percent' => $progressPercent,
        ]);
    }

    /**
     * Toggle bookmark on customer's reading progress.
     */
    public function toggleBookmark(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'page' => 'required|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();
        $book = Item::findOrFail($request->item_id);
        $page = min($request->page, $book->pages);

        $progress = ReadingProgress::where('customer_id', $customer->id)
            ->where('item_id', $book->id)
            ->first();

        if (!$progress) {
            $progress = ReadingProgress::create([
                'customer_id' => $customer->id,
                'item_id' => $book->id,
                'current_page' => 1,
                'completed' => false,
                'bookmarked_page' => $page,
            ]);
        } else {
            if ($progress->bookmarked_page == $page) {
                $progress->bookmarked_page = null;
            } else {
                $progress->bookmarked_page = $page;
            }
            $progress->save();
        }

        return response()->json([
            'success' => true,
            'message' => $progress->bookmarked_page ? 'Page ' . $page . ' bookmarked.' : 'Bookmark removed.',
            'bookmarked_page' => $progress->bookmarked_page,
        ]);
    }

    /**
     * Show Bookstore Home/Catalog page.
     */
    public function storeHome()
    {
        $classifications = \App\Models\Classification::where('status', 'active')->get();
        $books = Item::with(['author', 'classifications'])->where('status', 'active')->get();
        
        $now = now()->toDateString();
        $banners = \App\Models\Banner::where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->orderBy('order')
            ->get();

        return view('home', compact('classifications', 'books', 'banners'));
    }

    /**
     * Show dedicated Shopping Cart page.
     */
    public function cart()
    {
        return view('customer.store.cart');
    }

    /**
     * Get JSON data of current cart session.
     */
    public function getCartData()
    {
        $cart = session()->get('cart', []);
        
        $totalQuantity = 0;
        $totalAmount = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalAmount += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'items' => $cart,
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount
        ]);
    }

    /**
     * Add item to session cart.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $book = Item::findOrFail($request->item_id);

        if ($book->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock. Only {$book->stock_quantity} left."
            ], 422);
        }

        $cart = session()->get('cart', []);

        // Sequential cover classes sequential index helper
        $booksList = Item::where('status', 'active')->pluck('id')->toArray();
        $idx = array_search($book->id, $booksList);
        $colorClass = 'book-color-' . ((($idx !== false ? $idx : 0) % 4) + 1);

        if (isset($cart[$book->id])) {
            $newQty = $cart[$book->id]['quantity'] + $request->quantity;
            if ($book->stock_quantity < $newQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot add more. Limit of {$book->stock_quantity} reached."
                ], 422);
            }
            $cart[$book->id]['quantity'] = $newQty;
        } else {
            $cart[$book->id] = [
                'name' => $book->name,
                'author' => $book->author?->name ?? 'Unknown Author',
                'price' => $book->price,
                'quantity' => $request->quantity,
                'cover_class' => $colorClass
            ];
        }

        session()->put('cart', $cart);

        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => "Added \"{$book->name}\" to cart successfully.",
            'total_quantity' => $totalQuantity
        ]);
    }

    /**
     * Update cart item quantity in session.
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $book = Item::findOrFail($request->item_id);

        if ($book->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => "Only {$book->stock_quantity} books in stock."
            ], 422);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$request->item_id])) {
            $cart[$request->item_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart.'
        ], 404);
    }

    /**
     * Remove item from cart session.
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->item_id])) {
            $name = $cart[$request->item_id]['name'];
            unset($cart[$request->item_id]);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => "Removed \"{$name}\" from cart."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart.'
        ], 404);
    }

    /**
     * Show Checkout View page.
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.store.home')->with('error', 'Your shopping cart is empty.');
        }

        $totalQuantity = 0;
        $totalAmount = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalAmount += $item['price'] * $item['quantity'];
        }

        return view('customer.store.checkout', compact('cart', 'totalQuantity', 'totalAmount'));
    }

    /**
     * Process checkout form post.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'address_line' => 'required|string',
            'email' => 'required|email|max:255',
            'payment_method' => 'required|in:cod,stripe',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Your shopping cart is empty.'
            ], 422);
        }

        $customer = Auth::guard('customer')->user();

        try {
            DB::beginTransaction();

            // Calculate total amount and check stock
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($cart as $itemId => $details) {
                $book = Item::lockForUpdate()->find($itemId);
                if (!$book || $book->status !== 'active') {
                    throw new \Exception("Book \"{$details['name']}\" is no longer available.");
                }

                if ($book->stock_quantity < $details['quantity']) {
                    throw new \Exception("Insufficient stock for \"{$book->name}\". Only {$book->stock_quantity} left.");
                }

                $totalAmount += $book->price * $details['quantity'];
                
                $orderItemsData[] = [
                    'item_id' => $book->id,
                    'quantity' => $details['quantity'],
                    'price' => $book->price,
                    'book_model' => $book
                ];
            }

            // Create Order
            $order = Order::create([
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'status' => 'pending'
            ]);

            // Save order items and decrement stocks
            foreach ($orderItemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price']
                ]);

                // Decrement stock
                $itemData['book_model']->decrement('stock_quantity', $itemData['quantity']);
            }

            // Save Shipping Address
            ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $request->receiver_name,
                'phone_number' => $request->phone_number,
                'address_line' => $request->address_line,
                'email' => $request->email
            ]);

            DB::commit();

            // Create Order Placed Notification
            Notification::create([
                'customer_id' => $customer->id,
                'title' => 'Order Placed Successfully',
                'message' => 'Order ID #' . $order->id . ' has been received and will be processed soon.',
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        if ($request->payment_method === 'cod') {
            // Clear cart session immediately
            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'redirect_url' => route('customer.store.orders')
            ]);
        }

        // Handle Stripe payment checkout integration
        if ($request->payment_method === 'stripe') {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

                $lineItems = [];
                foreach ($cart as $itemId => $details) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $details['name'],
                                'description' => 'By ' . ($details['author'] ?? 'Unknown'),
                            ],
                            'unit_amount' => round($details['price'] * 100),
                        ],
                        'quantity' => $details['quantity'],
                    ];
                }

                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => route('customer.store.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('customer.store.checkout'),
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ]);

                $order->update(['stripe_session_id' => $session->id]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $session->url
                ]);
            } catch (\Exception $e) {
                // Restore stock and delete order since Stripe failed
                try {
                    DB::beginTransaction();
                    foreach ($order->orderItems as $orderItem) {
                        $book = Item::find($orderItem->item_id);
                        if ($book) {
                            $book->increment('stock_quantity', $orderItem->quantity);
                        }
                    }
                    $order->shippingAddress()->delete();
                    $order->orderItems()->delete();
                    $order->delete();
                    DB::commit();
                } catch (\Exception $rollbackEx) {
                    DB::rollBack();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Stripe Service Error: ' . $e->getMessage()
                ], 422);
            }
        }
    }

    /**
     * Handle Stripe Checkout payment success callback.
     */
    public function paymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('customer.store.home')->with('error', 'Invalid Session ID.');
        }

        $order = Order::where('stripe_session_id', $sessionId)->first();
        if (!$order) {
            return redirect()->route('customer.store.home')->with('error', 'Order not found.');
        }

        if ($order->payment_status !== 'paid') {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $session = \Stripe\Checkout\Session::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);

                    // Create Stripe Payment Success Notification
                    Notification::create([
                        'customer_id' => $order->customer_id,
                        'title' => 'Payment Successful',
                        'message' => 'Payment for Order ID #' . $order->id . ' via Stripe completed successfully.',
                        'is_read' => false,
                    ]);
                }
            } catch (\Exception $e) {
                // If retrieve fails, let it log or handle gracefully
            }
        }

        // Clear cart session
        session()->forget('cart');

        return view('customer.store.success', compact('order'));
    }

    /**
     * Show Customer's Order History Page.
     */
    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        
        $orders = Order::where('customer_id', $customer->id)
            ->with(['orderItems.item.author', 'shippingAddress'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.store.orders', compact('orders'));
    }

    /**
     * Show Subscription Plan Page.
     */
    public function subscriptionIndex()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.store.subscription', compact('customer'));
    }

    /**
     * Handle Subscription Checkout (KPay/WavePay selection).
     */
    public function subscriptionCheckout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:kpay,wave',
        ]);

        $customer = Auth::guard('customer')->user();

        // Update customer subscription status immediately for simulated manual flow
        $customer->update([
            'subscription_status' => 'active',
            'subscription_type' => 'monthly',
            'subscription_expires_at' => now()->addDays(30),
        ]);

        // Create Subscription Upgrade Notification
        Notification::create([
            'customer_id' => $customer->id,
            'title' => 'VIP Membership Upgraded',
            'message' => 'You have successfully upgraded to VIP Reader. You can now download and read books as PDF.',
            'is_read' => false,
        ]);

        // Redirect to success page, passing the payment method
        return redirect()->route('customer.subscription.success')->with([
            'success' => 'Subscription payment processed successfully!',
            'payment_method' => $request->payment_method
        ]);
    }

    /**
     * Show Subscription Purchase Success Page.
     */
    public function subscriptionSuccess()
    {
        $customer = Auth::guard('customer')->user();
        
        // Ensure user actually has an active subscription to access this success page
        if (!$customer->hasActiveSubscription()) {
            return redirect()->route('customer.subscription.index');
        }

        $paymentMethod = session('payment_method', 'kpay');

        return view('customer.store.subscription_success', compact('customer', 'paymentMethod'));
    }

    /**
     * Show Customer Profile Settings Page.
     */
    public function showProfile()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile', compact('customer'));
    }

    /**
     * Update Customer Profile Settings.
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => ['required', 'string', 'max:20', 'regex:/^(09|\+959|959)[0-9]{7,9}$/'],
            'gender' => 'nullable|string|in:male,female,other',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9])/'],
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address has already been taken.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'The phone number format is invalid. It must be a valid Myanmar phone number (e.g. 09xxxxxxxxx).',
            'image.max' => 'Profile picture size must not exceed 2MB.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.confirmed' => 'The password confirmation does not match.',
            'new_password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        // Handle password update if requested
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $customer->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
            $customer->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old avatar if exists
            if ($customer->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($customer->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($customer->image);
            }

            $path = $request->file('image')->store('avatars', 'public');
            $customer->image = $path;
        }

        // Update other fields
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->gender = $request->gender;
        $customer->dob = $request->dob ? \Illuminate\Support\Carbon::parse($request->dob)->format('Y-m-d') : null;
        $customer->address = $request->address;
        
        $customer->save();

        return redirect()->route('customer.profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Get customer notifications (limit to 6, delete older ones).
     */
    public function getNotifications()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        // Delete older notifications to keep only 6 latest
        $latestIds = $customer->notifications()->orderBy('created_at', 'desc')->take(6)->pluck('id')->toArray();
        if (!empty($latestIds)) {
            $customer->notifications()->whereNotIn('id', $latestIds)->delete();
        }

        $notifications = $customer->notifications()->orderBy('created_at', 'desc')->get();
        $unreadCount = $customer->notifications()->where('is_read', false)->count();

        // Format dates beautifully
        foreach ($notifications as $notification) {
            $notification->time_ago = $notification->created_at->diffForHumans();
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            $customer->notifications()->where('is_read', false)->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get reviews and rating breakdown for a book.
     */
    public function getBookReviews($id)
    {
        $book = Item::findOrFail($id);
        
        $reviews = \App\Models\Review::with(['customer'])
            ->where('item_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

        // Calculate rating breakdown distribution
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $review) {
            $distribution[$review->rating]++;
        }

        // Format dates and customer info
        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at_formatted' => $review->created_at->diffForHumans(),
                'customer_name' => $review->customer ? $review->customer->name : 'Unknown User',
                'customer_image' => $review->customer && $review->customer->image 
                    ? asset('storage/' . $review->customer->image) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($review->customer ? $review->customer->name : 'User') . '&background=f1e4d8&color=5c3a21&bold=true',
            ];
        });

        // Check if current user is logged in and has already reviewed this book
        $currentCustomerId = Auth::guard('customer')->id();
        $userReview = null;
        if ($currentCustomerId) {
            $existing = \App\Models\Review::where('customer_id', $currentCustomerId)
                ->where('item_id', $id)
                ->first();
            if ($existing) {
                $userReview = [
                    'rating' => $existing->rating,
                    'comment' => $existing->comment,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'reviews' => $formattedReviews,
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'distribution' => $distribution,
            'user_review' => $userReview,
            'is_logged_in' => (bool)$currentCustomerId,
        ]);
    }

    /**
     * Submit or update a book review.
     */
    public function submitBookReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $customerId = Auth::guard('customer')->id();
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $book = Item::findOrFail($id);

        // Sanitize comment to prevent XSS script injection
        $comment = $request->comment ? trim(strip_tags($request->comment)) : null;

        $review = \App\Models\Review::updateOrCreate(
            [
                'customer_id' => $customerId,
                'item_id' => $id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $comment,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!',
        ]);
    }

    /**
     * Toggle book wishlist status for current customer.
     */
    public function toggleWishlist($id)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $book = Item::findOrFail($id);
        $exists = $customer->wishlistBooks()->where('item_id', $book->id)->exists();

        if ($exists) {
            $customer->wishlistBooks()->detach($book->id);
            $status = 'removed';
            $message = 'Removed "' . $book->name . '" from your wishlist.';
        } else {
            $customer->wishlistBooks()->attach($book->id);
            $status = 'added';
            $message = 'Added "' . $book->name . '" to your wishlist.';
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a paginated list of orders with search and status filters.
     */
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->search, fn ($query, $search) => $query->search($search))
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the detail of a single order.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of an order.
     *
     * When an order is cancelled, the stock of its products is restored
     * (unless the order was already cancelled).
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $newStatus = OrderStatus::from($request->validated('status'));

        DB::transaction(function () use ($order, $newStatus): void {
            if ($newStatus === OrderStatus::Cancelled && $order->status !== $newStatus) {
                foreach ($order->items as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);
        });

        $message = "Status pesanan {$order->order_number} diperbarui.";

        if ($newStatus === OrderStatus::Cancelled) {
            $message .= ' Stok produk dikembalikan.';
        }

        return back()->with('success', $message);
    }
}

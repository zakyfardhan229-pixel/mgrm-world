<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the sales report with date and status filters.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'in:' . implode(',', OrderStatus::values())],
        ]);

        $orders = $this->filteredOrders($validated)
            ->withSum('items as total_items', 'quantity')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $totalOrders = (clone $this->filteredOrders($validated))->count();

        $successful = $this->filteredOrders($validated)
            ->where('status', '!=', OrderStatus::Cancelled->value);

        $revenue = (string) $successful->sum('total');

        $itemsSold = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('orders.created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('orders.created_at', '<=', $to))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('orders.status', $status))
            ->where('orders.status', '!=', OrderStatus::Cancelled->value)
            ->sum('order_items.quantity');

        return view('admin.reports.index', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'revenue' => number_format((float) $revenue, 2, '.', ''),
            'itemsSold' => $itemsSold,
            'filters' => $validated,
        ]);
    }

    /**
     * Base order query filtered by the given report filters.
     *
     * @param  array<string, mixed>  $filters
     */
    private function filteredOrders(array $filters): Builder
    {
        return Order::query()
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
    }
}
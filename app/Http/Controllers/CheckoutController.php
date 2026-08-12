<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\ProductQrCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page with a summary of the cart.
     */
    public function index(Request $request): View
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product')
            ->orderByDesc('id')
            ->get();

        $buyNowId = $request->session()->get('buy_now.product_id');

        if ($buyNowId !== null) {
            $buyNowItems = $cartItems->where('product_id', $buyNowId)->values();

            if ($buyNowItems->isEmpty()) {
                $request->session()->forget('buy_now');
            } else {
                $cartItems = $buyNowItems;
            }
        }

        $invalidItems = $cartItems->filter(function ($item) {
            $product = $item->product;

            return ! $product->is_active || $product->stock < $item->quantity;
        });

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'invalidItems' => $invalidItems,
            'total' => $this->cartTotal($cartItems),
            'buyNow' => $request->session()->has('buy_now'),
        ]);
    }

    /**
     * Place an order from the current user's cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:'.implode(',', PaymentMethod::values())],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $buyNowId = $request->session()->get('buy_now.product_id');

        $cartItems = $request->user()
            ->cartItems()
            ->with('product')
            ->when($buyNowId !== null, fn ($query) => $query->where('product_id', $buyNowId))
            ->get();

        if ($cartItems->isEmpty()) {
            $request->session()->forget('buy_now');

            return back()->with('error', 'Keranjang belanja masih kosong.');
        }

        $cartLines = $cartItems->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->values()->all();

        $order = $this->createOrder(
            $request->user(),
            $validated,
            OrderStatus::Pending,
            $validated['payment_method'],
            $cartLines,
            $buyNowId,
        );

        $request->session()->forget('buy_now');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
    }

    /**
     * Generate a QRIS QR code containing a signed URL to confirm payment.
     */
    public function qrisGenerate(Request $request): Response
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $buyNowId = $request->session()->get('buy_now.product_id');

        $cartItems = $request->user()
            ->cartItems()
            ->with('product')
            ->when($buyNowId !== null, fn ($query) => $query->where('product_id', $buyNowId))
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang belanja masih kosong.');
        }

        foreach ($cartItems as $item) {
            $product = $item->product;

            if (! $product->is_active) {
                throw ValidationException::withMessages(['cart' => 'Produk '.$product->name.' sudah tidak aktif.']);
            }

            if ($product->stock < $item->quantity) {
                throw ValidationException::withMessages(['cart' => 'Stok '.$product->name.' hanya tersisa '.$product->stock.'. Perbarui jumlah di keranjang.']);
            }
        }

        $token = Str::random(32);

        $payload = [
            'user_id' => $request->user()->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'notes' => $validated['notes'] ?? null,
            'buy_now_product_id' => $buyNowId,
            'cart_lines' => $cartItems->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ])->values()->all(),
        ];

        Cache::put("qris:{$token}", $payload, now()->addHour());

        $signedUrl = URL::signedRoute('checkout.qris.confirm', ['token' => $token]);

        $svg = ProductQrCode::svgForUrl($signedUrl);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Confirm QRIS payment via signed URL (one-time use).
     */
    public function confirmQris(Request $request, string $token): RedirectResponse
    {
        $payload = Cache::pull("qris:{$token}");

        if ($payload === null) {
            abort(410, 'QRIS ini sudah tidak berlaku (kadaluarsa atau sudah digunakan).');
        }

        $order = $this->createOrder(
            User::findOrFail($payload['user_id']),
            $payload,
            OrderStatus::Paid,
            PaymentMethod::Qris->value,
            $payload['cart_lines'] ?? [],
            $payload['buy_now_product_id'] ?? null,
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pembayaran QRIS berhasil. Pesanan Anda telah dibuat.');
    }

    /**
     * Create an order from cart lines (shared by regular checkout and QRIS confirm).
     */
    private function createOrder(
        User $user,
        array $payload,
        OrderStatus $status,
        string $paymentMethod,
        array $cartLines,
        ?int $buyNowProductId = null,
    ): Order {
        return DB::transaction(function () use ($user, $payload, $status, $paymentMethod, $buyNowProductId, $cartLines) {
            $lines = [];

            foreach ($cartLines as $cartLine) {
                $product = Product::whereKey($cartLine['product_id'])->lockForUpdate()->firstOrFail();

                if (! $product->is_active) {
                    throw ValidationException::withMessages(['cart' => 'Produk '.$product->name.' sudah tidak aktif.']);
                }

                if ($product->stock < $cartLine['quantity']) {
                    throw ValidationException::withMessages(['cart' => 'Stok '.$product->name.' hanya tersisa '.$product->stock.'. Perbarui jumlah di keranjang.']);
                }

                $lines[] = [
                    'product' => $product,
                    'quantity' => $cartLine['quantity'],
                ];
            }

            $totalCents = array_reduce(
                $lines,
                fn (int $carry, array $line): int => $carry + (int) round($line['product']->price * 100) * $line['quantity'],
                0,
            );

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'total' => number_format($totalCents / 100, 2, '.', ''),
                'status' => $status,
                'customer_name' => $payload['customer_name'],
                'phone' => $payload['phone'],
                'address' => $payload['address'],
                'payment_method' => $paymentMethod,
                'notes' => $payload['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $line['quantity'],
                    'subtotal' => number_format((int) round($product->price * 100) * $line['quantity'] / 100, 2, '.', ''),
                ]);

                $product->decrement('stock', $line['quantity']);
            }

            if ($buyNowProductId !== null) {
                $user->cartItems()->where('product_id', $buyNowProductId)->delete();
            } else {
                $user->cartItems()->delete();
            }

            return $order;
        });
    }

    /**
     * Compute the cart total in rupiah string format.
     */
    private function cartTotal(iterable $cartItems): string
    {
        $totalCents = 0;

        foreach ($cartItems as $item) {
            $totalCents += (int) round($item->product->price * 100) * $item->quantity;
        }

        return number_format($totalCents / 100, 2, '.', '');
    }
}

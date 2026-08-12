<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the current user's shopping cart.
     */
    public function index(Request $request): View
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product.category')
            ->orderByDesc('id')
            ->get();

        return view('cart.index', compact('cartItems'));
    }

    /**
     * Add a product to the current user's cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ]);

        $product = DB::transaction(function () use ($request, $validated): Product {
            $product = Product::active()
                ->whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $existing = $request->user()
                ->cartItems()
                ->where('product_id', $product->id)
                ->first();

            $newQuantity = ($existing?->quantity ?? 0) + $validated['quantity'];

            if ($newQuantity > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok {$product->name} hanya tersisa {$product->stock}.",
                ]);
            }

            $request->user()->cartItems()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $newQuantity],
            );

            return $product;
        });

        if ($request->boolean('buy_now')) {
            $request->session()->put('buy_now', ['product_id' => $product->id]);

            return redirect()
                ->route('checkout.index')
                ->with('success', "{$product->name} ditambahkan ke keranjang. Lanjutkan ke checkout.");
        }

        return redirect()
            ->route('cart.index')
            ->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    /**
     * Update the quantity of a cart item owned by the current user.
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwned($request, $cartItem);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ]);

        $product = $cartItem->product;

        if ($validated['quantity'] > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} hanya tersisa {$product->stock}.",
            ]);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    /**
     * Remove a cart item owned by the current user.
     */
    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwned($request, $cartItem);

        if ($request->session()->get('buy_now.product_id') === $cartItem->product_id) {
            $request->session()->forget('buy_now');
        }

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Prevent users from accessing cart items that do not belong to them.
     */
    private function ensureOwned(Request $request, CartItem $cartItem): void
    {
        abort_unless($cartItem->user_id === $request->user()->id, 404);
    }
}

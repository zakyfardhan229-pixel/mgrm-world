<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'total',
        'status',
        'customer_name',
        'phone',
        'address',
        'payment_method',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'status' => OrderStatus::class,
            'payment_method' => PaymentMethod::class,
        ];
    }

    /**
     * Scope to filter orders by search keyword.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('order_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%");
        });
    }

    /**
     * Generate the next sequential order number, e.g. ORD-20260811-0001.
     */
    public static function generateOrderNumber(): string
    {
        $todayPrefix = 'ORD-'.now()->format('Ymd').'-';

        $lastOrderNumber = (string) DB::table('orders')
            ->where('order_number', 'like', $todayPrefix.'%')
            ->orderByDesc('order_number')
            ->value('order_number');

        $lastNumber = $lastOrderNumber === ''
            ? 0
            : (int) substr($lastOrderNumber, strlen($todayPrefix));

        $next = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return $todayPrefix.$next;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

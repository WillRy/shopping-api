<?php

namespace CodeShopping\Models;

use Illuminate\Database\Eloquent\Model;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;

class Order extends Model
{

    use Filterable;

    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_SENT = 4;

    protected $fillable = ['user_id', 'product_id', 'amount', 'price', 'total'];

    public static function createWithProduct(array $data)
    {
        $product = Product::find($data['product_id']);
        $data['price'] = $product->price;
        $data['total'] = $data['price'] * $data['amount'];
        return self::create($data);
    }

    public function updateWithProduct()
    {
        try {
            \DB::beginTransaction();
            $this->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Supplier;
use App\Models\Product;

class Incoming extends Model
{   
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'product_id',
        'quantity',
        'purchase_price',
        'date_in',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Incoming;
use App\Models\Expense;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock'
    ];

    public function incoming()
    {
        return $this->hasMany(Incoming::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}

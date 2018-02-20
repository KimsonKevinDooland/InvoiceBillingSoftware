<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'invoice_number', 'discount_amount', 'total_amount',
    ];

     public function products()
    {
    	return $this->hasMany('\App\Product');
    }

    public function invoice_products()
    {
    	return $this->hasMany('\App\invoice_product','invoice_number', 'invoice_number');
    }

    
}

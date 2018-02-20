<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_product extends Model
{
    protected $fillable = [
        'invoice_number', 'products_id','product_qty',
    ];

    public function invoices()
    {
    	return $this->belongsTo('\App\Invoice','invoice_number','invoice_number');
    }

    public function product()
    {
    	return $this->belongsTo('\App\Product');
    }

}

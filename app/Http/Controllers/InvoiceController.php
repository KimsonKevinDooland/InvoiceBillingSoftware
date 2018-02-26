<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Product as Product;
use PDF;
use Redirect;
use DB;
use \App\Invoice;
use \App\Invoice_product;

class InvoiceController extends Controller
{
     public function htmltopdfview(Request $request)
    {
        $products = Product::all();
        view()->share('products',$products);
        
        if($request->has('download')){
            $pdf = PDF::loadView('htmltopdfview');
            return $pdf->download('htmltopdfview');
        }
        return view('htmltopdfview');
    }//EOF
     public function getproductdata(Request $request){

          $products = DB::table('products')->where('barcode_number', $request->message)->first();

                $invoice_number = $request->invoice_number;
                // save data in invoices table
                if($products)
                {
                          $newInvoice_product = new Invoice_product([
                            'invoice_number' => $invoice_number,
                            'products_id' => $products->id,
                            'product_qty'=>'1',
                          ]);
                          $newInvoice_product->save();
                }
            if($products)
            {   
                   return response()->json($products); 
            }
         }//EOF
         public function save_user(Request $request)
         {
                $invoice_number = $request->invoice_number;
                $client_name = $request->client_name;
                $client_address = $request->client_address;
                $client_phone = $request->client_phone;
                if($client_phone)
                {
                       $newInvoice = new Invoice([
                                'name' => $client_name,
                                'address' => $client_address,
                                'phone' => $client_phone,
                                'invoice_number' => $invoice_number,
                         ]);  
       if($newInvoice->save() && $client_phone)
                //get all the products for that bill and deduct from the main inventory the qty
             $inventory_products =  DB::table('invoice_products')->where('invoice_number', $invoice_number)->get();
             //updating each product in the invoice list
                 foreach ($inventory_products as $product) {
                     $product_id = $product->products_id;
                     $product_qty = $product->product_qty;
                     $main_inventory_products = DB::table('invetories')->where('product_id', $product_id)->first();
                     $main_inventory_product_qty=$main_inventory_products->product_qty;
                     $now_qty = $main_inventory_product_qty - $product_qty;
                     //check if product is in the inventory
                      if($now_qty>0){
                         DB::table('invetories')
                             ->where('product_id', $product_id)
                             ->update(['product_qty' => $now_qty]);
                        }else {
                            $error = 'no product in the inventory';
                               return response()->json($error); 
                        }
                    }
                        //returning back response
                        {   
                               return response()->json($newInvoice); 
                        }      
             }
         }//EOF
         public function delete_row(Request $request)
         {
            if($request)
            {
                $removeInvoiceProduct = DB::table('invoice_products')
                ->where('invoice_number',$request->invoice_number)
                ->where('products_id', $request->products_id)
                ->delete();
                return response()->json($request);                     
            }
         }//EOF

         public function update_qty(Request $request)
         {
            if($request)
            {
                $updateInvoiceProductqty  = DB::table('invoice_products')
                ->where('invoice_number',$request->invoice_number)
                ->where('products_id', $request->products_id)
                ->update(['product_qty' => $request->product_qty]);

              return response()->json($request); 

            }
         }//EOF
}

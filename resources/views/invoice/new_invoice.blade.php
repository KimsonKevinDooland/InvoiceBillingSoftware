@extends('layouts.app')
@section('content')
  <div class="container">
    <h1>Create Invoice</h1>
     <h5>Invoice Number <span class="send_invoice" data-invoice="{{$invoice_number}}">{{$invoice_number}}</span></h5>
    <div class="row">
        <div class="col-md-4">
              <label>Client</label><br>
              <input id="client_name" type="text" name=""  placeholder="Client">
        </div>    
        <div class="col-md-4">     
            <label>Client  Address</label><br>
            <textarea placeholder="Client Address"  rows="4" cols="50" id="client_address"></textarea>
        </div>      
         <div class="col-md-4">     
            <label>Client  Phone number</label><br>
            <input type="number" id="client_phone" name="" placeholder="phone number"> 
        </div>  
    </div>
    <input class="getinfo" name="barcode_txt" onchange="clickbtn()" placeholder="Barcode Number" autofocus ></input>
    <button id="postbutton" class="btn btn-default">Post via ajax!</button>
    <div class="writeinfo"></div>   
        <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th valign="middle">Id</th>
                                <th>Product Name</th>
                                <th>Product Code</th>
                                <th>Price</th>
                                <th>Product Desc</th>
                                <th>Barcode Number</th>
                                <th>Product Qty</th>
                                <th>Action</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody class="item_ho text-center"> 
                         
                        </tbody>
                    </table>
                    <button onclick="get_values_from_div()">hey</button>
                    <br>QTY<input type="" name="" id="get_qty_value"> <br>
                    Price<input type="" name="" id="get_price_value">

        </div><!-- /.panel-body -->
        <button class="btn btn-success" id="submit_invoice">Submit</button>
</div>
@endsection

    <!-- provide the csrf token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {{-- <link rel="stylesheet" href="{{asset('css/app.css')}}"> --}}
    <script src="{{asset('js/jquery.js')}}"></script>

    <script>
        $(document).ready(function(){
            //focous of input
            $('input[name=barcode_txt]').focus();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $("#postbutton").click(function(){
                $.ajax({
                    /* the route pointing to the post function */
                    url: '/getbarcode',
                    type: 'POST',
                    /* send the csrf-token and the input to the controller */
                    data: {
                        _token: CSRF_TOKEN, 
                        message:$(".getinfo").val(), 
                        invoice_number:$(".send_invoice").data("invoice"),
                        client_name:$("#client_name").val(),
                        client_address:$("#client_address").val(),
                        client_phone:$("#client_phone").val(),
                    },
                    dataType: 'JSON',
                    /* remind that 'data' is the response of the AjaxController */
                    success: function (data) { 
                        $(".item_ho").append("<tr class='item" + data.id + "'><td>" + data.id + "</td><td>" + data.product_name + "</td><td>" + data.product_code + "</td></td><td id='price_value'>"+data.price+"</td></td><td>" + data.product_desc + "</td><td>" + data.barcode_number +"</td><td>" + " <input type='number' name='total_qty_value' value='1' size='5' id='product_qty_value' data-id='" + data.id + "' data-barcode_number='" + data.barcode_number + "'> "+"</td><td>"+ "<a id='remove_row' data-id='" + data.id + "' data-barcode_number='" + data.barcode_number + "'>x</a>"+"</td>"+"<td id='total_price'></td>"+"</tr>"); 
                            // save qty value
                           document.getElementById('get_qty_value').value = document.getElementById('product_qty_value').value;
                            //calculating the price.
                           var $product_price = $('#price_value').text();
                           var $qty = $('#product_qty_value').val();
                            $('#total_price').text($product_price * $qty);

                    }
                });

                    //clearing the field for the barcode.
                    $(".getinfo").val("");
                    //focusing the cursor back to the input.
                    $('input[name=barcode_txt]').focus();

                    

             });
                    //auto click when the value is inserted into the field.
                    $(".getinfo").on("change paste keyup" ,function(){ // change paste keyup
                             $('#postbutton').trigger('click');

                    });

                     $("#submit_invoice").click(function(){
                            $.ajax({
                                /* the route pointing to the post function */
                                url: '/submit_invoice',
                                type: 'POST',
                                /* send the csrf-token and the input to the controller */
                                data: {
                                    _token: CSRF_TOKEN, 
                                    invoice_number:$(".send_invoice").data("invoice"),
                                    client_name:$("#client_name").val(),
                                    client_address:$("#client_address").val(),
                                    client_phone:$("#client_phone").val(),
                                },
                                dataType: 'JSON',
                                /* remind that 'data' is the response of the AjaxController */
                                success: function (data) { 
                                    alert(data.invoice_number + " submited your data");
                                }
                            });
                             
                    });
                     //Delete row trigger
                      $(document).on('click', '#remove_row', function() {
                        //deletes the row that is selected.
                        $(this).parent().parent().remove();

                        $.ajax({
                            type: 'POST',
                            url: '/delete_row',
                            data: {
                                '_token': $('input[name=_token]').val(),
                                invoice_number:$(".send_invoice").data("invoice"),
                                products_id:$(this).data("id"),
                            },
                            success: function(data) {
                                alert(data.products_id);
                            }
                        });     
                    });
                      //Update the qty of the row.
                      $(document).on('change', '#product_qty_value', function() {
                            //calculating the price.
                           var $product_price = $('#price_value').text();
                           var $qty = $('#product_qty_value').val();
                            $('#total_price').text($product_price * $qty); 

                        document.getElementById('get_qty_value').value = document.getElementById('product_qty_value').value;
                           $.ajax({
                            type: 'POST',
                            url: '/update_row',
                            data: {
                                '_token': $('input[name=_token]').val(),
                                invoice_number:$(".send_invoice").data("invoice"),
                                products_id:$(this).data("id"),
                                product_qty:$(this).val(),
                            },
                            success: function(data) {
                                alert(data.product_qty);
                            }
                        });     
                      });
       });    
         //get table data values
        // function get_values_from_div()
        // {
        //     var Row = document.getElementsByClassName("item");
        //     var Cells = document.getElementsByTagName("td");
        //      alert(Cells[5].innerText);
        //     var barcode_value = Cells[5].innerText;
        //     get_qty_value();
        // }
    
      
                  

    </script>
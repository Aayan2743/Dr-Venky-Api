<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderProductsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return Stock::all();
        
        //  return Stock::with('product', 'order.address')
        //     ->where('model_type', 'App\Models\Order')
        //     ->get();
        
        
    //     $orderProducts = Stock::with('product', 'order.address')
    //         ->where('model_type', 'App\Models\Order')
    //         ->get()
    //         ->map(function ($stock) {
    //             $productName = $stock->product ? $stock->product->name : null;
    //             $productPrice = $stock->product ? $stock->product->buying_price : null;
    //             $margin = $stock->product->selling_price - $stock->product->buying_price;

    //             $order = $stock->order;
    //             $orderTypeLabel = null;

    //             if ($order) {
    //                 switch ($order->order_type) {
    //                     case 5:
    //                         $orderTypeLabel = 'Online';
    //                         break;
    //                     case 15:
    //                         $orderTypeLabel = 'POS';
    //                         break;
    //                     default:
    //                         $orderTypeLabel = 'Unknown';
    //                         break;
    //                 }
    //             }

    //             $orderAddress = $order ? $order->address->first() : null;

    //             $cgst = 0;
    //             $sgst = 0;
    //             $igst = 0;
    //             $totalAmount = $stock->total;

    //             if ($orderAddress) {
    //                 if ($orderAddress->state == 'Telangana') {
    //                     $cgst = $stock->total * 0.09;
    //                     $sgst = $stock->total * 0.09;
    //                     $totalAmount = $stock->total + $cgst + $sgst;
    //                 } else {
    //                     $igst = $stock->total * 0.18;
    //                     $totalAmount = $stock->total + $igst;
    //                 }
    //             }
    }
    
    public function view(): View
    {
        $orderProducts = Stock::with('product', 'order.address')
            ->where('model_type', 'App\Models\Order')
            ->get()
            ->map(function ($stock) {
                $productName = $stock->product ? $stock->product->name : null;
                $productPrice = $stock->product ? $stock->product->buying_price : null;
                $margin = $stock->product->selling_price - $stock->product->buying_price;
                  $hsnCode = $stock->product ? $stock->product->HsnCode : null; 

        //dd($stock->product->HsnCode);
                $order = $stock->order;
                $orderTypeLabel = null;
                //dd($stock->product);
                if ($order) {
                    switch ($order->order_type) {
                        case 5:
                            $orderTypeLabel = 'Online';
                            break;
                        case 15:
                            $orderTypeLabel = 'POS';
                            break;
                        default:
                            $orderTypeLabel = 'Unknown';
                            break;
                    }
                }

                $orderAddress = $order ? $order->address->first() : null;

                $cgst = 0;
                $sgst = 0;
                $igst = 0;
                $totalAmount = $stock->total;

                if ($orderAddress) {
                    if ($orderAddress->state == 'Telangana') {
                        $cgst = $stock->total * 0.09;
                        $sgst = $stock->total * 0.09;
                        $totalAmount = $stock->total + $cgst + $sgst;
                    } else {
                        $igst = $stock->total * 0.18;
                        $totalAmount = $stock->total + $igst;
                    }
                }

                // dd($stock);

                return [
                    'id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'product_name' => $productName,
                    'sku' => $stock->sku,
                    'hsnCode' => $hsnCode,
                    'buying_price' => $stock->product->buying_price,
                    'selling_price' => $stock->product->selling_price,
                    'discount' => $stock->product->discount,
                    'margin' => $margin,
                    'subtotal' => $stock->subtotal,
                    'tax' => $stock->tax,
                    'final_price' => $stock->total,
                    'order_serial_no' => $order ? $order->order_serial_no : null,
                    'order_datetime' => $order ? $order->order_datetime : null,
                    'order_type' => $orderTypeLabel,
                    'order_address' => $orderAddress ? [
                        'city' => $orderAddress->city,
                        'state' => $orderAddress->state,
                        'postal_code' => $orderAddress->zip_code,
                    ] : null,
                    'cgst_amount' => round($cgst, 2),
                    'sgst_amount' => round($sgst, 2),
                    'igst_amount' => round($igst, 2),
                    'total_amount' => round($totalAmount, 2),
                ];
            });

        return view('exports.order_products', compact('orderProducts'));
    }
    
}

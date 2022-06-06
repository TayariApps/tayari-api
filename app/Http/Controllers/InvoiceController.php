<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice as Inv;
use App\Models\InvoiceItem as InvItem;
use App\Models\Place;
use App\Models\Order;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoiceController extends Controller
{
    public function generateInvoice($invoiceID){

        $inv = Inv::where('id', $invoiceID)->first();

        $place = Place::where('id', $inv->place_id)->first();

        $client = new Party([
            'name'          => 'TAYARI PAYMENTS',
            'phone'         => '+255766397171',
            // 'custom_fields' => [
            //     'note'        => 'IDDQD',
            //     'business id' => '365#GG',
            // ],
        ]);

        $customer = new Party([
            'name'          => $place->name,
            'address'       => $place->address,
            'code'          => '#22663214',
            // 'custom_fields' => [
            //     'order number' => '> 654321 <',
            // ],
        ]);

        $invItems = InvItem::where('invoice_id', $inv->id)->with('order.food')->get();

        $items = [];
        
        //start of foreach
        foreach ($invItems as $i) {
        
            foreach ($i->order->food as $meal) {

                array_push($items, (new InvoiceItem())->title($meal->menu_name)
                ->pricePerUnit((float)$meal->pivot->cost)
                ->quantity((float)$meal->pivot->quantity));

            }

        }
        //end of for each

        $invoice = Invoice::make('invoice')
        ->series('BIG')
        // ability to include translated invoice status
        // in case it was paid
        ->status(__('invoices::invoice.paid'))
        ->sequence($inv->id)
        ->serialNumberFormat('{SEQUENCE}/{SERIES}')
        ->seller($client)
        ->buyer($customer)
        ->date(now())
        ->dateFormat('m/d/Y')
        ->payUntilDays(1)
        ->currencySymbol('Tsh')
        ->currencyCode('TZS')
        ->currencyFormat('{SYMBOL}{VALUE}')
        ->currencyThousandsSeparator('.')
        ->currencyDecimalPoint(',')
        ->filename($client->name . ' ' . $customer->name)
        ->addItems($items)
        ->logo(public_path('images/tayarilaravel.png'));
        
        return $invoice->stream();
    }   

    public function storeInvoice($placeId, $disbursementId, $amount){
        $place = Place::where('id', $placeId)->with('orders')->first();

        $orders = Order::where([
            'place_id' => $placeId,
            'disbursed' => false
        ])->get();

        $inv = Inv::create([
            'disbursement_id' => $disbursementId, 
            'place_id' => $placeId, 
            'amount' => $amount
        ]);

        foreach ($orders as $order) {
            InvItem::create([
                'invoice_id' => $inv->id, 
                'order_id' => $order->id
            ]);
        }

        foreach ($orders as $order) {
            $order->update([
                'disbursed' => true
            ]);
        }

        return \response()->json('Invoice created',201);
    }

}

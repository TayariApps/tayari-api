<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{ Menu, User, Sale, Place, Table, Drink, DrinkOrder, DrinkStock, OrderItem, Order, UserCoupon, SystemConstant};
use Illuminate\Support\Facades\{Validator, Http};
use App\Http\Controllers\{MailController,SMSController, NotificationController};

class SecondOrderController extends Controller
{
    public function cashOrder(Request $request){
        date_default_timezone_set('Africa/Dar_es_Salaam');

        $validator = Validator::make($request->all(), [
            'executed_time' => 'required', 
            'customer_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        $place = Place::where('id', $request->place_id)->first();
        $now = Carbon::now();                                    //current timestamp

        $cost = 0.00;                                           //cost which will be displayed to the customer
        $productTotal = 0;                                      //total number of products in the order
        $foodCost = 0.0;                                        //cost of food for restuarant

        $txtBody = "A new order has been made on Tayari App. \n";
        switch ($request->type) {
            case 1:
                $txtBody .= "Order type: Pre-order \n"; 
                break;

            case 2: 
                $txtBody .= "Order type: Dine In \n"; 
                break;
            
            case 3: 
                $txtBody .= "Order type: Reservation \n"; 
                break;
            
            case 4: 
                $txtBody .= "Order type: Delivery"; 
                break;
            
            default:
                $txtBody .= "Order type: Pre-order \n"; 
                break;
        }

        $txtBody .= "\n The order items are: \n";

        if ($request->has('customer_id')) {
            $user = User::where('id', $request->customer_id)->first();
        }

        if($request->type == 4){
            $order = Order::create([
                'table_id' => 1,
                'place_id' => $request->place_id,
                'executed_time' => $cont->executed_time,
                'customer_id' => $cont->customer_id,
                'waiting_time' => $cont->waiting_time,
                'order_created_by' => $request->user()->id,
                'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
                'type' => $request->type,
                'payment_method' => $request->method //method of payment
            ]);
        } else{
            $order = Order::create([
                'table_id' => $cont->table_id,
                'place_id' => $request->place_id,
                'executed_time' => $cont->executed_time,
                'customer_id' => $cont->customer_id,
                'waiting_time' => $cont->waiting_time,
                'order_created_by' => $request->user()->id,
                'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
                'type' => $request->type,
                'payment_method' => $request->method //method of payment
            ]);
        }

        if($request->has('foods')){

            foreach ($cont->foods as $item) {

                $menu = Menu::where('id', $item->id)->with('type')->first();

                $orderItem = OrderItem::create([
                    'menu_id' => $item->id, 
                    'order_id' => $order->id, 
                    'quantity' => $item->quantity, 
                    'details' => $item->details,
                    'cost' =>  $item->price * $item->quantity 
                ]);

                $typename = $menu->type->name;

                $txtBody .= "Food type: $typename \n";
                
                if($orderItem->details !== null){
                    $txtBody .= "$item->quantity x $menu->menu_name with extras: $orderItem->details \n\n";
                }else{
                    $txtBody .= "$item->quantity x $menu->menu_name \n\n";
                }
                
                $cost += $orderItem->cost;
                $productTotal += $orderItem->quantity;       
            }

        }

        if($request->has('drinks')){

            foreach ($cont->drinks as $drink) { 

                $drinkItem = Drink::where('id', $drink->id)->first();
                
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $request->place_id
                ])->first();

                // $drinkstock->update([
                //     'quantity' => $drinkstock->quantity - $drink->quantity
                // ]);

                $drinkOrder = DrinkOrder::create([
                    'drink_id' => $drink->id,
                    'order_id' => $order->id,
                    'quantity' => $drink->quantity,
                    'price' =>  $drink->price * $drink->quantity
                ]);

                $txtBody .= "$drink->quantity x $drinkItem->name \n";
    
                $cost += $drinkOrder->price;
                $productTotal += $drink->quantity;
            }
    
        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost,
            'order_number' => "TYR-".$order->id,
            'product_total' => $productTotal
        ]);

        // $txtBody .= "\n 10% Tayari discount is active";
        $txtBody .= "\n The customer will pay $cost TZS";
        $txtBody .= "\n Customer phone: $user->phone";
        $txtBody .= "\n Tayari will pay you $order->total_cost TZS";

        $newOrder = Order::where('id', $order->id)->with(['food','drinks','table','place','customer'])->first();

        // $mailController = new MailController();
        // $mailController->orderRecievedMail(null, $place);

        $smsController = new SMSController();

        if($place->cashier_number !== null){
            $smsController->sendMessage(null, $txtBody, $place->cashier_number);
        }

       if($request->type == 4){
        $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255714779397");
        $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255747852570");
       } else{
        $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255714779397");
        $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255747852570");
       }

       //send notification to user

       //check if order has customer id
       if($newOrder->customer_id != null){
            //check if customer has FCM token
            if($newOrder->customer->fcm != null){
                $notificationController = new NotificationController();

                //send notification
                $notificationController->notificationMethod('Tayari', "Your order has been accepted, it will take $newOrder->waiting_time minutes", $newOrder->customer_id);
            }
       }

        return response()->json($newOrder, 201);
    }


    public function mobileOrder(Request $request){
        date_default_timezone_set('Africa/Dar_es_Salaam');

        $validator = Validator::make($request->all(), [
            'executed_time' => 'required', 
            'customer_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        $place = Place::where('id', $request->place_id)->first();
        $now = Carbon::now();                                    //current timestamp

        $cost = 0.00;                                           //cost which will be displayed to the customer
        $productTotal = 0;                                      //total number of products in the order
        $foodCost = 0.0;                                        //cost of food for restuarant
        $constant = SystemConstant::where('id', 1)->first();    //system constants
        $hasCoupon  = false;                                    //boolean value to check if user has coupon

        $txtBody = "A new order has been made on Tayari App. \n";
        switch ($request->type) {
            case 1:
                $txtBody .= "Order type: Pre-order \n"; 
                break;

            case 2: 
                $txtBody .= "Order type: Dine In \n"; 
                break;
            
            case 3: 
                $txtBody .= "Order type: Reservation \n"; 
                break;
            
            case 4: 
                $txtBody .= "Order type: Delivery"; 
                break;
            
            default:
                $txtBody .= "Order type: Pre-order \n"; 
                break;
        }

        $txtBody .= "\n The order items are: \n";

        if ($request->has('customer_id')) {
            $user = User::where('id', $request->customer_id)->first();
        }

        if($request->type == 4){
            $order = Order::create([
                'table_id' => 1,
                'place_id' => $request->place_id,
                'executed_time' => $cont->executed_time,
                'customer_id' => $cont->customer_id,
                'waiting_time' => $cont->waiting_time,
                'order_created_by' => $request->user()->id,
                'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
                'type' => $request->type,
                'payment_method' => $request->method //method of payment
            ]);
        } else{
            $order = Order::create([
                'table_id' => $cont->table_id,
                'place_id' => $request->place_id,
                'executed_time' => $cont->executed_time,
                'customer_id' => $cont->customer_id,
                'waiting_time' => $cont->waiting_time,
                'order_created_by' => $request->user()->id,
                'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
                'type' => $request->type,
                'payment_method' => $request->method //method of payment
            ]);
        }

        if($request->has('coupon')){

            $checkIfExists = UserCoupon::where([
                'coupon' => $request->coupon,
                'used' => false
                ])->exists();

            if($checkIfExists){

                $coupon = UserCoupon::where([
                    'coupon' => $request->coupon,
                    'used' => false
                    ])->first();

                $coupon->update([
                    'used' => true,
                    'user_id' => $cont->customer_id,
                    'place_id' => $request->place_id,
                ]);

                $hasCoupon = true;
            } 
        } 

        if($request->has('foods')){

            foreach ($cont->foods as $item) {

                $menu = Menu::where('id', $item->id)->with('type')->first();

                $orderItem = OrderItem::create([
                    'menu_id' => $item->id, 
                    'order_id' => $order->id, 
                    'quantity' => $item->quantity, 
                    'details' => $item->details,
                    'cost' => !$hasCoupon ? 
                                ($item->price - ($item->price * $menu->discount)) * $item->quantity :
                                ($item->price - ($item->price * 0.5)) * $item->quantity 
                ]);

                $typename = $menu->type->name;

                $txtBody .= "Food type: $typename \n";

                if($orderItem->details !== null){
                    $txtBody .= "$item->quantity x $menu->menu_name with extras: $orderItem->details \n\n";
                }else{
                    $txtBody .= "$item->quantity x $menu->menu_name \n\n";
                }
                
                $cost += $orderItem->cost;
                $foodCost += $constant->discount_active ? $item->price * $constant->discount : 0;
                $productTotal += $orderItem->quantity;       
            }

        }

        if($request->has('drinks')){

            foreach ($cont->drinks as $drink) { 

                $drinkItem = Drink::where('id', $drink->id)->first();
                
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $request->place_id
                ])->first();

                // $drinkstock->update([
                //     'quantity' => $drinkstock->quantity - $drink->quantity
                // ]);

                $drinkOrder = DrinkOrder::create([
                    'drink_id' => $drink->id,
                    'order_id' => $order->id,
                    'quantity' => $drink->quantity,
                    'price' => $hasCoupon ? ($drink->price - ($drink->price * 0.5)) * $drink->quantity :
                                $drink->price * $drink->quantity
                ]);

                $txtBody .= "$drink->quantity x $drinkItem->name \n";
    
                $cost += $drinkOrder->price;
                $productTotal += $drink->quantity;
            }
    
        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost + $foodCost,
            'order_number' => "TYR-".$order->id,
            'product_total' => $productTotal
        ]);

        // $txtBody .= "\n 10% Tayari discount is active";
        $txtBody .= "\n The customer will pay $cost TZS";
        $txtBody .= "\n Customer phone: $user->phone";
        $txtBody .= "\n Tayari will pay you $order->total_cost TZS";

        $newOrder = Order::where('id', $order->id)->with(['food','drinks','table','place','customer'])->first();

        // $mailController = new MailController();
        // $mailController->orderRecievedMail(null, $place);

        $smsController = new SMSController();

        if($place->cashier_number !== null){
            $smsController->sendMessage(null, $txtBody, $place->cashier_number);
        }

       if($request->type == 4){
        $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255714779397");
        $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255747852570");
       } else{
        $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255714779397");
        $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255747852570");
       }

       //send notification to user

       //check if order has customer id
       if($newOrder->customer_id != null){
            //check if customer has FCM token
            if($newOrder->customer->fcm != null){
                $notificationController = new NotificationController();

                //send notification
                $notificationController->notificationMethod('Tayari', "Your order has been accepted, it will take $newOrder->waiting_time minutes", $newOrder->customer_id);
            }
       }

        return response()->json($newOrder, 201);
    }





}

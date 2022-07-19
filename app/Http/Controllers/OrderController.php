<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{ Menu, User, Sale, Place, Table, Drink, DrinkOrder, DrinkStock, OrderItem, Order, UserCoupon, SystemConstant, RestaurantNumber};
use Illuminate\Support\Facades\{Validator, Http};
use App\Http\Controllers\{MailController,SMSController, NotificationController};

class OrderController extends Controller
{
    public function index(){
        return response()->json(Order::all(),200);
    }

    public function restaurantConfirmPayment($id){

        $order = Order::where('id', $id)->first();
        
        $order->update([
            'payment_method' => 1,
            'payment_status' => true,
            'status' => 4,
            'paid' => $order->cost
        ]);

        $time = time();

        Sale::create([
            'order_id' => $order->id , 
            'code' => $time, 
            'amount' => $order->cost, 
            'reference_id' => $time, 
            'remarks' => 'Paid with cash', 
            'type' => 1, 
            'place_id' => $order->place_id,
            'paid' => true,
        ]);

        return response()->json('Payment complete',200);
    }

    public function orderStatus($id){
        $orders = Order::where('place_id', $id)
                ->with(['customer', 'drinks', 'food','table'])
                ->withCount('food')
                ->orderBy('id','desc')
                ->whereDate('created_at', Carbon::today())
                ->get();
        return \response()->json($orders,200);
    }

    public function placeOrders($id){
        $orders = Order::where('place_id', $id)
                        ->with(['customer', 'drinks', 'food','table'])
                        ->withCount('food')
                        ->orderBy('id','desc')
                        ->get();
        return \response()->json($orders,200);
    }

    public function userOrders(Request $request){
        
        $checkIfOrderExists = Order::where('customer_id', $request->user()->id)->exists();

        if($checkIfOrderExists){
            $orders = Order::where('customer_id', $request->user()->id)->with([
                'food','drinks','table','place'
                ])->get();
            return \response()->json($orders,200);
        }

        return \response()->json([],200);
       
    }

    public function changeStatus(Request $request){
        $order = Order::where('id', $request->order_id)->with('customer')->first();

        $order->update([
            'status' => $request->status
        ]);

        if($request->status == 4){
            if($order->customer !== null){
                $smsController = new SMSController();
                $smsController->sendMessage(null, "Your order is ready and on the way!", $order->customer->phone);
            }
        }

        return \response()->json('Order status updated',200);
    }

    public function delete($id){
        $order = Order::where('id', $id)->first();

        if($order->payment_status == true){
            return response()->json('You cant delete an order that has already been paid for.',400);
        }

        OrderItem::where('order_id', $id)->delete();
        DrinkOrder::where('order_id',$id)->delete();
        Sale::where('order_id', $id)->delete();

        $order->delete();

        return response()->json('Order deleted',200);
    }

    public function store(Request $request){
        date_default_timezone_set('Africa/Dar_es_Salaam');

        $validator = Validator::make($request->all(), [
            'executed_time' => 'required', 
            'customer_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        //json parse the data
        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        $place = Place::where('id', $request->place_id)->first();            //get restaurant object
        $now = Carbon::now();                                               //current timestamp

        $cost = 0.00;                                                       //cost which will be displayed to the customer
        $productTotal = 0;                                                  //total number of products in the order
        $foodCost = 0.00;                                                   //actual cost for food
        $drinkCost = 0.00;                                                  //actual cost for drink
        $constant = SystemConstant::where('id', 1)->first();                //system constants
        $hasCoupon  = false;                                                //boolean value to check if user has coupon

        $txtBody = "A new order has been made on Tayari App. \n";           //initialize text

        //get order type to add to text
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

        //initialize order items to text
        $txtBody .= "\n The order items are: \n";

        //get customer id if present in request object
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
                'payment_method' => (int)$request->method //method of payment
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
                'payment_method' => (int)$request->method //method of payment
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
                    'details' => $item->details == null || "" ? null : $item->details,
                    'cost' => !$hasCoupon ? 
                                ($item->price - ($item->price * $menu->discount)) * $item->quantity :
                                ($item->price - ($item->price * 0.5)) * $item->quantity 
                ]);

                if($order->payment_method == 1){
                    $orderItem->update([
                        'cost' => $item->price * $item->quantity 
                    ]);
                }

                $typename = $menu->type->name;

                $txtBody .= "Food type: $typename \n";
                
                if($orderItem->details !== null){
                    $txtBody .= "$item->quantity x $menu->menu_name with extras: $orderItem->details \n\n";
                }else{
                    $txtBody .= "$item->quantity x $menu->menu_name \n\n";
                }
                
                $cost += $orderItem->cost;
                $productTotal += $orderItem->quantity;  
                
                if($order->payment_method == 2){
                    $foodCost += $constant->discount_active ? ($item->price * $item->quantity)* $constant->discount : 0;
                }else{
                    $foodCost += 0;
                }
            }

        }


        if($request->has('drinks')){

            foreach ($cont->drinks as $drink) { 

                $drinkItem = Drink::where('id', $drink->id)->first();
                
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $request->place_id
                ])->first();

                $drinkOrder = DrinkOrder::create([
                    'drink_id' => $drink->id,
                    'order_id' => $order->id,
                    'quantity' => $drink->quantity,
                    'price' => $hasCoupon ? ($drinkstock->selling_price - ($drinkstock->selling_price * 0.5)) * $drink->quantity :
                                ($drinkstock->selling_price - ($drinkstock->selling_price * $constant->discount)) * $drink->quantity
                ]);

                if($order->payment_method == 1){
                    $drinkOrder->update([
                        'price' => $drinkstock->selling_price * $drink->quantity 
                    ]);
                }

                $txtBody .= "$drink->quantity x $drinkItem->name \n";
    
                $cost += $drinkOrder->price;
                $productTotal += $drink->quantity;

                if($order->payment_method == 2){
                    $drinkCost += $constant->discount_active ? ($drinkstock->selling_price * $drink->quantity) * $constant->discount : 0;
                }else{
                    $drinkCost += 0;
                }
            }
    
        }

        $order->update([
            'cost' => $cost,
            'total_cost' => $cost + $foodCost + $drinkCost,
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

        if(env('APP_ENV') == "production"){
            if($place->cashier_number !== null){
                $smsController->sendMessage(null, $txtBody, $place->cashier_number);
            }

            if($request->type == 4){
                $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255714779397");
                $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255747852570");

                $numbers = RestaurantNumber::where('place_id', $place->id)->get();

                if(count($numbers) > 0){
                    foreach ($numbers as $number) {
                        $smsController->sendMessage(null, "A delivery order has been made on $place->name", $number->phone);
                    }
                }

               } else{
                $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255714779397");
                $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255747852570");

                $numbers = RestaurantNumber::where('place_id', $place->id)->get();
                
                if(count($numbers) > 0){
                    foreach ($numbers as $number) {
                        $smsController->sendMessage(null, $txtBody, $number->phone);
                    }
                }

               }
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{ Menu, User, Sale, Place, Table, Drink, DrinkOrder, DrinkStock, OrderItem, Order, UserCoupon, SystemConstant};
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
            'foods' => 'required'
        ]);

        if($validator->fails()){
            return response()->json('Please enter all details', 400);
        }

        $somedata = $request->input();
        $somedata = file_get_contents("php://input");
        $cont = json_decode($somedata);
        $place = Place::where('id', $request->place_id)->first();
        $now = Carbon::now();

        $cost = 0.00;
        $productTotal = 0;

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

        $constant = SystemConstant::where('id', 1)->first();
        $totalCost = 0.0;

        if($request->has('drinks')){

            foreach ($cont->drinks as $drink) { 

                $drinkItem = Drink::where('id', $drink->id)->first();
                
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $request->place_id
                ])->first();

                $drinkstock->update([
                    'quantity' => $drinkstock->quantity - $drink->quantity
                ]);

                DrinkOrder::create([
                    'drink_id' => $drink->id,
                    'order_id' => $order->id,
                    'quantity' => $drink->quantity,
                    'price' => $drinkstock->selling_price
                ]);

                $txtBody .= "$drink->quantity x $drinkItem->name \n";
    
                $cost += $drinkstock->selling_price;
                $totalCost += $drinkstock->selling_price;
                $productTotal += $drink->quantity;
            }
    
        }

        if($request->has('foods')){

            foreach ($cont->foods as $item) {

                $menu = Menu::where('id', $item->id)->first();

                $orderItem = OrderItem::create([
                    'menu_id' => $item->id, 
                    'order_id' => $order->id, 
                    'quantity' => $item->quantity, 
                    'cost' => ($item->price - ($item->price * $menu->discount)) * $item->quantity 
                ]);
                
                $txtBody .= "$item->quantity x $menu->menu_name \n";
                
                $cost += $orderItem->cost;
                $totalCost += $constant->discount_active ? $item->price * $constant->discount : 0;
                $productTotal += $orderItem->quantity;

                //testing
                // return \response()->json([
                //     'cost' => $cost,
                //     'totalCost' => $totalCost,
                //     'constant' => $constant->discount_active,
                //     'discount' => $constant->discount,
                //     'menu_discount' => $menu->discount,
                //     'menu_price' => $item->price,
                //     'discount_Active' => $constant->discount_active
                // ], 200);
            }

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

                $order->update([
                    'cost' => $cost - ($cost * 0.5),
                    'total_cost' => $cost + $totalCost,
                    'order_number' => "TYR-".$order->id,
                    'product_total' => $productTotal
                ]);

            } else{

                $order->update([
                    'cost' => $cost,
                    'total_cost' => $cost + $totalCost,
                    'order_number' => "TYR-".$order->id,
                    'product_total' => $productTotal
                ]);

            }

        } else {
            $order->update([
                'cost' => $cost,
                'total_cost' => $cost + $totalCost,
                'order_number' => "TYR-".$order->id,
                'product_total' => $productTotal
            ]);
        }

        $txtBody .= "\n 10% Tayari discount is active";
        $txtBody .= "\n The customer will pay $cost TZS";
        $txtBody .= "\n Customer phone: $user->phone";
        $txtBody .= "\n Tayari will pay you $order->total_cost TZS";

        $newOrder = Order::where('id', $order->id)->with(['food','drinks','table','place','customer'])->first();

        $mailController = new MailController();
        $mailController->orderRecievedMail(null, $place);

        $smsController = new SMSController();

        // if($place->cashier_number !== null){
        //     $smsController->sendMessage(null, $txtBody, $place->cashier_number);
        // }

    //    if($request->type == 4){
    //     $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255714779397");
    //     $smsController->sendMessage(null, "A delivery order has been made on $place->name", "255747852570");
    //    } else{
    //     $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255714779397");
    //     $smsController->sendMessage(null, "A restaurant order has been made on $place->name", "255747852570");
    //    }

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

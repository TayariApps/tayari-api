<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{ Menu, User, Sale, Place, Table, DrinkOrder, DrinkStock, OrderItem, Order, UserCoupon};
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MailController;
use App\Http\Controllers\SMSController;
use App\Models\SystemConstant;

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
        $validator = Validator::make($request->all(), [
            'table_id' => 'required', 
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

        $now = Carbon::now();
        $table = Table::where('id', $cont->table_id)->first();

        $cost = 0.00;
        $productTotal = 0;

        $order = Order::create([
            'table_id' => $table->id,
            'place_id' => $request->place_id,
            'executed_time' => $cont->executed_time,
            'customer_id' => $cont->customer_id,
            'waiting_time' => $cont->waiting_time,
            'order_created_by' => $request->user()->id,
            'completed_time' => $now->addMinutes($cont->waiting_time)->toDateTimeString(),
            'type' => $request->type,
            'payment_method' => $request->method //method of payment
        ]);


        if($request->has('drinks')){

            foreach ($cont->drinks as $drink) { 
                
                $drinkstock = DrinkStock::where([
                    'drink_id' => $drink->id, 
                    'place_id' => $table->place_id
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
    
                $cost += $drinkstock->selling_price;
                $productTotal += $drink->quantity;
            }
    
        }

        $constant = SystemConstant::where('id', 1)->first();

        $totalCost = 0.0;
        
        if($request->has('foods')){

            foreach ($cont->foods as $item) {

                $menu = Menu::where('id', $item->id)->first();

                $orderItem = OrderItem::create([
                    'menu_id' => $item->id, 
                    'order_id' => $order->id, 
                    'quantity' => $item->quantity, 
                    'cost' => ($item->price - ($item->price * $menu->discount)) * $item->quantity 
                ]);
    
                $cost += $orderItem->cost;
                $totalCost += $constant->discount_active ? $item->price * $constant->discount : 0;
                $productTotal += $orderItem->quantity;
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
                    'user_id' => $cont->customer_id
                ]);

                $order->update([
                    'cost' => 0,
                    'total_cost' => $cost + $totalCost,
                    'order_number' => "TYR-".$order->id,
                    'product_total' => $productTotal
                ]);

            }

            $order->update([
                'cost' => $cost,
                'total_cost' => $cost + $totalCost,
                'order_number' => "TYR-".$order->id,
                'product_total' => $productTotal
            ]);

        } else {
            $order->update([
                'cost' => $cost,
                'total_cost' => $cost + $totalCost,
                'order_number' => "TYR-".$order->id,
                'product_total' => $productTotal
            ]);
        }

        $newOrder = Order::where('id', $order->id)->with(['food','drinks','table','place','customer'])->first();

        $place = Place::where('id', $request->place_id)->first();

        $mailController = new MailController();
        $mailController->orderRecievedMail(null, $place);

        if($place->cashier_number !== null){
            $smsController = new SMSController();
            $smsController->sendMessage(null, "An order has been made. Please check the restuarant dashboard.", $place->cashier_number);
        }

        return response()->json($newOrder, 201);
    }
}

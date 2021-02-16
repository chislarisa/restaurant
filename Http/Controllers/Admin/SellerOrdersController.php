<?php

namespace App\Http\Controllers\Admin;

use App\Food;
use App\Http\Controllers\Controller;
use App\OrderDetails;
use App\SellerOrders;
use App\SellerOrdersDetails;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = SellerOrders::where([['closed', false], ['user_id', Auth::user()->id]])->paginate(15);
        foreach ($orders as $order) {
            $order->user = User::where('id', $order->user_id)->first();
        }

        $products = Food::orderBy('name')->get();


        return view('admin.seller-orders.index')->with('active', true)->with('orders', $orders)->with('products', $products);
    }

    public function history(Request $request)
    {
        $dateFilter = null;
        if($request->dateFilter) {
            switch ($request->dateFilter) {
                case 1:
                    $orders = SellerOrders::whereDate('created_at', Carbon::today())->where([['closed', true], ['user_id', Auth::user()->id]])->paginate(15);
                    break;
                case 2:
                    $orders = SellerOrders::whereDate('created_at', '>', Carbon::now()->subDays(30))->where([['closed', true], ['user_id', Auth::user()->id]])->paginate(15);
                    break;
                case 3:
                    $orders = SellerOrders::whereDate('created_at', '>', Carbon::now()->subDays(90))->where([['closed', true], ['user_id', Auth::user()->id]])->paginate(15);
                    break;
                default:
                    break;
            }
        } else {
            $orders = SellerOrders::where([['closed', true], ['user_id', Auth::user()->id]])->paginate(15);
        }

        $total = 0;
        foreach ($orders as $order) {
            $order->user = User::where('id', $order->user_id)->first();
            $total += $order->getOrderPrice();
        }

        $products = Food::orderBy('name')->get();


        return view('admin.seller-orders.index')->with('active', false)->with('orders', $orders)->with('products', $products)->with('dateFilter', $request->dateFilter)->with('total', $total);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $sellerOrder = new SellerOrders();
        $sellerOrder->user_id = Auth::user()->id;
        $sellerOrder->table_number = $request->tableNumber;
        $sellerOrder->save();

        foreach ($request->products as $product) {
            $details = new SellerOrdersDetails();
            $details->seller_order_id = $sellerOrder->id;
            $details->food_id = $product['id'];
            $details->quantity = $product['quantity'];
            $details->save();
        }

        return true;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = SellerOrders::findOrFail($id);

        return view('admin.seller-orders.show')->with('order', $order)->with('products', Food::orderBy('name', 'desc')->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function markAsPaid($sellerOrderId) {
        SellerOrders::find($sellerOrderId)->update(['closed' => 1]);
        flash('Comandă marcată ca plătită.')->success()->important();

        return redirect()->back();
    }

    public function removeFromCart(Request $request) {
        SellerOrdersDetails::where([['seller_order_id', $request->orderId], ['food_id', $request->foodId]])->delete();
        flash('Produs scos din comandă')->success()->important();

        return true;
    }

    public function addProductToCart(Request $request) {
        $orderDetails = SellerOrdersDetails::where([['seller_order_id', $request->orderId], ['food_id', $request->product['id']]])->first();

        if ($orderDetails == null) {
            $orderDetails = new SellerOrdersDetails();
        }

        $orderDetails->seller_order_id = $request->orderId;
        $orderDetails->food_id = $request->product['id'];
        $orderDetails->quantity = $orderDetails->quantity + 1;


        return $orderDetails->save();
    }

    public function changeTableNumber(Request $request) {
        return SellerOrders::where('id', $request->orderId)->update(['table_number' => $request->val]);
    }
}

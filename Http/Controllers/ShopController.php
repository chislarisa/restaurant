<?php

namespace App\Http\Controllers;

use App\Cart;
use App\CartDetails;
use App\Command;
use App\Food;
use App\FoodCategories;
use App\Order;
use App\OrderDetails;
use App\UserBillingDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->category) {
            $foods = Food::join('foods_categories', 'foods.id', '=', 'foods_categories.food_id')
                ->select('foods.*')
                ->where('foods_categories.category_id', $request->category)
                ->orderBy('foods.name')
                ->paginate(15);
        } else {
            $foods = Food::orderBy('name')->paginate(15);
        }

        $categories = FoodCategories::orderBy('name')->get();

        return view('shop.index')->with('foods', $foods)->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show()
    {
        $cartDetails = $this->getCartDetails(true);

        if ($cartDetails == null) {
            $cartDetails = [];
        }

        return view('shop.show')->with('cart_details', $cartDetails);
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

    public function addFoodToCart($foodId, Request $request) {
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if ($cart == null) {
            $cart = new Cart();
            $cart->user_id = Auth::user()->id;
            $cart->save();
        }

        $cart_details = CartDetails::where([['cart_id', $cart->id], ['food_id', $foodId]])->first();
        if ($cart_details == null) {
            $cart_details = new CartDetails();
        }

        $cart_details->cart_id = $cart->id;
        $cart_details->food_id = $foodId;
        if ($cart_details->quantity != null) {
            $cart_details->quantity += $request->quantity;
        } else {
            $cart_details->quantity = $request->quantity;
        }
        $cart_details->save();

        return $this->getCartDetails();
    }

    public function removeFoodFromCart($foodId, Request $request) {
        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if ($cart == null) {
            return;
        }
        $cart_details = CartDetails::where([['cart_id', $cart->id], ['food_id', $foodId]])->first();
        if ($cart_details == null) {
            return;
        }

        $cart_details->delete();

        return $this->getCartDetails();
    }

    public function getCartDetails($forPhp = false) {
        $user = Auth::user();

        $cart = Cart::where('user_id', Auth::user()->id)->first();
        if ($cart == null) {
            return null;
        }

        $cart_details = CartDetails::where('cart_id', $cart->id)->get();
        if ($cart_details == null) {
            return null;
        }

        foreach ($cart_details as $details) {
            $food = Food::find($details->food_id);
            $details->foodDetails = $food;
        }

        if ($forPhp) {
            return $cart_details;
        }

        return response()->json($cart_details);
    }

    public function placeOrder() {
        if(!$this->getUserHasBillingDetails(true)) {
            return null;
        }

        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->save();

        $details = $this->getCartDetails(true);
        foreach ($details as $detail) {
            $orderDetails = new OrderDetails();
            $orderDetails->order_id = $order->id;
            $orderDetails->food_id = $detail->foodDetails->id;
            $orderDetails->quantity = $detail->quantity;
            $orderDetails->save();

            $detail->delete();
        }

        Cart::where('user_id', Auth::user()->id)->delete();

        return true;
    }

    public function getUserHasBillingDetails($forPhp = false) {
        if($forPhp) {
            return UserBillingDetails::where('user_id', Auth::user()->id)->exists();
        } else {
            return response()->json(UserBillingDetails::where('user_id', Auth::user()->id)->exists());
        }
    }

    public function setUserBillingDetails(Request $request) {
        $request->merge([
           'user_id' => Auth::user()->id,
        ]);
        UserBillingDetails::create($request->all());

        return $this->placeOrder();
    }

    public function showOrder($id) {
        $order = Order::findOrFail($id);
        return view('shop.show-order')->with('order', $order);
    }
}

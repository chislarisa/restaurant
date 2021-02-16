<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('delivered', false)->paginate(15);
        foreach ($orders as $order) {
            $order->user = User::where('id', $order->user_id)->first();
        }

        return view('admin.orders.index')->with('active', true)->with('orders', $orders);
    }

    public function ordersHistory()
    {
        $orders = Order::where('delivered', true)->paginate(15);
        foreach ($orders as $order) {
            $order->user = User::where('id', $order->user_id)->first();
        }

        return view('admin.orders.index')->with('active', false)->with('orders', $orders);
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
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $order->user = User::where('id', $order->user_id)->first();

        return view('admin/orders/show')->with('order', $order);
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

    public function markAsDelivered($id) {
        Order::find($id)->update(['delivered' => 1]);
        flash('Comandă marcată ca livrată.')->success()->important();

        return redirect()->back();
    }
}

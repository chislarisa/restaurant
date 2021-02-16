<?php

namespace App\Http\Controllers\Admin;

use App\Food;
use App\FoodCategories;
use App\FoodsCategories;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FoodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $foods = Food::orderBy('name')->paginate(15);

        return view('admin.foods.index')->with('foods', $foods);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.foods.create')->with('categories', FoodCategories::orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->categories) {
            flash('Alegeți cel puțin o categorie!')->error()->important();
            return back();
        }

        $food = new Food();
        $food->name = $request->name;
        $food->price = $request->price;
        $food->save();

        $img = $request->file('img');
        $img_extension = $img->getClientOriginalExtension();
        $img_name = $food->id . '.' . $img_extension;
        $img->move('img/foods', $img_name);

        $food->img = $img_name;
        $food->save();

        $categories = array_unique($request->categories);
        foreach ($categories as $category) {
            FoodsCategories::create(['category_id' => $category, 'food_id' => $food->id]);
        }

        flash('Fel de mancare adaugat cu succes!')->success()->important();

        return redirect('admin/foods');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = FoodCategories::orderBy('name')->get();
        $foodsCategories = FoodsCategories::where('food_id', $id)->get();

        return view('admin.foods.edit')->with('food', Food::findOrFail($id))->with('categories', $categories)->with('foodsCategories', $foodsCategories);
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
        if(!$request->categories) {
            flash('Alegeți cel puțin o categorie!')->error()->important();
            return back();
        }

        $food = Food::findOrFail($id);
        $food->name = $request->name;
        $food->price = $request->price;

        if ($request->file('img')) {
            $img = $request->file('img');
            $img_extension = $img->getClientOriginalExtension();
            $img_name = $food->id . '.' . $img_extension;
            $img->move('img/foods', $img_name);

            $food->img = $img_name;
        }

        $food->save();

        FoodsCategories::where('food_id', $id)->delete();

        $categories = array_unique($request->categories);
        foreach ($categories as $category) {
            FoodsCategories::create(['category_id' => $category, 'food_id' => $food->id]);
        }

        flash('Tip de mancare modificat cu succes!')->success()->important();

        return redirect('admin/foods');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Food::where('id', $id)->delete();
        FoodsCategories::where('food_id', $id)->delete();

        flash('Fel de mancare sters.')->success()->important();

        return back();
    }

    public function search(Request $request) {
        $foods = Food::where('id', $request->val)
                ->orWhere('name', 'like', '%' . $request->val . '%')
                ->get();

        return $foods;
    }
}

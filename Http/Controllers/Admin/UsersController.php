<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index() {
        $users = User::orderBy('name', 'desc')->paginate(15);

        return view('admin.users.index')->with('users', $users);
    }

    public function create() {
        return view('admin.users.create');
    }

    public function store(Request $request) {
        $existingUser = User::where('email', $request->email)->get();
        if ($existingUser && count($existingUser)) {
            flash('Acest email exista deja in baza de date.')->error();

            return redirect()->back()->with('errors');
        }
        $request->merge([
            'password' => Hash::make($request->password),
        ]);

        User::create($request->all());
        flash('Informatiile au fost salvate cu succes.')->success()->important();

        return redirect('/admin/users');
    }

    public function edit($id) {
        return view('admin.users.edit')->with('user', User::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $existingUser = User::where('email', $request->email)->get();
        if ($existingUser && count($existingUser) && $user->email != $request->email) {
            flash('Acest email exista deja in baza de date.')->error();

            return redirect()->back()->with('errors');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->type = $request['user-type'];
        if (trim($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        flash('Informatiile au fost salvate cu succes.')->success();

        return redirect()->back();
    }

    public function destroy($id){
        User::where('id', $id)->delete();

        flash('Utilizator sters.')->success()->important();

        return redirect()->back();
    }
}

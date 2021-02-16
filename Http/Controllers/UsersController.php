<?php

namespace App\Http\Controllers;

use App\User;
use App\UserBillingDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function accountOptions() {
        return view('user.account-options');
    }

    public function updateAccountOptions(Request $request) {
        if ($request->email != Auth::user()->email) {
            if (User::where('email', $request->email)->exists()) {
                flash('Email-ul există deja în baza de date!')->error();
                return redirect()->back();
            }
        }

        $user = Auth::user();
        $user->email = $request->email;
        $user->name = $request->name;
        if($request->password) {
            if ($request->password != $request->password_confirmation) {
                flash('Parolele nu potrivesc!')->error();
                return redirect()->back();
            }

            $user->password = Hash::make($request->password);
        }
        $user->save();

        flash('Informațiile tale au fost modificate!')->success();
        return back();
    }

    public function updateBillingInfo(Request $request) {
        if ($request->phone != Auth::user()->getBillingInfo()->phone) {
            if (UserBillingDetails::where('phone', $request->phone)->exists()) {
                return response()->json(["errors" => ['phone' => ['Numărul de telefon există deja în baza de date']]], 422);
            }
        }

        return UserBillingDetails::where('user_id', Auth::user()->id)->first()->update($request->all());
    }
}

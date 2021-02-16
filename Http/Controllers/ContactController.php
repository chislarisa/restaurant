<?php

namespace App\Http\Controllers;

use App\Mail\ClientContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index() {
        return view('contact.index');
    }

    public function send(Request $request) {
        Mail::to('uniq.flavours@gmail.com')->send(new ClientContact($request));
        flash('Mesaj trimis!')->success();

        return back();
    }
}

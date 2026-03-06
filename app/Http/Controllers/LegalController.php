<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    public function termsOfService()
    {
        return view('legal.terms-of-service');
    }

    public function refundPolicy()
    {
        return view('legal.refund-policy');
    }

    public function contact()
    {
        return view('legal.contact');
    }
}

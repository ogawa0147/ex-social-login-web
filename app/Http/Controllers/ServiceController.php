<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class ServiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function __construct() {}

    /**
     * プライバシーポリシー
     *
     * @param
     * @return View
     */
    public function policy()
    {
        return view('services.policy');
    }

    /**
     * 利用規約
     *
     * @param
     * @return View
     */
    public function terms()
    {
        return view('services.terms');
    }
}

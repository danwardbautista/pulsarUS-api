<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServicesController extends Controller
{
    //
    public function example()
    {
        return response([
            'message' => "Example API Call. Working...",
        ], 200);
    }
}

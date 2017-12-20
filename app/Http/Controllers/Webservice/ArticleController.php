<?php

namespace App\Http\Controllers\Webservice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Teenagers;

class ArticleController extends Controller
{
    public function index()
    {
    	return response()->json(Teenagers::first(), 200);
    }

    public function store(Request $request)
    {
        return Teenagers::create($request->all());
    }
}

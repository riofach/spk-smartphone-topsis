<?php

namespace App\Http\Controllers;

use App\Models\Smartphone;
use Illuminate\Http\Request;

class ListHpController extends Controller
{
    public function index()
    {
        $smartphones = Smartphone::orderBy('created_at', 'desc')->paginate(10);
        return view('list-hp.index', compact('smartphones'));
    }
}
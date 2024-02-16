<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function download($file)
    {
        $path = public_path('pdf/' . $file);

        if (Storage::exists($file)) {
            dd($path);
            return response()->download($path);
        } else {
            abort(404, 'File not found');
        }
    }
    
}

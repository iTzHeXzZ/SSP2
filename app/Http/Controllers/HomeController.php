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
        $path = 'pdf/' . $file;

        if (Storage::exists($path)) {
            return response()->download(storage_path("app/public/{$path}"));
        } else {
            abort(404, 'Datei nicht gefunden');
        }
    }
}


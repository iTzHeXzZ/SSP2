<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        
        $auftraege = $user->auftraege()
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->latest()
                          ->paginate(10);

        return view('home', [
            'auftraege' => $auftraege,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function download($file)
    {
        $path = storage_path('app/' . $file);
    
        if (Storage::exists($file)) {
            $response = response()->download($path);
    
            if (str_starts_with($file, 'temporary_')) {
                $response->deleteFileAfterSend(true);
            }
    
            return $response;
        } else {
            abort(404, 'File not found');
        }
    }
    
    
}

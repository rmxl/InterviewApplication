<?php
namespace App\Http\Controllers;

use App\Models\AssessmentRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if(!session('backendGuy')) {
            return redirect()->route('login');
        }
        $schedules = AssessmentRequest::where('backend_guy_id', session('backendGuy'))
                        ->with('timeSlot')
                        ->with('user')
                        ->with('user.job')
                        ->get();
        
        return view('dashboard', compact('schedules'));
    }
   
}
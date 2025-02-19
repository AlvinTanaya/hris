<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
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
        $users = User::where('employee_status', '!=', 'Inactive')->get();  
        
        $generasiData = User::selectRaw(
            "
            CASE 
                WHEN YEAR(birth_date) BETWEEN 1946 AND 1964 THEN 'Baby Boomer'
                WHEN YEAR(birth_date) BETWEEN 1965 AND 1980 THEN 'Gen X'
                WHEN YEAR(birth_date) BETWEEN 1981 AND 1996 THEN 'Millennial'
                WHEN YEAR(birth_date) BETWEEN 1997 AND 2012 THEN 'Gen Z'
                ELSE 'Other'
            END as generasi,
            COUNT(*) as total"
        )
            ->whereNotNull('birth_date')
            ->groupBy('generasi')
            ->orderByRaw("
            FIELD(generasi, 'Baby Boomer', 'Gen X', 'Millennial', 'Gen Z', 'Other')
        ")
            ->get();

      
        $genderData = User::selectRaw('gender, COUNT(*) as total')
            ->whereIn('gender', ['Male', 'Female']) 
            ->groupBy('gender')
            ->get();

       
        $totalUsers = User::whereNotNull('employee_status')->count();


        $avgAge = User::selectRaw('AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) as avg_age')
            ->whereNotNull('birth_date')
            ->first()
            ->avg_age;

      
        return view('dashboard', compact('generasiData', 'genderData', 'totalUsers', 'avgAge', 'users'));
    }
}

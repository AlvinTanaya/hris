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

        // Add new query for employees 55 and older
        $olderEmployees = User::selectRaw('id, name, TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) as age')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 55')
            ->where('employee_status', '!=', 'Inactive')
            ->orderBy('age', 'desc')
            ->get();

        // Add new query for employees with contracts ending within 2 months
        $contractEndingSoon = User::select('id', 'name', 'contract_end_date')
            ->whereNotNull('contract_end_date')
            ->whereRaw('contract_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH)')
            ->where('employee_status', '!=', 'Inactive')
            ->orderBy('contract_end_date')
            ->get();

        return view('dashboard', compact(
            'generasiData',
            'genderData',
            'totalUsers',
            'avgAge',
            'users',
            'olderEmployees',
            'contractEndingSoon'
        ));
    }
}

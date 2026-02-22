<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index()
    {
        $data = $this->dashboardService->getDashboardData(Auth::user());
        return view('dashboard', $data);
    }

    public function superAdmin()
    {
        $data = $this->dashboardService->getDashboardData(Auth::user());
        return view('dashboard', $data);
    }

    public function kabupaten()
    {
        $data = $this->dashboardService->getDashboardData(Auth::user());
        return view('dashboard', $data);
    }

    public function kecamatan()
    {
        $data = $this->dashboardService->getDashboardData(Auth::user());
        return view('dashboard', $data);
    }

    public function kelurahan()
    {
        $data = $this->dashboardService->getDashboardData(Auth::user());
        return view('dashboard', $data);
    }
}

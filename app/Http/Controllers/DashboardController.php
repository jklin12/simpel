<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the generic dashboard.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Display the Super Admin dashboard.
     */
    public function superAdmin()
    {
        // TODO: Load global stats
        return view('dashboard');
    }

    /**
     * Display the Kabupaten dashboard.
     */
    public function kabupaten()
    {
        // TODO: Load kabupaten specific stats
        return view('dashboard');
    }

    /**
     * Display the Kecamatan dashboard.
     */
    public function kecamatan()
    {
        // TODO: Load kecamatan specific stats
        return view('dashboard');
    }

    /**
     * Display the Kelurahan dashboard.
     */
    public function kelurahan()
    {
        // TODO: Load kelurahan specific stats
        return view('dashboard');
    }
}

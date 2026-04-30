<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index()
    {
        return $this->renderForUser();
    }

    public function superAdmin()
    {
        return $this->renderForUser();
    }

    public function kabupaten()
    {
        return $this->renderForUser();
    }

    public function kecamatan()
    {
        return $this->renderForUser();
    }

    public function kelurahan()
    {
        return $this->renderForUser();
    }

    /**
     * Pilih view dashboard sesuai role user.
     * Executive view dipakai untuk admin_kabupaten & super_admin.
     * Query params: ?month=1-12, ?year=YYYY
     */
    private function renderForUser()
    {
        $user = Auth::user();
        $month = request()->query('month');
        $year  = request()->query('year');

        $month = $month !== null ? (int) $month : null;
        $year  = $year !== null ? (int) $year : null;

        $data = $this->dashboardService->getDashboardData($user, $month, $year);

        $view = !empty($data['is_executive']) ? 'dashboard_executive' : 'dashboard';

        return view($view, $data);
    }
}

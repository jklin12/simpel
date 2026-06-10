<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapitulasiSuratExport;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportRekapitulasi(Request $request)
    {
        $user  = Auth::user();
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $data     = $this->dashboardService->getRekapitulasiPerKecamatan($user, $start, $end);
        $filename = 'rekapitulasi-surat-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';

        return Excel::download(new RekapitulasiSuratExport($data, $month, $year), $filename);
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

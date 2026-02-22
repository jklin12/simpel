<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApprovalFlowRequest;
use App\Http\Requests\UpdateApprovalFlowRequest;
use App\Services\ApprovalFlowService;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class ApprovalFlowController extends Controller
{
    protected $service;

    public function __construct(ApprovalFlowService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of approval flows.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_active']);
        $approvalFlows = $this->service->getApprovalFlowsPaginated(10, $filters);

        return view('admin.approval-flow.index', compact('approvalFlows'));
    }

    /**
     * Show the form for creating a new approval flow.
     */
    public function create()
    {
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();
        $kelurahans = Kelurahan::where('kecamatan_id', '6372010')->with('kecamatan')->orderBy('nama')->get();

        return view('admin.approval-flow.create', compact('jenisSurats', 'kelurahans'));
    }

    /**
     * Store a newly created approval flow.
     */
    public function store(StoreApprovalFlowRequest $request)
    {
        try {
            $this->service->createApprovalFlow($request->validated());

            return redirect()
                ->route('admin.approval-flow.index')
                ->with('success', 'Approval Flow berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified approval flow.
     */
    public function edit($id)
    {
        try {
            $approvalFlow = $this->service->getApprovalFlowById($id);
            $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();
            $kelurahans = Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

            return view('admin.approval-flow.edit', compact('approvalFlow', 'jenisSurats', 'kelurahans'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.approval-flow.index')
                ->with('error', 'Approval Flow tidak ditemukan');
        }
    }

    /**
     * Update the specified approval flow.
     */
    public function update(UpdateApprovalFlowRequest $request, $id)
    {
        try {
            $this->service->updateApprovalFlow($id, $request->validated());

            return redirect()
                ->route('admin.approval-flow.index')
                ->with('success', 'Approval Flow berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified approval flow.
     */
    public function destroy($id)
    {
        try {
            $this->service->deleteApprovalFlow($id);

            return redirect()
                ->route('admin.approval-flow.index')
                ->with('success', 'Approval Flow berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}

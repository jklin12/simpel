@extends('layouts.app')

@section('title', 'Detail Permohonan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('admin.permohonan-surat.index') }}" class="hover:text-blue-600">Daftar Permohonan</a>
        <span>/</span>
        <span class="text-gray-800">Detail</span>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Permohonan Surat</h1>
            <p class="text-gray-500">{{ $permohonanSurat->nomor_permohonan }}</p>
        </div>
        <div>
            @if($permohonanSurat->status == 'pending' || $permohonanSurat->status == 'in_review')
            @php
            $currentApproval = $permohonanSurat->approvals()->where('status', 'pending')->where('step_order', $permohonanSurat->current_step)->first();
            $canApprove = $currentApproval && auth()->user()->hasRole($currentApproval->target_role);
            @endphp

            @if($canApprove)
            <div class="flex gap-2">
                <button onclick="showApproveModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm">
                    Setujui
                </button>
                <button onclick="showRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-sm">
                    Tolak
                </button>
            </div>
            @endif
            @elseif($permohonanSurat->status == 'completed')
            <a href="{{ route('admin.permohonan-surat.download', $permohonanSurat->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download Surat
            </a>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Data Pemohon -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Data Pemohon</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Lengkap</label>
                    <p class="text-gray-800 font-medium">{{ $permohonanSurat->nama_pemohon }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">NIK</label>
                    <p class="text-gray-800 font-medium">{{ $permohonanSurat->nik_pemohon }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-gray-800">{{ $permohonanSurat->alamat_pemohon }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">No. Telepon</label>
                    <p class="text-gray-800">{{ $permohonanSurat->phone_pemohon ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Kelurahan</label>
                    <p class="text-gray-800">{{ $permohonanSurat->kelurahan->nama }}</p>
                </div>
            </div>
        </div>

        <!-- Data Permohonan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Data Permohonan</h2>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-500">Jenis Surat</label>
                    <p class="text-gray-800 font-medium">{{ $permohonanSurat->jenisSurat->nama }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Keperluan</label>
                    <p class="text-gray-800">{{ $permohonanSurat->keperluan }}</p>
                </div>

                @if($permohonanSurat->data_permohonan)
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <label class="text-sm font-medium text-gray-500 mb-2 block">Data Tambahan</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($permohonanSurat->data_permohonan as $key => $value)
                        <div>
                            <label class="text-xs text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                            <p class="text-sm text-gray-800">{{ $value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($permohonanSurat->catatan)
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <label class="text-sm font-medium text-gray-500">Catatan</label>
                    <p class="text-gray-800">{{ $permohonanSurat->catatan }}</p>
                </div>
                @endif

                @if($permohonanSurat->rejected_reason)
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <label class="text-sm font-medium text-red-600">Alasan Penolakan</label>
                    <p class="text-red-700 bg-red-50 p-3 rounded-lg">{{ $permohonanSurat->rejected_reason }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-3">Status Permohonan</h3>
            <div class="text-center">
                @if($permohonanSurat->status == 'pending')
                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full bg-yellow-100 text-yellow-700">Menunggu Verifikasi</span>
                @elseif($permohonanSurat->status == 'in_review')
                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full bg-blue-100 text-blue-700">Sedang Diproses</span>
                @elseif($permohonanSurat->status == 'completed')
                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full bg-green-100 text-green-700">Selesai</span>
                @elseif($permohonanSurat->status == 'rejected')
                <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full bg-red-100 text-red-700">Ditolak</span>
                @endif
            </div>

            @if($permohonanSurat->nomor_surat)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <label class="text-xs text-gray-400 block mb-1">Nomor Surat</label>
                <p class="text-lg font-bold text-green-600">{{ $permohonanSurat->nomor_surat }}</p>
            </div>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                <p>Dibuat: {{ $permohonanSurat->created_at->format('d M Y H:i') }}</p>
                @if($permohonanSurat->completed_at)
                <p>Selesai: {{ $permohonanSurat->completed_at->format('d M Y H:i') }}</p>
                @endif
            </div>
        </div>

        <!-- Approval Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-800 mb-4">Timeline Persetujuan</h3>
            <div class="space-y-4">
                @foreach($approvals as $approval)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        @if($approval->status == 'approved')
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        @elseif($approval->status == 'rejected')
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        @elseif($approval->status == 'pending')
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        @endif
                        @if(!$loop->last)
                        <div class="w-0.5 h-12 {{ $approval->status == 'approved' ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-4">
                        <p class="text-sm font-medium text-gray-800">{{ $approval->step_name }}</p>
                        <p class="text-xs text-gray-500">{{ ucwords(str_replace('_', ' ', $approval->target_role)) }}</p>
                        @if($approval->status == 'approved')
                        <p class="text-xs text-green-600 mt-1">
                            Disetujui {{ $approval->approver ? 'oleh ' . $approval->approver->name : '' }}
                            <br>{{ $approval->approved_at->format('d M Y H:i') }}
                        </p>
                        @if($approval->catatan)
                        <p class="text-xs text-gray-500 mt-1 italic">"{{ $approval->catatan }}"</p>
                        @endif
                        @elseif($approval->status == 'rejected')
                        <p class="text-xs text-red-600 mt-1">
                            Ditolak {{ $approval->approver ? 'oleh ' . $approval->approver->name : '' }}
                            <br>{{ $approval->approved_at->format('d M Y H:i') }}
                        </p>
                        @if($approval->catatan)
                        <p class="text-xs text-red-600 mt-1 italic">"{{ $approval->catatan }}"</p>
                        @endif
                        @elseif($approval->status == 'pending')
                        <p class="text-xs text-yellow-600 mt-1">Menunggu persetujuan</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Setujui Permohonan</h3>
        <form action="{{ route('admin.permohonan-surat.approve', $permohonanSurat->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">Setujui</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Tolak Permohonan</h3>
        <form action="{{ route('admin.permohonan-surat.reject', $permohonanSurat->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="rejected_reason" id="rejected_reason" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Jelaskan alasan penolakan..." required></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">Tolak</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showApproveModal() {
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.getElementById('approveModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
@endpush
@endsection
@extends('layouts.landing')

@section('title', 'Cek Status Permohonan')

@section('content')
<section class="bg-gray-50 min-h-screen pt-24 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Search Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 text-center">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Cek Status Permohonan</h1>
            <p class="text-gray-500 mb-8">Masukkan kode tracking yang Anda dapatkan saat pengajuan.</p>

            <form action="{{ route('tracking.search') }}" method="GET" class="max-w-md mx-auto">
                <div class="flex gap-2">
                    <input type="text" name="track_token" value="{{ request('track_token') }}" placeholder="Contoh: TRX12345678" class="flex-1 rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500 uppercase tracking-widest text-center font-bold" required>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        Cek
                    </button>
                </div>
                @error('track_token')
                <p class="mt-2 text-sm text-red-600 text-left">{{ $message }}</p>
                @enderror
            </form>
        </div>

        @if(isset($permohonan))
        <!-- Result -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Result -->
            <div class="bg-primary-50 p-6 border-b border-primary-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mb-2">
                        {{ $permohonan->jenisSurat->nama }}
                    </span>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-wide">{{ $permohonan->track_token }}</h2>
                    <p class="text-sm text-gray-500">Diajukan pada {{ $permohonan->created_at->isoFormat('D MMMM Y HH:mm') }}</p>
                </div>
                <div class="text-right">
                    @php
                    $statusColor = match($permohonan->status) {
                    'approved', 'completed' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'pending', 'in_review' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-gray-100 text-gray-800'
                    };
                    $statusLabel = match($permohonan->status) {
                    'approved' => 'Disetujui',
                    'completed' => 'Selesai',
                    'rejected' => 'Ditolak',
                    'pending' => 'Menunggu',
                    'in_review' => 'Sedang Diproses',
                    'draft' => 'Draft',
                    };
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="p-6 border-b border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-semibold">Nama Pemohon</p>
                    <p class="font-medium text-gray-900">{{ $permohonan->nama_pemohon }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-semibold">NIK Pemohon</p>
                    <p class="font-medium text-gray-900">{{ $permohonan->nik_pemohon }}</p>
                </div>
                <!-- Add more details if needed from JSON data -->
            </div>

            <!-- Timeline History -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Riwayat Status</h3>

                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($permohonan->approvals as $approval)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                            {{ $approval->status == 'approved' ? 'bg-green-500' : ($approval->status == 'rejected' ? 'bg-red-500' : 'bg-gray-400') }}">
                                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if($approval->status == 'approved')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                @elseif($approval->status == 'rejected')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                @endif
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $approval->step_name ?? 'Verifikasi' }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            @if($approval->updated_at != $approval->created_at)
                                            <time datetime="{{ $approval->updated_at }}">{{ $approval->updated_at->isoFormat('D MMM HH:mm') }}</time>
                                            @else
                                            <span class="text-gray-400">Menunggu</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($approval->catatan)
                                <div class="ml-11 mt-2 p-3 bg-gray-50 rounded-lg text-sm text-gray-600 border border-gray-100">
                                    <strong>Catatan:</strong> {{ $approval->catatan }}
                                </div>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
        @endif
    </div>
</section>
@endsection
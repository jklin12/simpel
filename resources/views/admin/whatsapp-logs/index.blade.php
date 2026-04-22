@extends('layouts.app')

@section('title', 'Log Notifikasi WhatsApp')

@section('content')
<div class="mb-10">
    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-[#757682] mb-4">
        <span class="text-[#191c1e]">Log Notifikasi WhatsApp</span>
    </div>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-extrabold text-[#191c1e] tracking-tight leading-tight mb-2">Log Notifikasi WhatsApp</h1>
            <p class="text-sm text-[#757682]">Pantau semua pengiriman notifikasi WhatsApp dari sistem</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-[#757682]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Total: <span class="font-bold text-[#191c1e]">{{ $logs->total() }}</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-[24px] shadow-sm p-6 border border-[#f3f4f6] mb-8">
    <form method="GET" action="{{ route('admin.whatsapp-logs.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-semibold text-[#191c1e] mb-2">Cari Nomor / Pesan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor telepon atau isi pesan..."
                    class="w-full px-4 py-2.5 border border-[#e0e0e0] rounded-xl focus:border-[#00236f] focus:ring-2 focus:ring-[#00236f]/10 transition">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-semibold text-[#191c1e] mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-[#e0e0e0] rounded-xl focus:border-[#00236f] focus:ring-2 focus:ring-[#00236f]/10 transition">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        @switch($status)
                            @case('sent')
                                Terkirim
                                @break
                            @case('failed')
                                Gagal
                                @break
                            @case('pending')
                                Pending
                                @break
                        @endswitch
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Notification Type Filter -->
            <div>
                <label class="block text-sm font-semibold text-[#191c1e] mb-2">Tipe Notifikasi</label>
                <select name="notification_type" class="w-full px-4 py-2.5 border border-[#e0e0e0] rounded-xl focus:border-[#00236f] focus:ring-2 focus:ring-[#00236f]/10 transition">
                    <option value="">Semua Tipe</option>
                    @foreach($notificationTypes as $type)
                    <option value="{{ $type }}" {{ request('notification_type') === $type ? 'selected' : '' }}>
                        @switch($type)
                            @case('created')
                                Permohonan Dibuat
                                @break
                            @case('approved')
                                Disetujui
                                @break
                            @case('rejected')
                                Ditolak
                                @break
                            @case('revisi')
                                Revisi
                                @break
                            @case('sign_request')
                                Permintaan TTD
                                @break
                        @endswitch
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-semibold text-[#191c1e] mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-2.5 border border-[#e0e0e0] rounded-xl focus:border-[#00236f] focus:ring-2 focus:ring-[#00236f]/10 transition">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-semibold text-[#191c1e] mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-4 py-2.5 border border-[#e0e0e0] rounded-xl focus:border-[#00236f] focus:ring-2 focus:ring-[#00236f]/10 transition">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-br from-[#00236f] to-[#1e3a8a] text-white rounded-xl hover:shadow-lg font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Cari
            </button>
            <a href="{{ route('admin.whatsapp-logs.index') }}" class="px-6 py-2.5 bg-white text-[#757682] border border-[#e0e0e0] rounded-xl hover:bg-[#f5f5f5] font-bold transition-all">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="bg-white rounded-[24px] shadow-sm border border-[#f3f4f6] overflow-hidden">
    @if($logs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#fafbfc] border-b border-[#f3f4f6]">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">No</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Nomor Permohonan</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Tipe</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Nomor Tujuan</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Pesan</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Percobaan</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-[11px] font-bold text-[#757682] uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f3f4f6]">
                    @foreach($logs as $index => $log)
                    <tr class="hover:bg-[#fafbfc] transition">
                        <td class="px-4 py-3 text-[12px] font-semibold text-[#191c1e]">{{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3">
                            @if($log->permohonan)
                                <a href="{{ route('admin.permohonan-surat.show', $log->permohonan->id) }}" class="text-[12px] font-bold text-[#00236f] hover:underline">
                                    {{ $log->permohonan->nomor_permohonan }}
                                </a>
                            @else
                                <span class="text-[11px] text-[#999]">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @switch($log->notification_type)
                                @case('created')
                                    <span class="px-2 py-1 rounded-md bg-[#e3f2fd] text-[#1976d2] text-[11px] font-bold">Dibuat</span>
                                    @break
                                @case('approved')
                                    <span class="px-2 py-1 rounded-md bg-[#e8f5e9] text-[#388e3c] text-[11px] font-bold">Disetujui</span>
                                    @break
                                @case('rejected')
                                    <span class="px-2 py-1 rounded-md bg-[#ffebee] text-[#d32f2f] text-[11px] font-bold">Ditolak</span>
                                    @break
                                @case('revisi')
                                    <span class="px-2 py-1 rounded-md bg-[#fff3e0] text-[#f57c00] text-[11px] font-bold">Revisi</span>
                                    @break
                                @case('sign_request')
                                    <span class="px-2 py-1 rounded-md bg-[#f3e5f5] text-[#7b1fa2] text-[11px] font-bold">TTD</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 rounded-md bg-[#f5f5f5] text-[#616161] text-[11px] font-bold">{{ ucfirst($log->notification_type) }}</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-[12px] font-mono font-semibold text-[#191c1e]">{{ $log->phone_to }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->status === 'sent')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#c8e6c9] text-[#2e7d32] text-[11px] font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Terkirim
                                </span>
                            @elseif($log->status === 'failed')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#ffcdd2] text-[#c62828] text-[11px] font-bold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    Gagal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-[#fff9c4] text-[#f57f17] text-[11px] font-bold">
                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-[11px] text-[#757682] truncate" title="{{ $log->message_preview }}">
                            {{ substr($log->message_preview, 0, 40) }}{{ strlen($log->message_preview) > 40 ? '...' : '' }}
                        </td>
                        <td class="px-4 py-3 text-[12px] font-semibold text-[#191c1e] text-center">{{ $log->attempt }}</td>
                        <td class="px-4 py-3 text-[11px] text-[#757682] nowrap">{{ $log->created_at->format('d/m/y H:i') }}</td>
                        <td class="px-4 py-3 space-y-1">
                            <div class="flex items-center gap-2">
                                @if($log->permohonan)
                                    <a href="{{ route('admin.permohonan-surat.show', $log->permohonan->id) }}" class="text-[11px] font-bold text-[#00236f] hover:underline">
                                        Lihat
                                    </a>
                                @endif
                                @if($log->status === 'failed')
                                    <form action="{{ route('admin.whatsapp-logs.retry', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Kirim ulang notifikasi WhatsApp?');">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-white bg-[#00236f] hover:bg-[#1e3a8a] px-2 py-1 rounded transition">
                                            Retry
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @if($log->error_message)
                    <tr class="bg-[#fafbfc]">
                        <td colspan="9" class="px-4 py-2">
                            <div class="text-[11px] text-[#d32f2f] font-medium">
                                <span class="font-bold">Error:</span> {{ $log->error_message }}
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-[#f3f4f6]">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-[#c5c5d3] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-[14px] text-[#757682] font-medium">Belum ada log notifikasi WhatsApp</p>
        </div>
    @endif
</div>

@endsection

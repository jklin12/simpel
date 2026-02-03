<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\SampleNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Send random sample notifications
            $user->notify(new SampleNotification(
                'Permohonan Baru Masuk',
                'Ada permohonan Surat Keterangan Domisili baru dari warga.',
                'info'
            ));

            $user->notify(new SampleNotification(
                'Approval Diperlukan',
                'Permohonan #REQ-2026-001 membutuhkan persetujuan Anda.',
                'warning'
            ));
            
             $user->notify(new SampleNotification(
                'Laporan Bulanan Siap',
                'Laporan rekapitulasi surat bulan Januari telah digenerate.',
                'success'
            ));
        }
    }
}

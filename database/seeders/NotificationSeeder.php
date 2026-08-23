<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        Notification::query()->delete();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user ditemukan, seeder notifikasi dilewati.');
            return;
        }


        foreach ($users as $user) {
            if ($user->hasRole('Lead Engineer')) {
                $samples = [
                    [
                        'title' => 'Pekerjaan Selesai',
                        'message' => 'Dimas Prakoso telah menyelesaikan tugas: "Instalasi Fiber Optik Lantai 2".',
                        'url' => route('tasks.index'),
                        'is_read' => false,
                    ],
                    [
                        'title' => 'Sertifikasi Diupload',
                        'message' => 'Fajar Nugroho mengupload dokumen sertifikasi baru untuk diverifikasi.',
                        'url' => route('users.index'), // halaman verifikasi
                        'is_read' => false,
                    ],
                    [
                        'title' => 'Task Butuh Review',
                        'message' => 'Sinta Wulandari mengubah status tugas "Konfigurasi Switch" menjadi Waiting Review.',
                        'url' => route('tasks.index'),
                        'is_read' => true,
                    ],
                ];
            } else {
                $samples = [
                    [
                        'title' => 'Tugas Baru Ditugaskan',
                        'message' => 'Anda mendapatkan tugas baru dari Team Leader.',
                        'url' => route('tasks.index'),
                        'is_read' => false,
                    ],
                    [
                        'title' => 'Sertifikasi Disetujui',
                        'message' => 'Selamat, sertifikasi keahlian Anda telah disetujui oleh Lead Engineer.',
                        'url' => route('profile.show'),
                        'is_read' => false,
                    ],
                    [
                        'title' => 'Jadwal Baru Ditambahkan',
                        'message' => 'Ada jadwal kerja baru untuk Anda minggu ini.',
                        'url' => route('schedules.index'),
                        'is_read' => true,
                    ],
                ];
            }

            foreach ($samples as $sample) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $sample['title'],
                    'message' => $sample['message'],
                    'url' => $sample['url'],
                    'is_read' => $sample['is_read'],
                ]);
            }
        }

        $this->command->info('Dummy notifikasi berhasil dibuat untuk ' . $users->count() . ' user.');
    }
}
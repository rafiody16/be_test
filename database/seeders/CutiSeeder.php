<?php

namespace Database\Seeders;

use App\Models\Cuti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::query()->where('role', 'employee')->get();

        foreach ($employees as $employee) {
            
            // Cuti 12 Hari
            if ($employee->email === 'siti@poltek.com') {
                Cuti::create([
                    'user_id' => $employee->id,
                    'start_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                    'end_date' => Carbon::now()->subMonths(2)->addDays(11)->format('Y-m-d'),
                    'reason' => 'Liburan panjang / Umroh',
                    'status' => 'approved', 
                    'attachment' => null,
                ]);
                continue; 
            }
            
            // Cuti lintas tahun
            if ($employee->email === 'beni@poltek.com') {
                Cuti::create([
                    'user_id' => $employee->id,
                    'start_date' => Carbon::create(2025, 12, 25)->format('Y-m-d'),
                    'end_date' => Carbon::create(2026, 1, 5)->format('Y-m-d'),
                    'reason' => 'Libur akhir tahun',
                    'status' => 'approved',
                    'attachment' => null,
                ]);
                continue;
            }

            // Cuti Approve (3 hari)
            Cuti::create([
                'user_id' => $employee->id,
                'start_date' => Carbon::now()->subMonth()->format('Y-m-d'),
                'end_date' => Carbon::now()->subMonth()->addDays(2)->format('Y-m-d'),
                'reason' => 'Acara keluarga di luar kota',
                'status' => 'approved',
                'attachment' => null, 
            ]);

            // Cuti Rejected (2 hari)
            Cuti::create([
                'user_id' => $employee->id,
                'start_date' => Carbon::now()->subWeeks(2)->format('Y-m-d'),
                'end_date' => Carbon::now()->subWeeks(2)->addDays(1)->format('Y-m-d'),
                'reason' => 'Istirahat (Tanpa surat dokter)',
                'status' => 'rejected',
                'attachment' => null,
            ]);

            // Cuti Pending (1 hari)
            Cuti::create([
                'user_id' => $employee->id,
                'start_date' => Carbon::now()->addWeek()->format('Y-m-d'),
                'end_date' => Carbon::now()->addWeek()->format('Y-m-d'),
                'reason' => 'Keperluan administrasi kependudukan',
                'status' => 'pending',
                'attachment' => null,
            ]);
        }
    }
}
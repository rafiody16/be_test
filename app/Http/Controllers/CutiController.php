<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function getAll() {
        $cuti = Cuti::query()->with('user')->get();

        if ($cuti->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada data cuti.',
                'cuti' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Data cuti berhasil diambil.',
            'cuti' => $cuti,
        ], 200);
    }

    public function getById($id) {
        $cuti = Cuti::query()->with('user')->find($id);

        if (!$cuti) {
            return response()->json([
                'message' => 'Data cuti tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Data cuti berhasil diambil.',
            'cuti' => $cuti,
        ], 200);
    }

    public function approveCuti($id) {
        $cuti = Cuti::query()->find($id);

        if (!$cuti) {
            return response()->json([
                'message' => 'Data cuti tidak ditemukan.',
            ], 404);
        }

        if ($cuti->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya permintaan cuti dengan status pending yang dapat disetujui.',
            ], 400);
        }

        $cuti->status = 'approved';
        $cuti->save();

        return response()->json([
            'message' => 'Permintaan cuti berhasil disetujui.',
            'cuti' => $cuti,
        ], 200);
    }

    public function rejectCuti($id) {
        $cuti = Cuti::query()->find($id);

        if (!$cuti) {
            return response()->json([
                'message' => 'Data cuti tidak ditemukan.',
            ], 404);
        }

        if ($cuti->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya permintaan cuti dengan status pending yang dapat ditolak.',
            ], 400);
        }

        $cuti->status = 'rejected';
        $cuti->save();

        return response()->json([
            'message' => 'Permintaan cuti berhasil ditolak.',
            'cuti' => $cuti,
        ], 200);
    }

    public function cekKuotaCuti(int $reqHari, int $id):bool
    {
        $approvedCuti = Cuti::query()
            ->where('user_id', $id)
            ->where('status', 'approved')
            ->whereYear('start_date', Carbon::now()->year)
            ->get();
        
        $totalHariCuti = 0;

        foreach ($approvedCuti as $cuti) {
            $totalHariCuti += Carbon::parse($cuti->start_date)->diffInDays(Carbon::parse($cuti->end_date)) + 1;
        }

        return ($totalHariCuti + $reqHari) <= User::MAX_CUTI;
    }

    public function ajukanCuti(Request $req)
    {
        $validated = $req->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $user = Auth::user();

        $hasPendingCuti = Cuti::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingCuti) {
            return response()->json([
                'message' => 'Anda sudah memiliki permintaan cuti yang sedang diproses. Harap tunggu hingga permintaan tersebut selesai.',
            ], 400);
        }

        $reqHari = Carbon::parse($req->start_date)->diffInDays(Carbon::parse($req->end_date)) + 1;

        if (!$this->cekKuotaCuti($reqHari, $user->id)) {
            return response()->json([
                'message' => 'Kuota cuti tahunan Anda tidak mencukupi untuk permintaan ini.',
            ], 400);
        }

        $attachmentPath = null;
        if ($req->hasFile('attachment')) {
            $attachmentPath = $req->file('attachment')->store('attachments', 'public');
        }

        $cuti = Cuti::query()->create([
            'user_id' => $user->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
        ]);

        return response()->json([
            'message' => 'Permintaan cuti berhasil diajukan.',
            'cuti' => $cuti,
        ], 201);
    }

    public function cekStatusCuti()
    {
        $user = Auth::user();
        $cuti = Cuti::query()->where('user_id', $user->id)->latest()->first();

        if (!$cuti) {
            return response()->json([
                'message' => 'Anda belum memiliki permintaan cuti.',
            ], 404);
        }

        return response()->json([
            'message' => 'Status cuti berhasil diambil.',
            'cuti' => $cuti,
        ], 200);
    }

    public function cancelPermintaanCuti()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Token tidak valid atau hilang.'], 401);
        }

        $cuti = Cuti::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

        if (!$cuti) {
            return response()->json([
                'message' => 'Tidak ada permintaan cuti yang sedang diproses untuk dibatalkan.',
            ], 400);
        }

        if ($cuti->attachment) {
            Storage::disk('public')->delete($cuti->attachment);
        }

        $cuti->delete();

        return response()->json([
            'message' => 'Permintaan cuti berhasil dibatalkan.',
        ], 200);
    }

    public function riwayatCuti()
    {
        $user = Auth::user();
        $cuti = Cuti::query()->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        if ($cuti->isEmpty()) {
            return response()->json([
                'message' => 'Anda belum memiliki riwayat cuti.',
                'cuti' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Riwayat cuti berhasil diambil.',
            'cuti' => $cuti,
        ], 200);
    }
}

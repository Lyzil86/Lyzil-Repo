<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('deskripsi', 'like', "%{$request->search}%")
                    ->orWhere('user_name', 'like', "%{$request->search}%")
                    ->orWhere('aksi', 'like', "%{$request->search}%");
            }))
            ->when($request->aksi, fn ($q) => $q->where('aksi', $request->aksi))
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->tanggal_dari, function ($q) use ($request) {
                try {
                    // Konversi input tanggal awal WIB ke batas UTC
                    $startUtc = Carbon::createFromFormat('Y-m-d', $request->tanggal_dari, 'Asia/Jakarta')
                        ->startOfDay()
                        ->setTimezone('UTC');
                    $q->where('created_at', '>=', $startUtc);
                } catch (\Throwable) {
                    $q->where('created_at', '>=', $request->tanggal_dari);
                }
            })
            ->when($request->tanggal_sampai, function ($q) use ($request) {
                try {
                    // Konversi input tanggal akhir WIB ke akhir hari UTC
                    $endUtc = Carbon::createFromFormat('Y-m-d', $request->tanggal_sampai, 'Asia/Jakarta')
                        ->endOfDay()
                        ->setTimezone('UTC');
                    $q->where('created_at', '<=', $endUtc);
                } catch (\Throwable) {
                    $q->where('created_at', '<=', $request->tanggal_sampai.' 23:59:59');
                }
            })
            ->orderByDesc('created_at');

        $logs = $query->paginate(20)->withQueryString();

        $aksiList = AuditLog::select('aksi')->distinct()->orderBy('aksi')->pluck('aksi');

        return view('audit-log.index', compact('logs', 'aksiList'));
    }
}

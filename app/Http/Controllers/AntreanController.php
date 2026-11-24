<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Services\BPJS\BpjsAntreanService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AntreanController extends Controller
{
    protected BpjsAntreanService $bpjsService;

    public function __construct(BpjsAntreanService $bpjsService)
    {
        $this->bpjsService = $bpjsService;
    }

    /**
     * Halaman Utama Antrean
     */
    public function index(Request $request): View
    {
        $metrics = $this->calculateMetrics();
        return view('antrean.index', compact('metrics'));
    }

    /**
     * Endpoint AJAX untuk mengambil data tabel secara partial
     */
    public function getTableData(Request $request): JsonResponse
    {
        $tab = $request->input('tab', 'today_mjkn');
        $search = $request->input('search', '');
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $perPage = $request->input('per_page', 15);

        $query = Reservasi::query();

        // Filter berdasarkan Tab
        if ($tab === 'today_mjkn') {
            $query->whereDate('TANGGALKUNJUNGAN', Carbon::today())
                  ->where('JENIS_APLIKASI', 2);
        } elseif ($tab === 'today_all') {
            $query->whereDate('TANGGALKUNJUNGAN', Carbon::today());
        } elseif ($tab === 'all') {
            if (!empty($tanggal)) {
                $query->whereDate('TANGGALKUNJUNGAN', $tanggal);
            }
        }

        // Pencarian Nama, NORM, Kode Booking (ID), atau Dokter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('NAMA', 'like', "%{$search}%")
                  ->orWhere('NORM', 'like', "%{$search}%")
                  ->orWhere('ID', 'like', "%{$search}%")
                  ->orWhere('DOKTER', 'like', "%{$search}%");
            });
        }

        // Urutan: Prioritas Status 1 (Belum Check-in) di atas, lalu berdasarkan Nomor Antrean
        $antreans = $query->orderBy('ID', 'asc')
                        // ->orderByRaw("CASE WHEN STATUS = 1 THEN 0 WHEN STATUS = 2 THEN 1 ELSE 2 END")
                        //   ->orderBy('NOMOR_ANTRIAN', 'asc')
                        //   ->orderBy('ID', 'asc')
                          ->paginate($perPage);

        $metrics = $this->calculateMetrics();

        $html = view('antrean.partials.table_rows', compact('antreans', 'tab'))->render();
        $paginationHtml = view('antrean.partials.pagination', ['paginator' => $antreans])->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'pagination' => $paginationHtml,
            'total' => $antreans->total(),
            'current_page' => $antreans->currentPage(),
            'last_page' => $antreans->lastPage(),
            'metrics' => $metrics,
        ]);
    }

    /**
     * Hit Check-in (BPJS updatewaktu jika MJKN + Update DB Lokal)
     */
    public function checkIn(Request $request, string $id): JsonResponse
    {
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => "Antrean dengan kode {$id} tidak ditemukan.",
            ], 404);
        }

        if ($reservasi->STATUS != 1) {
            return response()->json([
                'status' => false,
                'message' => "Antrean sudah memiliki status: {$reservasi->status_label}.",
            ], 422);
        }

        // Jika pasien mendaftar via Mobile JKN (JENIS_APLIKASI = 2), hit endpoint BPJS
        if ($reservasi->is_mjkn) {
            $taskId = $reservasi->task_id; // 1 = baru, 3 = lama
            $bpjsResult = $this->bpjsService->updateWaktuAntrean($reservasi->ID, $taskId);

            if (!$bpjsResult['status']) {
                $msg = $bpjsResult['metadata']['message'] ?? 'Gagal menghubungi BPJS API.';
                return response()->json([
                    'status' => false,
                    'message' => "Gagal Check-In BPJS: {$msg}",
                    'bpjs_error' => $bpjsResult,
                ], 400);
            }
        }

        // Update database lokal
        $reservasi->STATUS = 2; // Terkirim / Sudah Check-In
        $reservasi->WAKTU_CHECK_IN = Carbon::now();
        $reservasi->save();

        return response()->json([
            'status' => true,
            'message' => "Check-In berhasil untuk pasien {$reservasi->NAMA} (Kode: {$reservasi->ID}).",
            'metrics' => $this->calculateMetrics(),
        ]);
    }

    /**
     * Hit Batal Antrean (BPJS batal jika MJKN + Update DB Lokal)
     */
    public function batal(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'keterangan' => 'required|string|min:3|max:255',
        ]);

        $keterangan = $request->input('keterangan');
        $reservasi = Reservasi::find($id);

        if (!$reservasi) {
            return response()->json([
                'status' => false,
                'message' => "Antrean dengan kode {$id} tidak ditemukan.",
            ], 404);
        }

        if ($reservasi->STATUS == 0) {
            return response()->json([
                'status' => false,
                'message' => "Antrean ini sudah berstatus Batal sebelumnya.",
            ], 422);
        }

        // Jika pendaftaran via Mobile JKN, hit endpoint BPJS
        if ($reservasi->is_mjkn) {
            $bpjsResult = $this->bpjsService->batalAntrean($reservasi->ID, $keterangan);

            if (!$bpjsResult['status']) {
                $msg = $bpjsResult['metadata']['message'] ?? 'Gagal menghubungi BPJS API.';
                return response()->json([
                    'status' => false,
                    'message' => "Gagal Batal di BPJS: {$msg}",
                    'bpjs_error' => $bpjsResult,
                ], 400);
            }
        }

        // Update database lokal
        $reservasi->STATUS = 0; // Batal
        $reservasi->save();

        return response()->json([
            'status' => true,
            'message' => "Antrean {$reservasi->ID} ({$reservasi->NAMA}) berhasil dibatalkan.",
            'metrics' => $this->calculateMetrics(),
        ]);
    }

    /**
     * Hitung ringkasan metrik pasien hari ini
     */
    protected function calculateMetrics(): array
    {
        $today = Carbon::today();

        $totalToday = Reservasi::whereDate('TANGGALKUNJUNGAN', $today)->count();
        $mjknToday = Reservasi::whereDate('TANGGALKUNJUNGAN', $today)->where('JENIS_APLIKASI', 2)->count();
        $pendingToday = Reservasi::whereDate('TANGGALKUNJUNGAN', $today)->where('STATUS', 1)->count();
        $checkedInToday = Reservasi::whereDate('TANGGALKUNJUNGAN', $today)->where('STATUS', 2)->count();
        $cancelledToday = Reservasi::whereDate('TANGGALKUNJUNGAN', $today)->where('STATUS', 0)->count();

        return [
            'total_today' => $totalToday,
            'mjkn_today' => $mjknToday,
            'pending_today' => $pendingToday,
            'checked_in_today' => $checkedInToday,
            'cancelled_today' => $cancelledToday,
        ];
    }
}

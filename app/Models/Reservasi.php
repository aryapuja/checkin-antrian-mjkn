<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    protected $table = 'reservasi';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'TANGGALKUNJUNGAN',
        'TANGGAL_REF',
        'NORM',
        'NIK',
        'NAMA',
        'TEMPAT_LAHIR',
        'TANGGAL_LAHIR',
        'ALAMAT',
        'PEKERJAAN',
        'INSTANSI_ASAL',
        'JK',
        'WILAYAH',
        'PROFESI',
        'POLI',
        'POLI_BPJS',
        'REF_POLI_RUJUKAN',
        'DOKTER',
        'CARABAYAR',
        'JENIS_KUNJUNGAN',
        'NO_KARTU_BPJS',
        'CONTACT',
        'TGL_DAFTAR',
        'NO',
        'ANTRIAN_POLI',
        'NOMOR_ANTRIAN',
        'JAM',
        'JAM_PELAYANAN',
        'ESTIMASI_PELAYANAN',
        'POS_ANTRIAN',
        'JENIS',
        'JADWAL_DOKTER',
        'NO_REF_BPJS',
        'JENIS_REF_BPJS',
        'JENIS_REQUEST_BPJS',
        'POLI_EKSEKUTIF_BPJS',
        'REF',
        'SEP',
        'RAWAT_INAP',
        'JENIS_APLIKASI',
        'STATUS',
        'REF_JADWAL',
        'JAM_PRAKTEK',
        'WAKTU_CHECK_IN',
        'LOKET_RESPON',
        'UPDATE_TIME',
        'VAKSIN_KE',
    ];

    protected $casts = [
        'TANGGALKUNJUNGAN' => 'date',
        'TANGGAL_LAHIR' => 'date',
        'WAKTU_CHECK_IN' => 'datetime',
        'NORM' => 'integer',
        'STATUS' => 'integer',
        'JENIS_APLIKASI' => 'integer',
        'JENIS' => 'integer',
        'NOMOR_ANTRIAN' => 'integer',
    ];

    /**
     * Hitung usia pasien
     */
    public function getUsiaAttribute(): string
    {
        if (!$this->TANGGAL_LAHIR) {
            return '-';
        }
        return Carbon::parse($this->TANGGAL_LAHIR)->age . ' thn';
    }

    /**
     * Label status antrean
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->STATUS) {
            1,99 => 'Belum Check In',
            2 => 'Sudah Check In',
            0 => 'Batal',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Badge status color
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->STATUS) {
            1,99 => [
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-400',
                'text' => 'Belum Check In',
            ],
            2 => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-400',
                'text' => 'Sudah Check In',
            ],
            0 => [
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-400',
                'text' => 'Batal',
            ],
            default => [
                'bg' => 'bg-slate-50 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-400',
                'text' => 'Unknown',
            ],
        };
    }

    /**
     * Apakah pendaftaran lewat Mobile JKN?
     */
    public function getIsMjknAttribute(): bool
    {
        return $this->JENIS_APLIKASI === 2;
    }

    /**
     * Task ID BPJS (1 = Pasien Baru, 3 = Pasien Lama)
     */
    public function getTaskIdAttribute(): int
    {
        return $this->JENIS === 2 ? 1 : 3;
    }
}

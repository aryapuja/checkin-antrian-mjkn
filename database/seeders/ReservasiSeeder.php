<?php

namespace Database\Seeders;

use App\Models\Reservasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservasiSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            ['Ahmad Fauzi', 'L', 'Jakarta', '1985-04-12'],
            ['Siti Nurhaliza', 'P', 'Bandung', '1992-08-23'],
            ['Budi Santoso', 'L', 'Surabaya', '1978-11-05'],
            ['Dewi Lestari', 'P', 'Semarang', '1995-02-17'],
            ['Eko Prasetyo', 'L', 'Yogyakarta', '1988-06-30'],
            ['Rina Kurniawati', 'P', 'Solo', '1990-12-14'],
            ['Agus Setiawan', 'L', 'Malang', '1982-03-25'],
            ['Sri Wahyuni', 'P', 'Medan', '1975-09-08'],
            ['Hendra Wijaya', 'L', 'Palembang', '1987-07-19'],
            ['Tri Handayani', 'P', 'Makassar', '1993-01-28'],
            ['Bambang Supriyanto', 'L', 'Denpasar', '1969-05-15'],
            ['Ratna Sari', 'P', 'Bogor', '1998-10-02'],
            ['Dedi Irawan', 'L', 'Tangerang', '1984-04-09'],
            ['Nur Hidayah', 'P', 'Bekasi', '1991-07-21'],
            ['Aris Munandar', 'L', 'Depok', '1986-11-11'],
            ['Fitri Wulandari', 'P', 'Cirebon', '1994-03-03'],
            ['Joko Susilo', 'L', 'Magelang', '1980-08-16'],
            ['Endang Purwanti', 'P', 'Kediri', '1977-12-07'],
            ['Rizky Ramadhan', 'L', 'Pekalongan', '1996-09-29'],
            ['Yuliana Anggraeni', 'P', 'Purwokerto', '1989-06-18']
        ];

        $polis = [
            ['INT', 'Poli Penyakit Dalam', 'dr. Soebroto, Sp.PD'],
            ['ANA', 'Poli Anak', 'dr. Maya Indah, Sp.A'],
            ['OBG', 'Poli Kebidanan & Kandungan', 'dr. Firdaus, Sp.OG'],
            ['BED', 'Poli Bedah Umum', 'dr. Hendarto, Sp.B'],
            ['MAT', 'Poli Mata', 'dr. Anita Rahma, Sp.M'],
            ['THT', 'Poli THT-KL', 'dr. Bambang Irawan, Sp.THT'],
            ['SAR', 'Poli Saraf', 'dr. Dian Kusuma, Sp.S'],
            ['GIG', 'Poli Gigi & Mulut', 'drg. Cynthia Dewi']
        ];

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $data = [];
        for ($i = 1; $i <= 100; $i++) {
            $id = sprintf('261121%04d', $i);
            $nameData = $names[($i - 1) % count($names)];
            $nama = $nameData[0] . ($i > 20 ? ' ' . ceil($i / 20) : '');
            $jk = $nameData[1];
            $tempatLahir = $nameData[2];
            $tglLahir = $nameData[3];
            $norm = 100000 + $i;
            $nik = sprintf('320101%s%04d', str_replace('-', '', substr($tglLahir, 2)), $i);
            $noBpjs = sprintf('0001%09d', 100000000 + $i);
            $contact = sprintf('0812%08d', 10000000 + $i);

            // 70 data hari ini, 15 kemarin, 15 besok
            if ($i <= 70) {
                $tglKunjungan = $today;
            } elseif ($i <= 85) {
                $tglKunjungan = $yesterday;
            } else {
                $tglKunjungan = $tomorrow;
            }

            $poliData = $polis[$i % count($polis)];
            $poliBpjs = $poliData[0];
            $dokter = $poliData[2];
            $poliId = ($i % count($polis)) + 1;

            // 75% MJKN (2), 25% Offline (22)
            $jenisAplikasi = ($i % 4 == 0) ? 22 : 2;

            if ($tglKunjungan === $today) {
                if ($i % 10 == 0) {
                    $status = 0; // Batal
                    $checkinTime = null;
                } elseif ($i % 3 == 0) {
                    $status = 2; // Terkirim / Sudah Check In
                    $checkinTime = date('Y-m-d H:i:s', strtotime('-' . rand(10, 180) . ' minutes'));
                } else {
                    $status = 1; // Belum aksi
                    $checkinTime = null;
                }
            } else {
                $status = ($i % 5 == 0) ? 0 : 2;
                $checkinTime = ($status == 2) ? $tglKunjungan . ' 08:30:00' : null;
            }

            $jenisPasien = ($i % 6 == 0) ? 2 : 1;
            $noAntrean = ($i % 30) + 1;

            $data[] = [
                'ID' => $id,
                'TANGGALKUNJUNGAN' => $tglKunjungan,
                'NORM' => $norm,
                'NIK' => $nik,
                'NAMA' => $nama,
                'TEMPAT_LAHIR' => $tempatLahir,
                'TANGGAL_LAHIR' => $tglLahir,
                'ALAMAT' => 'Jl. Kenanga No. ' . $i . ', Kota Sejahtera',
                'JK' => $jk,
                'POLI' => $poliId,
                'POLI_BPJS' => $poliBpjs,
                'DOKTER' => $dokter,
                'CARABAYAR' => '1',
                'JENIS_KUNJUNGAN' => 1,
                'NO_KARTU_BPJS' => $noBpjs,
                'CONTACT' => $contact,
                'NOMOR_ANTRIAN' => $noAntrean,
                'JAM' => '08:00',
                'JAM_PELAYANAN' => '08:15',
                'ESTIMASI_PELAYANAN' => '08:30',
                'JENIS' => $jenisPasien,
                'NO_REF_BPJS' => '0001R004' . sprintf('%06d', $i),
                'JENIS_APLIKASI' => $jenisAplikasi,
                'STATUS' => $status,
                'JAM_PRAKTEK' => '08:00-12:00',
                'WAKTU_CHECK_IN' => $checkinTime,
            ];
        }

        Reservasi::upsert($data, ['ID']);
    }
}

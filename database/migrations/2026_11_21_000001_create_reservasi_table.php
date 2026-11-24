<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('reservasi')) {
            Schema::create('reservasi', function (Blueprint $table) {
                $table->char('ID', 10)->primary();
                $table->date('TANGGALKUNJUNGAN')->nullable()->comment('field -> tanggalperiksa BPJS');
                $table->dateTime('TANGGAL_REF')->nullable();
                $table->integer('NORM')->default(0)->comment('field -> nomorrm BPJS');
                $table->char('NIK', 16)->default('0')->comment('field -> nik BPJS');
                $table->string('NAMA', 50)->nullable();
                $table->char('TEMPAT_LAHIR', 100)->nullable();
                $table->date('TANGGAL_LAHIR')->nullable();
                $table->mediumText('ALAMAT')->nullable();
                $table->smallInteger('PEKERJAAN')->default(0);
                $table->char('INSTANSI_ASAL', 150)->default('-');
                $table->enum('JK', ['L', 'P'])->nullable();
                $table->char('WILAYAH', 10)->default('-');
                $table->smallInteger('PROFESI')->nullable();
                $table->integer('POLI')->nullable();
                $table->char('POLI_BPJS', 10)->nullable();
                $table->char('REF_POLI_RUJUKAN', 10)->nullable();
                $table->char('DOKTER', 50)->nullable();
                $table->char('CARABAYAR', 50)->default('1');
                $table->smallInteger('JENIS_KUNJUNGAN')->default(1);
                $table->char('NO_KARTU_BPJS', 15)->nullable()->comment('nomorkartu BPJS');
                $table->char('CONTACT', 50)->nullable()->comment('notelp BPJS');
                $table->timestamp('TGL_DAFTAR')->nullable()->useCurrent();
                $table->integer('NO')->nullable();
                $table->integer('ANTRIAN_POLI')->nullable();
                $table->integer('NOMOR_ANTRIAN')->nullable();
                $table->char('JAM', 50)->nullable();
                $table->char('JAM_PELAYANAN', 50)->nullable();
                $table->char('ESTIMASI_PELAYANAN', 50)->nullable();
                $table->char('POS_ANTRIAN', 10)->nullable();
                $table->integer('JENIS')->default(1)->comment('1: Pasien Lama, 2: Pasien Baru');
                $table->integer('JADWAL_DOKTER')->default(0);
                $table->char('NO_REF_BPJS', 75)->default('0');
                $table->integer('JENIS_REF_BPJS')->default(0);
                $table->integer('JENIS_REQUEST_BPJS')->default(0);
                $table->integer('POLI_EKSEKUTIF_BPJS')->default(0);
                $table->char('REF', 100)->default('0');
                $table->char('SEP', 20)->default('0');
                $table->smallInteger('RAWAT_INAP')->default(0);
                $table->integer('JENIS_APLIKASI')->default(1)->comment('2: MJKN, 22: Offline');
                $table->integer('STATUS')->default(1)->comment('1: Belum aksi, 2: Terkirim, 0: Batal');
                $table->integer('REF_JADWAL')->default(0);
                $table->char('JAM_PRAKTEK', 50)->default('0');
                $table->dateTime('WAKTU_CHECK_IN')->nullable();
                $table->integer('LOKET_RESPON')->default(0);
                $table->timestamp('UPDATE_TIME')->nullable()->useCurrentOnUpdate();
                $table->smallInteger('VAKSIN_KE')->default(0);

                $table->index('STATUS');
                $table->index('JENIS_APLIKASI');
                $table->index('TANGGALKUNJUNGAN');
                $table->index('NORM');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};

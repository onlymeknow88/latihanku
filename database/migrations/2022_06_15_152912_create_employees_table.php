<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('permit_id', 50);
            $table->string('perusahaan_id', 50);
            $table->string('nama_karyawan');
            $table->longText('alamat');
            $table->string('telp', 100);
            $table->string('email', 100);
            $table->string('goldar', 20);
            $table->string('jekel', 100);
            $table->string('tempatlahir', 100);
            $table->bigInteger('ccow_id')->nullable()->default('0');
            $table->bigInteger('contractor_id')->nullable()->default('0');
            $table->bigInteger('subContractor_id')->nullable()->default('0');
            $table->string('kec', 100);
            $table->string('kel', 100);
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->string('poh', 100);
            $table->string('originally', 100);
            $table->string('stsKawin', 100);
            $table->string('agama', 100);
            $table->string('warganegara', 100);
            $table->string('posisi', 100);
            $table->string('klarifikasijabatan', 100);
            $table->string('fungsijabatan', 100);
            $table->string('stsKontrak', 100);
            $table->string('pendidikan', 100);
            $table->string('lokasiKerja', 100);
            $table->string('roster', 100);
            $table->string('pengkategorian', 100);
            $table->string('hrga_app', 100);
            $table->string('qhse_app', 100);
            $table->string('del_app', 100);
            $table->longText('al_del');
            $table->string('ktt_app', 100);
            $table->string('tipe', 100);
            $table->string('ret_hrga', 100);
            $table->longText('not_hrga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}

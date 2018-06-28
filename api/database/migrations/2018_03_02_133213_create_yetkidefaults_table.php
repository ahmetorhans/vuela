<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateYetkidefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yetki_default', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bolum');
            $table->string('bolumAdi');
            $table->boolean('giris');
            $table->boolean('yeni');
            $table->boolean('duzelt');
            $table->boolean('sil');
        });

       
    }

   
}

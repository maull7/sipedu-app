<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_mental', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa');
            $table->decimal('nilai_mental', 5, 2)->nullable();
            $table->timestamps();
            $table->index('id_siswa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_mental');
    }
};

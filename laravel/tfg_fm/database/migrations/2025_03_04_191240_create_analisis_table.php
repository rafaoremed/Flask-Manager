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
        Schema::create('analisis', function (Blueprint $table) {
            $table->char('id', 10); 
            $table->foreign('id')->references('id')->on('muestras')->onDelete('cascade');
            $table->primary('id');
            $table->tinyInteger('coliformes')->unsigned()->nullable();
            $table->tinyInteger('e_coli')->unsigned()->nullable();
            $table->decimal('cloro', 3, 2)->unsigned()->nullable();
            $table->decimal('pH', 3, 2)->unsigned()->nullable();
            $table->decimal('turbidez', 3, 2)->unsigned()->nullable();
            $table->float('conductividad')->unsigned()->nullable();
            $table->smallInteger('dureza')->unsigned()->nullable();
            $table->tinyInteger('color')->unsigned()->nullable();
            $table->boolean('completada')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis');
    }
};

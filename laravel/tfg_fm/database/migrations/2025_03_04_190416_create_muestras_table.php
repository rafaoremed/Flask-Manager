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
        Schema::create('muestras', function (Blueprint $table) {
            $table->foreignUuid('id_cliente')->constrained('clientes');
            $table->char('id', 10)->primary();
            $table->string('direccion');
            $table->decimal('cloro_in_situ', 3, 2)->unsigned();
            $table->enum('tipo', ['TOTAL', 'FQ', 'MICRO']);
            $table->boolean('enviado')->default(false);
            $table->text('anotaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muestras');
    }
};

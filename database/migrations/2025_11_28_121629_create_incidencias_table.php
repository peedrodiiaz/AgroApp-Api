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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['abierta', 'en_progreso', 'resuelta'])->default('abierta');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->datetime('fechaApertura');
            $table->datetime('fechaCierre')->nullable();
            
            // Relaciones
            $table->foreignId('maquina_id')->constrained('maquinas')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadors')->cascadeOnDelete();
            
            $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};

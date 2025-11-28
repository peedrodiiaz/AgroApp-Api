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
        Schema::create('asignacions', function (Blueprint $table) {
            $table->id();
            $table->date('fechaInicio');
            $table->date('fechaFin')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('tipoAsignacion', ['temporal', 'permanente'])->default('temporal');
            
            // Relaciones N:N
            $table->foreignId('trabajador_id')->constrained('trabajadors')->cascadeOnDelete();
            $table->foreignId('maquina_id')->constrained('maquinas')->cascadeOnDelete();
            
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacions');
    }
};

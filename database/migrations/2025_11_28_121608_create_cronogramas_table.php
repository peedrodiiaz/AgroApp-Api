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
        Schema::create('cronogramas', function (Blueprint $table) {
        $table->id();
        $table->date('fechaInicio');
        $table->date('fechaFin');
        $table->string('color')->nullable();
        $table->text('descripcion')->nullable();
        
        // Relaciones
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
        Schema::dropIfExists('cronogramas');
    }
};

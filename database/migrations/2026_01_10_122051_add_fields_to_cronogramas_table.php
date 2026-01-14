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
        Schema::table('cronogramas', function (Blueprint $table) {
            $table->time('horaInicio')->nullable()->after('fechaInicio');
            $table->time('horaFin')->nullable()->after('fechaFin');
            $table->enum('estado', ['pendiente', 'confirmada', 'en_uso', 'completada', 'cancelada'])
                    ->default('pendiente')->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cronogramas', function (Blueprint $table) {
            $table->dropColumn(['horaInicio', 'horaFin', 'estado']);
        });
    }
    
};

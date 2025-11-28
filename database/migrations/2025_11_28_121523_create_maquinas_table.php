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
        Schema::create('maquinas', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('imagen')->nullable();
        $table->string('numSerie')->unique();
        $table->string('modelo');
        $table->string('tipo'); // Tractor, Cosechadora, Empacadora
        $table->date('fechaCompra');
        $table->enum('estado', ['activa', 'inactiva', 'mantenimiento'])->default('activa');
        $table->string('ubicacion')->nullable();
        $table->text('descripcion')->nullable();
        
        $table->integer('potenciaCv')->nullable(); 
        $table->string('tipoCombustible')->nullable(); 
        $table->integer('capacidadRemolque')->nullable(); 
        
        $table->string('tipoCultivo')->nullable(); 
        $table->string('anchoCorte')->nullable(); 
        $table->integer('capacidadTolva')->nullable(); 
        
        $table->string('tipoBala')->nullable(); 
        $table->integer('capacidadEmpaque')->nullable(); 
        
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};

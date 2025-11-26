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

            // Datos básicos
            $table->string('nombre');
            $table->string('tipo'); // Ej: Tractor, Cosechadora, Empacadora...
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->year('anio_fabricacion')->nullable();

            // Información de adquisición
            $table->date('fecha_compra')->nullable();
            $table->decimal('precio_compra', 10, 2)->nullable();

            // Estado actual de la máquina
            $table->enum('estado', ['LIBRE', 'RESERVADA', 'EN_USO', 'EN_MANTENIMIENTO'])
                ->default('LIBRE');

            // Lógica de negocio
            $table->integer('horas_uso')->default(0); // Acumuladas
            $table->integer('mantenimiento_cada_horas')->nullable(); // p. ej. 200h

            // Información opcional
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable(); // Ruta/URL en storage

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

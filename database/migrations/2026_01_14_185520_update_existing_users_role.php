<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Asignar rol 'admin' al usuario 'admin'
        DB::table('users')
            ->where('usuario', 'admin')
            ->update(['role' => 'admin']);

        // Asignar rol 'user' a los demás usuarios
        DB::table('users')
            ->where('usuario', '!=', 'admin')
            ->update(['role' => 'user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hace nada en la reversión
    }
};

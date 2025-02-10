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
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->integer('unidade');
            $table->timestamp('data_ciencia');
            $table->timestamp('data_multa');
            $table->timestamp('data_limite');
            $table->string('responsavel');
            $table->foreignId('propriedade')->constrained('propriedades')->onDelete('cascade');
            $table->foreignId('local')->constrained('propriedades')->onDelete('cascade');
            $table->integer('auto_infracao');
            $table->string('condutor');
            $table->timestamp('data_identificacao');
            $table->timestamp('data_identificacao_detran');
            $table->foreignId('status')->nullable()->constrained('statuses')->onDelete('cascade');
            $table->foreignId('status_final')->nullable()->constrained('status_finals')->onDelete('cascade');

            $table->users_actions();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multas');
    }
};

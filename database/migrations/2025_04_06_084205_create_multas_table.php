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
            $table->foreign('responsavel')->references('name')->on('users')->onDelete('cascade');
            $table->string('corresponsavel')->nullable();
            $table->foreignId('propriedade')->constrained('propriedades')->onDelete('cascade');
            $table->string('placa')->nullable();
            $table->string('auto_infracao');
            $table->unsignedBigInteger('cod_infracao');
            $table->foreign('cod_infracao')->references('cod')->on('infracoes')->onDelete('cascade');
            $table->string('condutor')->nullable();
            $table->timestamp('data_identificacao')->nullable();
            $table->string('identificador_interno')->nullable();
            $table->timestamp('data_identificacao_detran')->nullable();
            $table->string('identificador_detran')->nullable();
            $table->foreignId('status')->nullable()->constrained('statuses')->onDelete('cascade');
            $table->foreignId('status_final')->nullable()->constrained('status_finals')->onDelete('cascade');
            $table->foreignId('nao_identificacao')->nullable()->constrained('nao_identificados')->onDelete('cascade');
            $table->foreignId('nao_desconto')->nullable()->constrained('nao_descontados')->onDelete('cascade');
            $table->integer('cod_triare')->nullable();
            $table->timestamp('data_finalizada')->nullable();
            $table->string('finalizado_por')->nullable();

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

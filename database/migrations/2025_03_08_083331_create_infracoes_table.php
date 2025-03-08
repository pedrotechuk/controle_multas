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
        Schema::create('infracoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cod')->unique();
            $table->string('responsavel')->nullable();
            $table->string('infracao')->nullable();
            $table->decimal('valor', 10, 2);
            $table->string('orgao_atuador')->nullable();
            $table->string('art_ctb')->nullable();
            $table->integer('pontos')->nullable();
            $table->string('gravidade')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infracaos');
    }
};

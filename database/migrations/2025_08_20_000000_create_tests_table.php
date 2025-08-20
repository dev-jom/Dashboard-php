<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_teste');
            $table->string('numero_ticket');
            $table->text('resumo_tarefa');
            $table->string('estrutura');
            $table->string('atribuido_a');
            $table->enum('resultado', ['Aprovado', 'Reprovado', 'Validado']);
            $table->date('data_teste');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tests');
    }
};

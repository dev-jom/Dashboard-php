<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'tipo_teste',
        'numero_ticket',
        'resumo_tarefa',
        'link_tarefa',
        'estrutura',
        'atribuido_a',
        'resultado',
        'data_teste',
        'sprint'
    ];

    protected $casts = [
        'data_teste' => 'date:d/m/Y',
        'estrutura' => 'array',
    ];
}

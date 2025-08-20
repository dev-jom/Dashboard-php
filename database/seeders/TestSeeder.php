<?php

namespace Database\Seeders;

use App\Models\Test;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run()
    {
        $tests = [
            ["TESTE HOMOLOG", "20094", "Ajustar comportamento do campo Data de Homologação", "PMS", "Millene", "Validado", "14/08/2025"],
            ["TESTE HOMOLOG", "20116", "Resultado da busca sem exibir o termo pesquisado", "Acari", "Murillo", "Aprovado", "14/08/2025"],
            ["TESTE HOMOLOG", "20093", "Ajustar comportamento do campo Data de Homologação", "SINERGIA", "Millene", "Aprovado", "14/08/2025"],
            ["TESTE HOMOLOG", "20094", "Ajustar comportamento do campo Data de Homologação", "Araruama", "Millene", "Aprovado", "14/08/2025"],
            ["TESTE HOMOLOG", "20115", "Usuário com permissão de Ouvidoria não consegue responder chamados", "SINERGIA", "Murillo", "Reprovado", "15/08/2025"],
            ["TESTE HOMOLOG", "20094", "Ajustar comportamento do campo Data de Homologação", "Araruama", "Millene", "Aprovado", "15/08/2025"],
            ["TESTE HOMOLOG", "20116", "Resultado da busca sem exibir o termo pesquisado", "Acari", "Murillo", "Validado", "15/08/2025"],
            ["TESTE HOMOLOG", "20116", "Resultado da busca sem exibir o termo pesquisado", "Acari", "Murillo", "Validado", "18/08/2025"],
            ["TESTE HOMOLOG", "20094", "Ajustar comportamento do campo Data de Homologação", "Araruama", "Millene", "Validado", "18/08/2025"],
            ["TESTE HOMOLOG", "20093", "Ajustar comportamento do campo Data de Homologação", "SINERGIA", "Millene", "Validado", "18/08/2025"],
            ["TESTE HOMOLOG", "20080", "Ajustar exibição da votação secreta no telão", "TECVOTO", "Murillo", "Validado", "18/08/2025"],
            ["TESTE HOMOLOG", "20084", "Permitir configuração de votação para expediente", "TECVOTO", "Murillo", "Validado", "18/08/2025"],
            ["TESTE HOMOLOG", "20138", "Automatizar envio de dados para o telão", "TECVOTO", "Murillo", "Validado", "18/08/2025"],
            ["TESTE HOMOLOG", "20193", "Criação de API para envio de documentos de ATA e PAUTA da sessão", "TECVOTO", "Murillo", "Validado", "18/08/2025"],
            ["VALIDAÇÃO", "20081", "Ajustar renderização de imagens no PDF para exibição em 100%", "PM SOSSEGO", "Kayo", "Validado", "19/08/2025"],
            ["VALIDAÇÃO", "20095", "Página de Requerimentos não carrega corretamente", "CM RIACHO DE SANTO ANTONIO", "Jeff", "Validado", "19/08/2025"],
            ["TESTE HOMOLOG", "20126", "NavBar desalinhada e submenus sobrepostos", "PMS", "Millene", "Aprovado", "19/08/2025"]
        ];

        foreach ($tests as $test) {
            Test::updateOrCreate(
                ['numero_ticket' => $test[1]],
                [
                    'tipo_teste' => $test[0],
                    'resumo_tarefa' => $test[2],
                    'estrutura' => $test[3],
                    'atribuido_a' => $test[4],
                    'resultado' => $test[5],
                    'data_teste' => \Carbon\Carbon::createFromFormat('d/m/Y', $test[6])
                ]
            );
        }
    }
}

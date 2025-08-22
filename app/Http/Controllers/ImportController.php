<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ImportController extends Controller
{
    public function form(): View
    {
        return view('import');
    }

    public function import(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'json' => ['required', 'string'],
            'truncate' => ['sometimes', 'boolean'],
        ]);

        $json = json_decode($data['json'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            return back()->withInput()->withErrors(['json' => 'JSON inválido.']);
        }

        // Aceita tanto um array de objetos quanto um objeto com uma chave "items"
        $items = $json;
        if (isset($json['items']) && is_array($json['items'])) {
            $items = $json['items'];
        }

        if (!is_array($items)) {
            return back()->withInput()->withErrors(['json' => 'Estrutura esperada: array de objetos de teste.']);
        }

        // Função utilitária para normalizar chaves: tira acentos, espaços e símbolos, lowercase
        $normalizeKey = function (string $key): string {
            $k = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
            $k = strtolower($k);
            $k = preg_replace('/[^a-z0-9]+/i', '_', $k);
            $k = trim($k, '_');
            return $k;
        };

        // Normaliza todas as chaves de cada item antes de mapear
        $inserted = 0;

        DB::transaction(function () use ($items, $request, &$inserted, $normalizeKey) {
            if ($request->boolean('truncate')) {
                Test::truncate();
            }

            foreach ($items as $row) {
                if (!is_array($row)) continue;
                // Renomeia as chaves do item para a forma normalizada
                $norm = [];
                foreach ($row as $k => $v) {
                    $norm[$normalizeKey((string)$k)] = $v;
                }

                // Campos suportados -> mapeamento flexível (inclui variantes normalizadas)
                // Esperados por registro: tipo_teste, numero_ticket, resumo_tarefa, link_tarefa, estrutura, atribuido_a, resultado, data_teste
                $numero = $norm['numero_ticket']
                    ?? $norm['ticket']
                    ?? $norm['id']
                    ?? $norm['numero']
                    ?? $norm['numero_do_ticket']
                    ?? $norm['n_numero_do_ticket']
                    ?? null;

                $resumo = $norm['resumo_tarefa']
                    ?? $norm['resumo_da_tarefa']
                    ?? $norm['titulo']
                    ?? $norm['title']
                    ?? $norm['descricao']
                    ?? $norm['description']
                    ?? null;

                $resultado = $norm['resultado'] ?? $norm['status'] ?? null; // ex.: Aprovado, Reprovado, Validado

                $estrutura = $norm['estrutura'] ?? $norm['sistema'] ?? $norm['modulo'] ?? null;

                $atribuido = $norm['atribuido_a']
                    ?? $norm['atribuido']
                    ?? $norm['responsavel']
                    ?? $norm['owner']
                    ?? null;

                $tipo = $norm['tipo_teste'] ?? $norm['tipo'] ?? $norm['tipo_de_teste'] ?? null;

                $link = $norm['link_tarefa'] ?? $norm['link_da_tarefa'] ?? $norm['url'] ?? $norm['link'] ?? null;

                $dataTeste = $norm['data_teste'] ?? $norm['data'] ?? $norm['date'] ?? $norm['data_do_teste'] ?? null;

                // Normaliza data: aceita YYYY-MM-DD, DD/MM/YYYY, timestamp
                if ($dataTeste) {
                    try {
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dataTeste)) {
                            [$d, $m, $y] = explode('/', $dataTeste);
                            $dataTeste = sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
                        } elseif (is_numeric($dataTeste)) {
                            $dataTeste = date('Y-m-d', (int)$dataTeste);
                        }
                    } catch (\Throwable $e) {
                        $dataTeste = null;
                    }
                }

                // Validação mínima por registro: numero_ticket e resultado
                if (!$numero || !$resultado) {
                    continue;
                }

                Test::create([
                    'tipo_teste' => $tipo,
                    'numero_ticket' => (string)$numero,
                    'resumo_tarefa' => $resumo,
                    'link_tarefa' => $link,
                    'estrutura' => $estrutura,
                    'atribuido_a' => $atribuido,
                    'resultado' => $resultado,
                    'data_teste' => $dataTeste,
                ]);
                $inserted++;
            }
        });

        return back()->with('success', "Importação concluída: {$inserted} registro(s) inserido(s).");
    }
}

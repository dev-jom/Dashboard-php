<?php

namespace App\Services;

use App\Models\Test;
use Illuminate\Support\Facades\DB;

class TestImporter
{
    /**
     * Importa registros a partir de um array decodificado de JSON.
     * @param array $json Estrutura pode ser um array de objetos ou { items: [...] }
     * @param bool $truncate Zerar a tabela antes de importar
     * @return int Quantidade inserida
     */
    public static function import(array $json, bool $truncate = false): int
    {
        // Extrai items se vier embrulhado
        $items = $json;
        if (isset($json['items']) && is_array($json['items'])) {
            $items = $json['items'];
        }
        if (!is_array($items)) {
            throw new \InvalidArgumentException('Estrutura esperada: array de objetos de teste.');
        }

        $normalizeKey = function (string $key): string {
            $k = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $key);
            $k = strtolower($k);
            $k = preg_replace('/[^a-z0-9]+/i', '_', $k);
            $k = trim($k, '_');
            return $k;
        };

        $inserted = 0;
        DB::transaction(function () use ($items, $truncate, &$inserted, $normalizeKey) {
            if ($truncate) {
                Test::truncate();
            }
            foreach ($items as $row) {
                if (!is_array($row)) continue;
                $norm = [];
                foreach ($row as $k => $v) {
                    $norm[$normalizeKey((string)$k)] = $v;
                }

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

                $resultado = $norm['resultado'] ?? $norm['status'] ?? null;
                $estrutura = $norm['estrutura'] ?? $norm['sistema'] ?? $norm['modulo'] ?? null;
                $atribuido = $norm['atribuido_a'] ?? $norm['atribuido'] ?? $norm['responsavel'] ?? $norm['owner'] ?? null;
                $tipo = $norm['tipo_teste'] ?? $norm['tipo'] ?? $norm['tipo_de_teste'] ?? null;
                $link = $norm['link_tarefa'] ?? $norm['link_da_tarefa'] ?? $norm['url'] ?? $norm['link'] ?? null;
                $dataTeste = $norm['data_teste'] ?? $norm['data'] ?? $norm['date'] ?? $norm['data_do_teste'] ?? null;
                $sprint = $norm['sprint'] ?? $row['Sprint'] ?? null;

                // Log para depuração
                if (app()->environment('local')) {
                    \Log::info('Dados da sprint:', [
                        'sprint' => $sprint,
                        'has_sprint' => isset($row['Sprint']),
                        'norm_keys' => array_keys($norm),
                        'row' => $row
                    ]);
                }

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
                    'sprint' => $sprint,
                ]);
                $inserted++;
            }
        });

        return $inserted;
    }
}

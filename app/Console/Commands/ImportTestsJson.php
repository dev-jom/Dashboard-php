<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\TestImporter;

class ImportTestsJson extends Command
{
    protected $signature = 'tests:import {--file=database/seeders/data/tests.json} {--truncate}';
    protected $description = 'Importa testes a partir de um arquivo JSON local';

    public function handle(): int
    {
        $path = $this->option('file');
        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return self::FAILURE;
        }

        $content = file_get_contents($path);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('JSON inválido: ' . json_last_error_msg());
            return self::FAILURE;
        }

        $truncate = (bool)$this->option('truncate');
        $count = TestImporter::import($json, $truncate);
        $this->info("Importação concluída: {$count} registro(s) inserido(s).");
        return self::SUCCESS;
    }
}

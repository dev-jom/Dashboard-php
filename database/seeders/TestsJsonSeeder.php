<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\TestImporter;

class TestsJsonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('database/seeders/data/tests.json');
        if (!file_exists($path)) {
            $this->command?->warn('Arquivo tests.json não encontrado em database/seeders/data. Pulei a importação.');
            return;
        }
        $content = file_get_contents($path);
        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command?->error('JSON inválido no tests.json. Pulei a importação.');
            return;
        }
        // Não faz truncate por padrão; ajuste aqui se desejar
        $count = TestImporter::import($json, false);
        $this->command?->info("Seed de testes: {$count} registro(s) inserido(s).");
    }
}

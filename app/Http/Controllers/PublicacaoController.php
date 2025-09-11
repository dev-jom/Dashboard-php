<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $sprint = $request->input('sprint');

        $tests = Test::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('numero_ticket', 'like', "%{$q}%")
                        ->orWhere('resumo_tarefa', 'like', "%{$q}%")
                        ->orWhere('atribuido_a', 'like', "%{$q}%")
                        ->orWhere('estrutura', 'like', "%{$q}%");
                });
            })
            ->when($sprint, fn($query) => $query->where('sprint', $sprint))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $sprints = Test::select('sprint')
            ->whereNotNull('sprint')
            ->distinct()
            ->orderBy('sprint', 'desc')
            ->pluck('sprint')
            ->toArray();

        $tipoTesteOptions = ['Teste Homolog', 'Teste', 'Validação'];

        return view('publicacoes.index', [
            'tests' => $tests,
            'q' => $q,
            'sprints' => $sprints,
            'selectedSprint' => $sprint,
            'tipoTesteOptions' => $tipoTesteOptions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sprints = Test::select('sprint')->whereNotNull('sprint')->distinct()->orderBy('sprint', 'desc')->pluck('sprint')->toArray();
        $tipoTesteOptions = ['Teste Homolog', 'Teste', 'Validação'];
        return view('publicacoes.form', [
            'test' => new Test(),
            'sprints' => $sprints,
            'tipoTesteOptions' => $tipoTesteOptions,
            'mode' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        // normaliza estrutura: string "a, b" -> ["a","b"]
        if (isset($data['estrutura']) && is_string($data['estrutura'])) {
            $parts = array_filter(array_map('trim', explode(',', $data['estrutura'])), fn($v) => $v !== '');
            $data['estrutura'] = array_values($parts);
        }
        // permite sprint nova via campo extra
        $sprintOther = trim((string) $request->input('sprint_other'));
        if ($sprintOther !== '') {
            $data['sprint'] = $sprintOther;
        }
        Test::create($data);
        return redirect()->route('publicacoes.index')->with('success', 'Registro criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Test $publicaco)
    {
        // Route parameter is mapped to Test via parameters mapping; variable name $publicaco is intentional to avoid conflict with reserved words
        $sprints = Test::select('sprint')->whereNotNull('sprint')->distinct()->orderBy('sprint', 'desc')->pluck('sprint')->toArray();
        $tipoTesteOptions = ['Teste Homolog', 'Teste', 'Validação'];
        return view('publicacoes.form', [
            'test' => $publicaco,
            'sprints' => $sprints,
            'tipoTesteOptions' => $tipoTesteOptions,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Test $publicaco)
    {
        $data = $this->validateData($request);
        if (isset($data['estrutura']) && is_string($data['estrutura'])) {
            $parts = array_filter(array_map('trim', explode(',', $data['estrutura'])), fn($v) => $v !== '');
            $data['estrutura'] = array_values($parts);
        }
        $sprintOther = trim((string) $request->input('sprint_other'));
        if ($sprintOther !== '') {
            $data['sprint'] = $sprintOther;
        }
        $publicaco->update($data);
        return redirect()->route('publicacoes.index')->with('success', 'Registro atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $publicaco)
    {
        $publicaco->delete();
        return redirect()->route('publicacoes.index')->with('success', 'Registro removido com sucesso.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'tipo_teste' => ['required', Rule::in(['Teste Homolog', 'Teste', 'Validação'])],
            'numero_ticket' => ['required', 'string', 'max:255'],
            'resumo_tarefa' => ['required', 'string', 'max:1000'],
            'estrutura' => ['nullable'], // aceita string simples; se desejar multi-valor, pode enviar array e converter
            'atribuido_a' => ['nullable', 'string', 'max:255'],
            'resultado' => ['nullable', 'string', 'max:255'],
            'data_teste' => ['nullable', 'date'],
            'link_tarefa' => ['nullable', 'url'],
            'sprint' => ['nullable', 'string', 'max:255'],

            // Campos extras opcionais podem ser adicionados aqui; não afetarão o dashboard se não forem usados lá
        ]);
    }
}

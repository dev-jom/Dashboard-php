<?php
namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return View
     */
    public function index(): View
    {
        // Get total number of tickets
        $totalTickets = Test::count();
        
        // Calculate approval percentage
        $approvedCount = Test::whereIn('resultado', ['Aprovado', 'Validado'])->count();
        $totalResults = Test::whereIn('resultado', ['Aprovado', 'Reprovado', 'Validado'])->count();
        $approvalRate = $totalResults > 0 ? round(($approvedCount / $totalResults) * 100, 1) : 0;
        
        // Get structure counts
        $structures = [];
        Test::select('estrutura')->chunk(500, function ($chunk) use (&$structures) {
            foreach ($chunk as $row) {
                $values = $row->estrutura;
                // suporta string ou array
                $list = is_array($values) ? $values : (array) $values;
                foreach ($list as $val) {
                    if ($val === null || $val === '') continue;
                    $key = (string)$val;
                    $structures[$key] = ($structures[$key] ?? 0) + 1;
                }
            }
        });

        // Totals by resultado (status)
        $totais = Test::select('resultado')
            ->selectRaw('count(*) as total')
            ->groupBy('resultado')
            ->pluck('total', 'resultado')
            ->toArray();

        // Calculate percentage for each status
        $totalTests = array_sum($totais);
        $percentages = [];
        foreach ($totais as $status => $count) {
            $percentages[$status] = $totalTests > 0 ? round(($count / $totalTests) * 100, 1) : 0;
        }

        // Totals by responsible person
        $porPessoa = Test::select('atribuido_a')
            ->selectRaw('count(*) as total')
            ->groupBy('atribuido_a')
            ->pluck('total', 'atribuido_a')
            ->toArray();

        // Tickets grouped by status (for donut click panel)
        $validResults = ['Aprovado', 'Reprovado', 'Validado'];
        $ticketsCollection = Test::select('resultado', 'numero_ticket', 'resumo_tarefa', 'link_tarefa', 'data_teste')
            ->whereIn('resultado', $validResults)
            ->orderByDesc('data_teste')
            ->get();

        $ticketsPorStatus = $ticketsCollection
            ->groupBy('resultado')
            ->map(function ($group) {
                return $group->map(function ($t) {
                    return [
                        'id' => $t->numero_ticket,
                        'titulo' => trim($t->resumo_tarefa ?: ('Ticket ' . $t->numero_ticket)),
                        'url' => $t->link_tarefa,
                    ];
                })->values();
            })
            ->toArray();
            
        // Tickets grouped by responsible person (for bar chart click panel)
        $ticketsPorPessoa = Test::select('atribuido_a', 'numero_ticket', 'resumo_tarefa', 'link_tarefa', 'data_teste')
            ->orderByDesc('data_teste')
            ->get()
            ->groupBy('atribuido_a')
            ->map(function ($group) {
                return $group->map(function ($t) {
                    return [
                        'id' => $t->numero_ticket,
                        'titulo' => trim($t->resumo_tarefa ?: ('Ticket ' . $t->numero_ticket)),
                        'url' => $t->link_tarefa,
                    ];
                })->values();
            })
            ->toArray();

        // Tickets grouped by structure (for bar chart click panel)
        $ticketsPorEstrutura = [];
        Test::select('estrutura', 'numero_ticket', 'resumo_tarefa', 'link_tarefa', 'data_teste')
            ->orderByDesc('data_teste')
            ->chunk(500, function ($chunk) use (&$ticketsPorEstrutura) {
                foreach ($chunk as $t) {
                    $values = $t->estrutura;
                    $list = is_array($values) ? $values : (array) $values;
                    foreach ($list as $val) {
                        if ($val === null || $val === '') continue;
                        $key = (string)$val;
                        $ticketsPorEstrutura[$key] = $ticketsPorEstrutura[$key] ?? collect();
                        $ticketsPorEstrutura[$key]->push([
                            'id' => $t->numero_ticket,
                            'titulo' => trim($t->resumo_tarefa ?: ('Ticket ' . $t->numero_ticket)),
                            'url' => $t->link_tarefa,
                        ]);
                    }
                }
            });
        // Converter collections para arrays indexados
        $ticketsPorEstrutura = collect($ticketsPorEstrutura)
            ->map(fn($c) => $c->values())
            ->toArray();
            
        return view('dashboard', [
            'total_tickets' => $totalTickets,
            'percentual_aprovacao' => $approvalRate,
            'estruturas' => $structures,
            'totais' => $totais,
            'percentages' => $percentages,
            'porPessoa' => $porPessoa,
            'ticketsPorStatus' => $ticketsPorStatus,
            'ticketsPorPessoa' => $ticketsPorPessoa,
            'ticketsPorEstrutura' => $ticketsPorEstrutura,
        ]);
    }
}
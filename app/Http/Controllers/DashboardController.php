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
        $structures = Test::select('estrutura')
            ->selectRaw('count(*) as total')
            ->groupBy('estrutura')
            ->pluck('total', 'estrutura')
            ->toArray();

        // Totals by resultado (status)
        $totais = Test::select('resultado')
            ->selectRaw('count(*) as total')
            ->groupBy('resultado')
            ->pluck('total', 'resultado')
            ->toArray();

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
            
        return view('dashboard', [
            'total_tickets' => $totalTickets,
            'percentual_aprovacao' => $approvalRate,
            'estruturas' => $structures,
            'totais' => $totais,
            'porPessoa' => $porPessoa,
            'ticketsPorStatus' => $ticketsPorStatus,
        ]);
    }
}
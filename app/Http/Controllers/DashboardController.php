<?php
namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $sprint = $request->input('sprint'); // Define '147' como valor padrão
        
        // Base query
        $query = Test::query();
        
        // Apply sprint filter if provided
        if ($sprint) {
            $query->where('sprint', $sprint);
        }
        
        // Get total number of tickets
        $totalTickets = $query->count();
        
        // Calculate approval percentage
        $approvedCount = (clone $query)->whereIn('resultado', ['Aprovado', 'Validado'])->count();
        $totalResults = (clone $query)->whereIn('resultado', ['Aprovado', 'Reprovado', 'Validado'])->count();
        $approvalRate = $totalResults > 0 ? round(($approvedCount / $totalResults) * 100, 1) : 0;
        
        // Get structure counts
        $structures = [];
        $totalStructures = 0;
        $structureQuery = $sprint ? Test::where('sprint', $sprint) : new Test;
        $structureQuery->select('estrutura')->chunk(500, function ($chunk) use (&$structures, &$totalStructures) {
            foreach ($chunk as $row) {
                $values = $row->estrutura;
                // Handle both string and array structures
                $list = is_array($values) ? $values : (array) $values;
                foreach ($list as $val) {
                    if ($val === null || $val === '') continue;
                    $key = (string)$val;
                    if (!isset($structures[$key])) {
                        $totalStructures++;
                        $structures[$key] = 0;
                    }
                    $structures[$key]++;
                }
            }
        });

        // Get unique sprints for the dropdown
        $sprints = Test::select('sprint')
            ->whereNotNull('sprint')
            ->distinct()
            ->orderBy('sprint', 'desc')
            ->pluck('sprint')
            ->toArray();

        // Get testers count
        $testersCount = 0;
        $people = [];
        $ticketsPorPessoa = [];
        $testerQuery = $sprint ? Test::where('sprint', $sprint) : new Test;
        $testerQuery->select('atribuido_a')
            ->whereNotNull('atribuido_a')
            ->where('atribuido_a', '!=', '')
            ->groupBy('atribuido_a')
            ->orderBy('atribuido_a')
            ->chunk(100, function ($chunk) use (&$testersCount, &$people) {
                foreach ($chunk as $tester) {
                    if (!empty($tester->atribuido_a)) {
                        $testersCount++;
                        $people[$tester->atribuido_a] = 0;
                    }
                }
            });

        // Get test counts per person
        $testerCountsQuery = $sprint ? Test::where('sprint', $sprint) : new Test;
        $testerCountsQuery->select('atribuido_a', DB::raw('count(*) as total'))
            ->whereNotNull('atribuido_a')
            ->where('atribuido_a', '!=', '')
            ->groupBy('atribuido_a')
            ->orderBy('atribuido_a')
            ->chunk(100, function ($chunk) use (&$people) {
                foreach ($chunk as $row) {
                    if (isset($people[$row->atribuido_a])) {
                        $people[$row->atribuido_a] = $row->total;
                    }
                }
            });

        // Build tickets per person (for People chart modal)
        $ticketsPorPessoa = [];
        $peopleTicketsQuery = $sprint ? Test::where('sprint', $sprint) : new Test;
        $peopleTicketsQuery->select('atribuido_a', 'numero_ticket', 'resumo_tarefa', 'link_tarefa')
            ->whereNotNull('atribuido_a')
            ->where('atribuido_a', '!=', '')
            ->chunk(200, function ($chunk) use (&$ticketsPorPessoa) {
                foreach ($chunk as $ticket) {
                    $nome = trim($ticket->atribuido_a);
                    if ($nome === '') continue;
                    if (!isset($ticketsPorPessoa[$nome])) {
                        $ticketsPorPessoa[$nome] = [];
                    }
                    $ticketsPorPessoa[$nome][] = [
                        'id' => $ticket->numero_ticket,
                        'titulo' => $ticket->resumo_tarefa,
                        'url' => $ticket->link_tarefa,
                    ];
                }
            });

        // Totals by resultado (status)
        $totais = (clone $query)
            ->select('resultado')
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

        // Get recent tests
        $recentTests = (clone $query)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get tests by date for the chart
        $testsByDate = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Get tickets by status for the donut chart
        $ticketsPorStatus = [];
        $statusQuery = $sprint ? Test::where('sprint', $sprint) : new Test;
        $statusQuery->select('resultado', 'numero_ticket', 'resumo_tarefa', 'link_tarefa')
            ->chunk(100, function ($chunk) use (&$ticketsPorStatus) {
                foreach ($chunk as $ticket) {
                    $status = strtolower($ticket->resultado);
                    if (!isset($ticketsPorStatus[$status])) {
                        $ticketsPorStatus[$status] = [];
                    }
                    $ticketsPorStatus[$status][] = [
                        'id' => $ticket->numero_ticket,
                        'titulo' => $ticket->resumo_tarefa,
                        'url' => $ticket->link_tarefa
                    ];
                }
            });

        // Get tickets by structure for the structure chart
        $ticketsPorEstrutura = [];
        $structureTicketQuery = $sprint ? Test::where('sprint', $sprint) : new Test;
        $structureTicketQuery->select('estrutura', 'numero_ticket', 'resumo_tarefa', 'link_tarefa')
            ->chunk(200, function ($chunk) use (&$ticketsPorEstrutura) {
                foreach ($chunk as $ticket) {
                    $estruturas = is_array($ticket->estrutura) ? $ticket->estrutura : [$ticket->estrutura];
                    foreach ($estruturas as $est) {
                        if (empty($est)) continue;
                        if (!isset($ticketsPorEstrutura[$est])) {
                            $ticketsPorEstrutura[$est] = [];
                        }
                        $ticketsPorEstrutura[$est][] = [
                            'id' => $ticket->numero_ticket,
                            'titulo' => $ticket->resumo_tarefa,
                            'url' => $ticket->link_tarefa
                        ];
                    }
                }
            });

        return view('dashboard', [
            'totalTickets' => $totalTickets,
            'totalStructures' => $totalStructures,
            'testersCount' => $testersCount,
            'approvalRate' => $approvalRate,
            'structures' => $structures,
            'percentages' => $percentages,
            'recentTests' => $recentTests,
            'testsByDate' => $testsByDate,
            'sprints' => $sprints,
            'selectedSprint' => $sprint,
            'people' => $people,
            'ticketsPorPessoa' => $ticketsPorPessoa,
            'ticketsPorStatus' => $ticketsPorStatus ?? [],
            'ticketsPorEstrutura' => $ticketsPorEstrutura ?? [],
        ]);
    }
}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Testes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">TASH</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle custom-drop" href="#" id="sprintDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ $selectedSprint ?? 'Selecione uma Sprint' }}
          </a>
          <ul class="dropdown-menu" aria-labelledby="sprintDropdown">
            <li><a class="dropdown-item {{ !$selectedSprint ? 'active' : '' }}" href="{{ route('dashboard') }}">Todas as Sprints</a></li>
            @foreach($sprints as $sprintOption)
            <li><hr class="dropdown-divider"></li>  
            <li>
                <a class="dropdown-item {{ ($selectedSprint ?? '147') == $sprintOption ? 'active' : '' }}" 
                   href="{{ route('dashboard', ['sprint' => $sprintOption]) }}">
                  {{ $sprintOption }}
                </a>
              </li>
            @endforeach
          </ul>
        </li>
      </ul>
    </div>
  </div>
  <div>
    <a class="AddData btn"
   href="{{ route('publicacoes.index') }}"
   title="Ir para Publicações">
  <i class="fa-solid fa-database"></i>
</a> 
  </div>
</nav>



<h1 class="text-center py-3 text-white">
    @if($selectedSprint)
        Dashboard de Testes da {{ $selectedSprint }}
    @else
        Dashboard de Testes - Todas as Sprints
    @endif
</h1>
    <div class="container-fluid">
        <div class="row mb-4">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ticket-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total de Tickets</span>
                <span class="info-box-number">{{ $totalTickets }}</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Taxa de Aprovação</span>
                <span class="info-box-number">{{ $approvalRate }}%</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-sitemap"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total de Estruturas</span>
                <span class="info-box-number">{{ $totalStructures ?? 0 }}</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger  elevation-1"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Quantidade de Devs</span>
                <span class="info-box-number">{{ $testersCount ?? 0 }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card align-items-center">
                  <div class="card-header w-100 justify-content-between align-items-center text-white">
                    <h3 class="card-title mb-0 d-flex justify-content-center">STATUS DOS TESTES</h3>
                  </div>
                  <div class="card-body w-100">
                    <div class="row g-3 align-items-start">
                      <div id="donutCol" class="col-12 d-flex flex-column align-items-center">
                        <canvas id="donutChart" style="min-height: 250px;height: 400px;max-height: 400px;max-width: 896px;display: block;width: 100%;box-sizing: border-box;" height="400"></canvas>
                        <div id="donutLegend" class="chart-legend mt-2 align-self-start"></div>
                      </div>
                      <div id="ticketCol" class="col-lg-5 col-md-5 col-sm-12 d-none">
                        <div id="ticketPanel" class="border rounded p-2 d-none">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Detalhes por Status</h5>
                            <button type="button" id="closeTicketPanel" class="btn btn-sm btn-outline-secondary">Fechar</button>
                          </div>
                          <div id="ticketPanelBody">
                            <p class="text-muted mb-0">Clique em um segmento do gráfico para ver os tickets.</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
             </div>
             <div class="col-md-6">
                 <div class="chart-container">
                     <canvas id="graficoPessoas"></canvas>
                 </div>
                 <div id="ticketPanelPessoas" class="ticket-panel border rounded p-2 d-none mt-2">
                   <div class="d-flex justify-content-between align-items-center mb-2">
                     <h5 class="mb-0">Detalhes por Responsável</h5>
                     <button type="button" id="closeTicketPanelPessoas" class="btn btn-sm btn-outline-secondary">Fechar</button>
                   </div>
                   <div id="ticketPanelBodyPessoas" class="ticket-panel-body">
                     <p class="text-muted mb-0">Clique em uma barra do gráfico para ver os tickets.</p>
                   </div>
                 </div>
             </div>
         </div>
        <div class="row">
            <div class="col-md-12">
                <div class="chart-container chart-container-estruturas mb-4">
                    <canvas id="graficoEstruturas"></canvas>
                </div>
                <div id="ticketPanelEstruturas" class="ticket-panel border rounded p-2 d-none mt-2">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Detalhes por Estrutura</h5>
                    <button type="button" id="closeTicketPanelEstruturas" class="btn btn-sm btn-outline-secondary">Fechar</button>
                  </div>
                  <div id="ticketPanelBodyEstruturas" class="ticket-panel-body">
                    <p class="text-muted mb-0">Clique em uma barra do gráfico para ver os tickets.</p>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Tickets por Status -->
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="ticketModalLabel">Detalhes por Status</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="ticketModalBody">
            <!-- Populado via JS -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dados auxiliares vindos do backend
            const ticketsPorStatus = {!! json_encode($ticketsPorStatus ?? []) !!}; // { aprovado: [{id,titulo,url}], reprovado: [...], validado: [...] }
            const statusColors = {
              'Reprovado': '#d30019',
              'Validado': '#0500FF',
              'Aprovado': '#02791e'
            };

            // Percentuais (com fallback vazio caso não exista a variável no backend)
            const donutLabels = {!! json_encode(array_keys($percentages ?? [])) !!};
            const donutPercents = {!! json_encode(array_values($percentages ?? [])) !!};

            // Tickets auxiliares para os outros gráficos
            const ticketsPorPessoa = {!! json_encode($ticketsPorPessoa ?? []) !!}; // { "Fulano": [{id,titulo,url}], ... }
            const ticketsPorEstrutura = {!! json_encode($ticketsPorEstrutura ?? []) !!}; // { "ERP": [{...}], ... }

            // Donut Chart
            const donutCtx = document.getElementById('donutChart');
            if (donutCtx) {
                const donutChart = new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: donutLabels,
                        datasets: [{
                            data: donutPercents,
                            backgroundColor: donutLabels.map(l => ({
                              'Reprovado': '#d30019',
                              'Validado': '#0500FF',
                              'Aprovado': '#02791e'
                            })[l] || 'rgba(200,200,200,0.7)'),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: { display: false },
                            tooltip: {
                                callbacks: {
                                  label: function(context) {
                                    const val = context.formattedValue ?? context.parsed;
                                    return `${val}%`;
                                  }
                                }
                              }
                        }
                    }
                });

                // Função ao clicar gerar modal
                donutCtx.onclick = function(evt) {
                  const points = donutChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                  if (!points || !points.length) return;
                  const idx = points[0].index;
                  const statusLabel = donutChart.data.labels[idx]; 
                  const key = (statusLabel || '').toLowerCase();
                  const tickets = (ticketsPorStatus && ticketsPorStatus[key]) ? ticketsPorStatus[key] : [];
                  if (!tickets.length) return;
                  // Lista no modal
                  const modalTitle = document.getElementById('ticketModalLabel');
                  const modalBody = document.getElementById('ticketModalBody');
                  modalTitle.textContent = `Tickets - ${statusLabel}`;
                  modalBody.innerHTML = tickets.map(t => `
                    <div class="d-flex align-items-start mb-2">
                      <span class="badge me-2" style="background-color:${statusColors[statusLabel] || '#6c757d'};color:#fff;">${statusLabel}</span>
                      <a href="${t.url}" target="_blank" rel="noopener" class="link-light text-decoration-underline">
                        #${t.id} - ${t.titulo}
                      </a>
                    </div>
                  `).join('');
                  const bsModal = new bootstrap.Modal(document.getElementById('ticketModal'));
                  bsModal.show();
                };

                // Popula a legenda customizada com percentual e contagem
                const legendEl = document.getElementById('donutLegend');
                if (legendEl) {
                  const labels = donutChart.data.labels;
                  const percents = donutChart.data.datasets[0].data;
                  const html = labels.map((label, i) => {
                    const key = (label || '').toLowerCase();
                    const count = (ticketsPorStatus && ticketsPorStatus[key]) ? ticketsPorStatus[key].length : 0;
                    const color = donutChart.data.datasets[0].backgroundColor[i];
                    return `
                      <div class="legend-item" data-index="${i}" style="cursor:pointer;">
                        <span class="legend-box" style="background:${color}"></span>
                        <span>${label}: ${percents[i]}% (${count})</span>
                      </div>`;
                  }).join('');
                  legendEl.innerHTML = html;

                  // Torna a legenda clicável para (des)ativar segmentos APENAS deste donut
                  legendEl.querySelectorAll('.legend-item').forEach(el => {
                    el.addEventListener('click', () => {
                      const idx = Number(el.getAttribute('data-index'));
                      // Alterna visibilidade do segmento
                      const currentlyVisible = donutChart.getDataVisibility(idx);
                      donutChart.toggleDataVisibility(idx);
                      donutChart.update();
                      // Opcional: ajustar opacidade visual da legenda ao desativar
                      el.style.opacity = currentlyVisible ? 0.4 : 1;
                    });
                  });
                }
            }

            // People Chart
            const peopleCtx = document.getElementById('graficoPessoas');
            if (peopleCtx) {
                const peopleLabels = {!! json_encode(array_keys($people)) !!};
                const peopleData = {!! json_encode(array_values($people)) !!};
                const peopleChart = new Chart(peopleCtx, {
                    type: 'bar',
                    data: {
                        labels: peopleLabels,
                        datasets: [{
                            label: 'Testes por Pessoa',
                            data: peopleData,
                            backgroundColor: 'rgb(4, 0, 230)',
                            borderColor: 'rgb(4, 0, 230)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#fff'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#fff'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Testes por Pessoa',
                                color: '#fff',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const val = context.parsed.y ?? context.parsed;
                                        const data = context.chart.data.datasets[0].data || [];
                                        const total = data.reduce((a,b) => a + (Number(b)||0), 0);
                                        const pct = total > 0 ? ((val/total)*100).toFixed(1) : 0;
                                        return `${val} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Clique na barra -> abre modal com lista de tickets da pessoa
                peopleCtx.onclick = function(evt) {
                  // Usa 'index' e intersect: false para facilitar o clique em qualquer ponto da barra
                  const points = peopleChart.getElementsAtEventForMode(evt, 'index', { intersect: false }, true);
                  if (!points || !points.length) return;
                  const idx = points[0].index;
                  const label = (peopleChart.data.labels[idx] || '').trim();
                  const tickets = (ticketsPorPessoa && ticketsPorPessoa[label]) ? ticketsPorPessoa[label] : [];
                  if (!tickets.length) return;
                  const modalTitle = document.getElementById('ticketModalLabel');
                  const modalBody = document.getElementById('ticketModalBody');
                  modalTitle.textContent = `Tickets - ${label}`;
                  modalBody.innerHTML = tickets.map(t => `
                    <div class="d-flex align-items-start mb-2">
                      <span class="badge me-2" style="background-color:rgb(4, 0, 230);color:#fff;">Dev</span>
                      <a href="${t.url}" target="_blank" rel="noopener" class="link-light text-decoration-underline">
                        #${t.id} - ${t.titulo}
                      </a>
                    </div>
                  `).join('');
                  const bsModal = new bootstrap.Modal(document.getElementById('ticketModal'));
                  bsModal.show();
                };
            }

            // Structures Chart
            const structureCtx = document.getElementById('graficoEstruturas');
            if (structureCtx) {
                const estruturaLabels = {!! json_encode(array_keys($structures)) !!};
                const estruturaData = {!! json_encode(array_values($structures)) !!};
                const estruturasChart = new Chart(structureCtx, {
                    type: 'bar',
                    data: {
                        labels: estruturaLabels,
                        datasets: [{
                            label: 'Testes por Estrutura',
                            data: estruturaData,
                            backgroundColor: 'rgb(4, 0, 230)',
                            borderColor: 'rgb(4, 0, 230)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#fff'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            y: {
                                ticks: {
                                    color: '#fff'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Testes por Estrutura',
                                color: '#fff',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const val = context.parsed.x ?? context.parsed;
                                        const data = context.chart.data.datasets[0].data || [];
                                        const total = data.reduce((a,b) => a + (Number(b)||0), 0);
                                        const pct = total > 0 ? ((val/total)*100).toFixed(1) : 0;
                                        return `${val} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Clique na barra -> abre modal com lista de tickets da estrutura
                structureCtx.onclick = function(evt) {
                  const points = estruturasChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                  if (!points || !points.length) return;
                  const idx = points[0].index;
                  const label = estruturasChart.data.labels[idx];
                  const tickets = (ticketsPorEstrutura && ticketsPorEstrutura[label]) ? ticketsPorEstrutura[label] : [];
                  if (!tickets.length) return;
                  const modalTitle = document.getElementById('ticketModalLabel');
                  const modalBody = document.getElementById('ticketModalBody');
                  modalTitle.textContent = `Tickets - ${label}`;
                  modalBody.innerHTML = tickets.map(t => `
                    <div class=\"d-flex align-items-start mb-2\">
                      <span class=\"badge me-2\" style=\"background-color:rgb(4, 0, 230);color:#fff;\">Estrutura</span>
                      <a href=\"${t.url}\" target=\"_blank\" rel=\"noopener\" class=\"link-light text-decoration-underline\">
                        #${t.id} - ${t.titulo}
                      </a>
                    </div>
                  `).join('');
                  const bsModal = new bootstrap.Modal(document.getElementById('ticketModal'));
                  bsModal.show();
                };
            }
        });
    </script>
    <footer class="footer mt-4 py-3">
      <div class="container text-center">
        <span class="text-muted d-block d-md-inline">  {{ date('Y') }} Dashboard de Testes</span>
        <span class="text-muted d-none d-md-inline"> • </span>
        <span class="text-muted d-block d-md-inline">Feito por 
          <a href="https://github.com/dev-jom" target="_blank" rel="noopener" class="footer-link">Jonathas</a>
        </span> <br>
        <span>Versão 1.3.0</span>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sprint 145</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h1 class="text-center py-3 text-white">Dashboard de Testes da Sprint 146</h1>
    <div class="container-fluid">
        <div class="row mb-4">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ticket-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total de Tickets</span>
                <span class="info-box-number">{{ $total_tickets }}</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Taxa de Aprovação</span>
                <span class="info-box-number">{{ number_format($percentual_aprovacao, 1) }}<small>%</small></span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-layer-group"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total de Estruturas</span>
                <span class="info-box-number">{{ count($estruturas ?? []) }}</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Quantidade de Testadores</span>
                <span class="info-box-number">{{ count($porPessoa ?? []) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card align-items-center">
                  <div class="card-header w-100 justify-content-between align-items-center">
                    <h3 class="card-title mb-0 d-flex justify-content-center">STATUS DOS TESTES</h3>
                  </div>
                  <div class="card-body w-100">
                    <div class="row g-3 align-items-start">
                      <div id="donutCol" class="col-12 d-flex justify-content-center">
                        <canvas id="donutChart" style="min-height: 250px;height: 400px;max-height: 400px;max-width: 896px;display: block;width: 100%;box-sizing: border-box;" height="400"></canvas>
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
             </div>
         </div>
        <div class="row">
            <div class="col-md-12">
                <div class="chart-container">
                    <canvas id="graficoEstruturas"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        const root = getComputedStyle(document.documentElement);
        const colors = {
            aprovado: root.getPropertyValue('--cor-aprovado').trim(),
            reprovado: root.getPropertyValue('--cor-reprovado').trim(),
            validado: root.getPropertyValue('--cor-validado').trim(),
        };
        // Dados de tickets por status vindos do backend (opcional)
        const ticketsRaw = {!! json_encode($ticketsPorStatus ?? []) !!};
        const ticketsByStatus = Object.fromEntries(
          Object.entries(ticketsRaw).map(([k, v]) => [String(k).toLowerCase(), v])
        );
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($totais ?? [])) !!},
                datasets: [{
                    data: {!! json_encode(array_values($totais ?? [])) !!},
                    backgroundColor: [
                        colors.aprovado,
                        colors.validado, 
                        colors.reprovado,
                    ]
                }]
            },
            options: {
                plugins: {
                    title: { display: true, font: { size: 16 } },
                    legend: { position: 'bottom'}
                }
            }
        });
        // Clique no donut para abrir/atualizar painel (na mesma card)
        (function(){
          const donutCanvas = document.getElementById('donutChart');
          const ticketPanel = document.getElementById('ticketPanel');
          const ticketPanelBody = document.getElementById('ticketPanelBody');
          const closeTicketPanel = document.getElementById('closeTicketPanel');
          const donutCol = document.getElementById('donutCol');
          const ticketCol = document.getElementById('ticketCol');
          if (!donutCanvas || !ticketPanel) return;
          donutCanvas.addEventListener('click', function(evt) {
            const chart = Chart.getChart(donutCanvas);
            if (!chart) return;
            const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (!points.length) return;
            const idx = points[0].index;
            const label = (chart.data.labels?.[idx] ?? '').toString();
            const items = ticketsByStatus[String(label).toLowerCase()] ?? [];
            if (!items.length) {
              ticketPanelBody.innerHTML = `<p class="text-muted mb-0">Sem tickets para "${label}".</p>`;
            } else {
              const html = items.map(raw => {
                const isStr = typeof raw === 'string';
                const item = isStr ? { titulo: raw } : raw;
                const title = item.titulo || item.title || item.nome || item.descricao || (isStr ? raw : `Ticket ${item.id ?? ''}`);
                let code = item.id || item.numero || item.num || item.codigo || item.code || item.chave || item.key;
                if (!code && isStr) {
                  const m = String(raw).match(/([A-Za-z]{2,}-?\d+|\b\d{2,}\b)/);
                  if (m) code = m[1];
                }
                const badgeInner = code ? `#${code}` : '';
                const badge = code
                  ? (item.url
                      ? `<a href="${item.url}" target="_blank" rel="noopener" class="text-decoration-none"><span class="badge bg-secondary">${badgeInner}</span></a>`
                      : `<span class="badge bg-secondary">${badgeInner}</span>`)
                  : '';
                // Title (resumo) não é clicável; somente o badge
                return `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${title}</span>${badge}</li>`;
              }).join('');
              ticketPanelBody.innerHTML = `<h6 class="mb-2">Tickets: ${label} <span class=\"text-muted\">(${items.length})</span></h6><ul class="list-group list-group-flush">${html}</ul>`;
            }
            // Mostrar coluna de tickets e reduzir coluna do donut
            ticketCol?.classList.remove('d-none');
            donutCol?.classList.remove('col-12');
            donutCol?.classList.add('col-lg-7','col-md-7','col-sm-12');
            ticketPanel.classList.remove('d-none');
          });
          closeTicketPanel?.addEventListener('click', () => {
            ticketPanel.classList.add('d-none');
            // Esconder coluna de tickets e centralizar novamente o donut
            ticketCol?.classList.add('d-none');
            donutCol?.classList.remove('col-lg-7','col-md-7','col-sm-12');
            donutCol?.classList.add('col-12');
          });
        })();
        new Chart(document.getElementById('graficoPessoas'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($porPessoa ?? [])) !!},
                datasets: [{
                    label: 'Testes Realizados',
                    data: {!! json_encode(array_values($porPessoa ?? [])) !!},
                    backgroundColor: colors.responsavel,
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    title: { display: true, text: 'Testes por Responsável', font: { size: 16 } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
        new Chart(document.getElementById('graficoEstruturas'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($estruturas ?? [])) !!},
                datasets: [{
                    label: 'Quantidade de Testes',
                    data: {!! json_encode(array_values($estruturas ?? [])) !!},
                    backgroundColor: colors.estrutura,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    title: { display: true, text: 'Distribuição por Estrutura', font: { size: 16 } }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
</body>
</html>

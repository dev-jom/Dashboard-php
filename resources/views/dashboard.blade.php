<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Testes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <span class="info-box-text">Quantidade de Devs</span>
                <span class="info-box-number">{{ count($porPessoa ?? []) }}</span>
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
                <div class="chart-container">
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
    <script>
        const root = getComputedStyle(document.documentElement);
        const colors = {
            aprovado: root.getPropertyValue('--cor-aprovado').trim(),
            reprovado: root.getPropertyValue('--cor-reprovado').trim(),
            validado: root.getPropertyValue('--cor-validado').trim(),
            // Novas cores controladas por CSS para gráficos de barras
            responsavel: (root.getPropertyValue('--cor-responsavel') || '').trim() || '#4e79a7',
            responsavelHover: (root.getPropertyValue('--cor-responsavel-hover') || '').trim() || '#5a86ba',
            responsavelBorder: (root.getPropertyValue('--cor-responsavel-borda') || '').trim() || '#335b8e',
            estrutura: (root.getPropertyValue('--cor-estrutura') || '').trim() || '#f28e2b',
        };
        // Cores das linhas (grades e bordas dos eixos)
        const gridColor = (root.getPropertyValue('--chart-grid-color') || '').trim() || 'rgba(255,255,255,0.2)';
        const axisBorderColor = (root.getPropertyValue('--chart-axis-border-color') || '').trim() || 'rgba(255,255,255,0.5)';
        // Cor padrão para textos/legendas dos gráficos
        const legendColor = '#ffffff';
        if (window.Chart && Chart.defaults) {
            Chart.defaults.color = legendColor;
        }
        // Dados de tickets por status vindos do backend
        const ticketsRaw = {!! json_encode($ticketsPorStatus ?? []) !!};
        const ticketsByStatus = Object.fromEntries(
          Object.entries(ticketsRaw).map(([k, v]) => [String(k).toLowerCase(), v])
        );
        
        // Dados para o gráfico de status
        const statusLabels = {!! json_encode(array_keys($totais ?? [])) !!};
        const statusCounts = {!! json_encode(array_values($totais ?? [])) !!};
        const statusPercentages = {!! json_encode($percentages ?? []) !!};
        const totalTests = statusCounts.reduce((a, b) => a + b, 0);
        
        // Labels limpos para os gráficos
        const labelsWithPercentages = [...statusLabels];
        
        // Dados para o gráfico de desenvolvedores
        const pessoasData = {!! json_encode($porPessoa ?? []) !!};
        const pessoasLabels = Object.keys(pessoasData);
        const pessoasCounts = Object.values(pessoasData);
        const pessoasPercentages = pessoasLabels.map((_, index) => {
            const count = pessoasCounts[index];
            return totalTests > 0 ? Math.round((count / totalTests) * 100) : 0;
        });
        
        // Dados para o gráfico de estruturas
        const estruturasData = {!! json_encode($estruturas ?? []) !!};
        const estruturasLabels = Object.keys(estruturasData);
        const estruturasCounts = Object.values(estruturasData);
        const estruturasPercentages = estruturasLabels.map((_, index) => {
            const count = estruturasCounts[index];
            return totalTests > 0 ? Math.round((count / totalTests) * 100) : 0;
        });
        
        // Dados de tickets por responsável e por estrutura
        const ticketsByPessoa = {!! json_encode($ticketsPorPessoa ?? []) !!};
        const ticketsByEstrutura = {!! json_encode($ticketsPorEstrutura ?? []) !!};
        // Configuração do gráfico de status
        const donutChart = new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
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
                    legend: { 
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} testes (${percentage}%)`;
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        bottom: 30 // Reduzido o espaço para a legenda personalizada
                    }
                },
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const label = statusLabels[index];
                        const items = ticketsByStatus[label.toLowerCase()] || [];
                        const panel = document.getElementById('ticketPanel');
                        const panelBody = document.getElementById('ticketPanelBody');
                        
                        if (items.length) {
                            const html = items.map(item => {
                                const title = item.titulo || item.title || item.nome || item.descricao || `Ticket ${item.id ?? ''}`;
                                const code = item.id || item.numero || item.num || item.codigo || item.code || item.chave || item.key;
                                const badgeInner = code ? `#${code}` : '';
                                const badge = code
                                    ? (item.url
                                        ? `<a href="${item.url}" target="_blank" rel="noopener" class="text-decoration-none"><span class="badge bg-secondary">${badgeInner}</span></a>`
                                        : `<span class="badge bg-secondary">${badgeInner}</span>`)
                                    : '';
                                return `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${title}</span>${badge}</li>`;
                            }).join('');
                            
                            const total = statusCounts[index] || 0;
                            const percentage = totalTests > 0 ? Math.round((total / totalTests) * 100) : 0;
                            
                            panelBody.innerHTML = `<h6 class="mb-2">${label} <span class="text-muted">(${total} testes - ${percentage}% do total)</span></h6><ul class="list-group list-group-flush">${html}</ul>`;
                            panel.classList.remove('d-none');
                        }
                    }
                }
            }
        });

        // Cria a legenda personalizada
        function createCustomLegend() {
            const legendContainer = document.getElementById('donutLegend');
            if (!legendContainer) return;
            
            legendContainer.innerHTML = '';
            
            statusLabels.forEach((label, i) => {
                const count = statusCounts[i];
                const percentage = statusPercentages[label] || 0;
                const color = donutChart.data.datasets[0].backgroundColor[i];
                
                const item = document.createElement('div');
                item.className = 'd-inline-flex align-items-center me-3 mb-2';
                item.style.cursor = 'pointer';
                
                const colorBox = document.createElement('span');
                colorBox.className = 'd-inline-block me-2';
                colorBox.style.width = '15px';
                colorBox.style.height = '15px';
                colorBox.style.backgroundColor = color;
                colorBox.style.borderRadius = '3px';
                
                const text = document.createElement('span');
                text.className = 'small text-white';
                text.textContent = `${label} (${percentage}%)`;
                
                item.appendChild(colorBox);
                item.appendChild(text);
                
                // Adiciona evento de clique para destacar/ocultar segmentos
                item.addEventListener('click', () => {
                    const meta = donutChart.getDatasetMeta(0);
                    meta.data[i].hidden = !meta.data[i].hidden;
                    donutChart.update();
                    
                    // Atualiza o estilo do item da legenda
                    item.style.opacity = meta.data[i].hidden ? '0.5' : '1';
                });
                
                legendContainer.appendChild(item);
            });
        }
        
        // Atualiza a legenda quando o gráfico for renderizado
        donutChart.options.animation.onComplete = createCustomLegend;
        // Clique no gráfico de Pessoas
        (function(){
          const canvas = document.getElementById('graficoPessoas');
          const panel = document.getElementById('ticketPanelPessoas');
          const panelBody = document.getElementById('ticketPanelBodyPessoas');
          const btnClose = document.getElementById('closeTicketPanelPessoas');
          if (!canvas || !panel) return;
          canvas.addEventListener('click', function(evt){
            const chart = Chart.getChart(canvas);
            if (!chart) return;
            const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (!points.length) return;
            const idx = points[0].index;
            const label = (chart.data.labels?.[idx] ?? '').toString();
            const items = ticketsByPessoa[label] ?? [];
            if (!items.length) {
              panelBody.innerHTML = `<p class="text-muted mb-0">Sem tickets para "${label}".</p>`;
            } else {
              const html = items.map(item => {
                const title = item.titulo || item.title || item.nome || item.descricao || `Ticket ${item.id ?? ''}`;
                const code = item.id || item.numero || item.num || item.codigo || item.code || item.chave || item.key;
                const badgeInner = code ? `#${code}` : '';
                const badge = code
                  ? (item.url
                      ? `<a href="${item.url}" target="_blank" rel="noopener" class="text-decoration-none"><span class="badge bg-secondary">${badgeInner}</span></a>`
                      : `<span class="badge bg-secondary">${badgeInner}</span>`)
                  : '';
                return `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${title}</span>${badge}</li>`;
              }).join('');
              const total = pessoasCounts[idx] || 0;
              const percentage = pessoasPercentages[idx] || 0;
              panelBody.innerHTML = `<h6 class="mb-2">${label} <span class=\"text-muted\">(${total} testes - ${percentage}% do total)</span></h6><ul class=\"list-group list-group-flush\">${html}</ul>`;
            }
            panel.classList.remove('d-none');
          });
          btnClose?.addEventListener('click', ()=>{
            panel.classList.add('d-none');
          });
        })();
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
        // HTML legend (vertical, bottom-left)
        (function(){
          const legendContainer = document.getElementById('donutLegend');
          const canvasEl = document.getElementById('donutChart');
          const chart = Chart.getChart(canvasEl);
          if (!legendContainer || !chart) return;
          function renderLegend() {
            legendContainer.innerHTML = '';
            const items = chart.options.plugins.legend.labels.generateLabels(chart);
            items.forEach(item => {
              const li = document.createElement('div');
              li.className = 'legend-item';
              li.style.opacity = item.hidden ? '0.5' : '1';
              li.onclick = () => {
                chart.toggleDataVisibility(item.index);
                chart.update();
                renderLegend();
              };
              const box = document.createElement('span');
              box.className = 'legend-box';
              box.style.background = item.fillStyle;
              box.style.borderColor = item.strokeStyle;
              const label = document.createElement('span');
              label.textContent = item.text;
              legendContainer.appendChild(li);
              li.appendChild(box);
              li.appendChild(label);
            });
          }
          renderLegend();
        })();
        new Chart(document.getElementById('graficoPessoas'), {
            type: 'bar',
            data: {
                labels: pessoasLabels,
                datasets: [{
                    label: 'Testes Realizados',
                    data: {!! json_encode(array_values($porPessoa ?? [])) !!},
                    backgroundColor: colors.responsavel,
                    hoverBackgroundColor: colors.responsavelHover,
                    borderColor: colors.responsavelBorder,
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    title: { 
                        display: true, 
                        text: 'Testes por Responsável', 
                        font: { size: 16 } 
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = pessoasPercentages[context.dataIndex] || 0;
                                return `${label}: ${value} testes (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor }, border: { color: axisBorderColor } },
                    x: { grid: { color: gridColor }, border: { color: axisBorderColor } }
                }
            }
        });
        new Chart(document.getElementById('graficoEstruturas'), {
            type: 'bar',
            data: {
                labels: estruturasLabels,
                datasets: [{
                    label: 'Quantidade de Testes',
                    data: {!! json_encode(array_values($estruturas ?? [])) !!},
                    backgroundColor: '#0042cf',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    title: { 
                        display: true, 
                        text: 'Distribuição por Estrutura', 
                        font: { size: 16 } 
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = estruturasPercentages[context.dataIndex] || 0;
                                return `${label}: ${value} testes (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor }, border: { color: axisBorderColor } },
                    y: { grid: { color: gridColor }, border: { color: '#ffffff' } }
                }
            }
        });
        // Clique no gráfico de Estruturas
        (function(){
          const canvas = document.getElementById('graficoEstruturas');
          const panel = document.getElementById('ticketPanelEstruturas');
          const panelBody = document.getElementById('ticketPanelBodyEstruturas');
          const btnClose = document.getElementById('closeTicketPanelEstruturas');
          if (!canvas || !panel) return;
          canvas.addEventListener('click', function(evt){
            const chart = Chart.getChart(canvas);
            if (!chart) return;
            const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (!points.length) return;
            const idx = points[0].index;
            const label = (chart.data.labels?.[idx] ?? '').toString();
            const items = ticketsByEstrutura[label] ?? [];
            if (!items.length) {
              panelBody.innerHTML = `<p class=\"text-muted mb-0\">Sem tickets para \"${label}\".</p>`;
            } else {
              const html = items.map(item => {
                const title = item.titulo || item.title || item.nome || item.descricao || `Ticket ${item.id ?? ''}`;
                const code = item.id || item.numero || item.num || item.codigo || item.code || item.chave || item.key;
                const badgeInner = code ? `#${code}` : '';
                const badge = code
                  ? (item.url
                      ? `<a href="${item.url}" target="_blank" rel="noopener" class="text-decoration-none"><span class="badge bg-secondary">${badgeInner}</span></a>`
                      : `<span class="badge bg-secondary">${badgeInner}</span>`)
                  : '';
                return `<li class=\"list-group-item d-flex justify-content-between align-items-center\"><span>${title}</span>${badge}</li>`;
              }).join('');
              const total = estruturasCounts[idx] || 0;
              const percentage = estruturasPercentages[idx] || 0;
              panelBody.innerHTML = `<h6 class=\"mb-2\">${label} <span class=\"text-muted\">(${total} testes - ${percentage}% do total)</span></h6><ul class=\"list-group list-group-flush\">${html}</ul>`;
            }
            panel.classList.remove('d-none');
          });
          btnClose?.addEventListener('click', ()=>{
            panel.classList.add('d-none');
          });
        })();
    </script>
    <footer class="footer mt-4 py-3">
      <div class="container text-center">
        <span class="text-muted d-block d-md-inline">  {{ date('Y') }} Dashboard de Testes</span>
        <span class="text-muted d-none d-md-inline"> • </span>
        <span class="text-muted d-block d-md-inline">Feito por 
          <a href="https://github.com/dev-jom" target="_blank" rel="noopener" class="footer-link">Jonathas</a>
        </span> <br>
        <span>Versão 1.0.0</span>
      </div>
    </footer>
</body>
</html>

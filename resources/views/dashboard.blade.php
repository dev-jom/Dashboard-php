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
                  <div class="card-body">
                  <canvas id="donutChart" style="min-height: 250px;height: 400px;max-height: 400px;max-width: 896px;display: block;width: 896px;box-sizing: border-box;" width="896" height="400"></canvas>
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

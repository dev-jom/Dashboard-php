
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sprint 145</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="container-fluid">
        <h1 class="text-center text-white mb-4">
            <i class="fas fa-chart-line me-2"></i>Dashboard Sprint 145
        </h1>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Total de Tickets</h6>
                        <h2 class="display-4 fw-bold text-primary">{{ $total_tickets }}</h2>
                        <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Taxa de Aprovação</h6>
                        <h2 class="display-4 fw-bold text-success">{{ number_format($percentual_aprovacao, 1) }}%</h2>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Total de Estruturas</h6>
                        <h2 class="display-4 fw-bold text-info">{{ count($estruturas) }}</h2>
                        <i class="fas fa-layer-group fa-2x text-info"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Testadores Ativos</h6>
                        <h2 class="display-4 fw-bold text-warning">{{ count($porPessoa) }}</h2>
                        <i class="fas fa-users fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <canvas id="graficoResultados"></canvas>
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
            pendente: root.getPropertyValue('--cor-pendente').trim(),
            outros: root.getPropertyValue('--cor-outros').trim(),
            responsavel: root.getPropertyValue('--cor-responsavel').trim(),
            estrutura: root.getPropertyValue('--cor-estrutura').trim()
        };
        new Chart(document.getElementById('graficoResultados'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($totais)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($totais)) !!},
                    backgroundColor: [
                        colors.aprovado,
                        colors.reprovado,
                        colors.pendente,
                        colors.outros
                    ]
                }]
            },
            options: {
                plugins: {
                    title: { display: true, text: 'Status dos Testes', font: { size: 16 } },
                    legend: { position: 'bottom' }
                }
            }
        });
        new Chart(document.getElementById('graficoPessoas'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($porPessoa)) !!},
                datasets: [{
                    label: 'Testes Realizados',
                    data: {!! json_encode(array_values($porPessoa)) !!},
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
                labels: {!! json_encode(array_keys($estruturas)) !!},
                datasets: [{
                    label: 'Quantidade de Testes',
                    data: {!! json_encode(array_values($estruturas)) !!},
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

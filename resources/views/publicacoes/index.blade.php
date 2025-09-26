<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Publicações</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/style.css">
</head>
<body class="bg-dark text-light">
  <div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Lista de Testes</h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-light btn-sm" href="/">Voltar</a>
        <a class="btn btn-success btn-sm" href="{{ route('publicacoes.create') }}">+ Cadastrar</a>
      </div>
    </div>

    <form method="get" class="row g-2 align-items-end mb-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Pesquisar</label>
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="ID, título, responsável, estrutura...">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Sprint</label>
        <select name="sprint" class="form-select">
          <option value="">Todas</option>
          @foreach($sprints as $sp)
            <option value="{{ $sp }}" {{ $selectedSprint == $sp ? 'selected' : '' }}>{{ $sp }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-2">
        <button class="btn btn-primary w-100" type="submit">Filtrar</button>
      </div>
    </form>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-sm table-hover align-middle mb-0 text-light">
        <thead class="table-light">
          <tr>
            <th style="width: 60px;">ID</th>
            <th>Título</th>
            <th>Estrutura</th>
            <th>Dev</th>
            <th>Resultado</th>
            <th>Data</th>
            <th>Sprint</th>
            <th style="width: 120px;">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tests as $t)
            <tr>
              <td>{{ $t->numero_ticket }}</td>
              <td>
                <div class="fw-semibold">{{ $t->resumo_tarefa }}</div>
                @if($t->link_tarefa)
                  <a href="{{ $t->link_tarefa }}" target="_blank" class="small text-light text-opacity-75 text-decoration-underline">Abrir tarefa</a>
                @endif
              </td>
              <td>
                @php
                  $estruturas = is_array($t->estrutura) ? $t->estrutura : (empty($t->estrutura) ? [] : [$t->estrutura]);
                @endphp
                <div class="small text-light">{{ implode(', ', $estruturas) }}</div>
              </td>
              <td>{{ $t->atribuido_a }}</td>
              <td>{{ $t->resultado }}</td>
              <td>{{ optional($t->data_teste)->format('d/m/Y') }}</td>
              <td>{{ $t->sprint }}</td>
              <td>
                <div class="d-flex gap-1 table-actions">
                  <a href="{{ route('publicacoes.edit', $t) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                    <i class="fa fa-pen"></i>
                  </a>
                  <form action="{{ route('publicacoes.destroy', $t) }}" method="post" onsubmit="return confirm('Remover este registro?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4">Nenhum registro encontrado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $tests->links() }}
    </div>
  </div>
  <!-- Removido FA JS para evitar substituição por SVG que pode herdar tamanhos inesperados -->
</body>
</html>

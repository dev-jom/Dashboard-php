<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $mode === 'edit' ? 'Editar' : 'Cadastrar' }} Teste</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/style.css">
  <style>
    .form-hint { font-size: .85rem; opacity: .85; }
  </style>
</head>
<body class="bg-dark text-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">{{ $mode === 'edit' ? 'Editar' : 'Cadastrar' }} Teste</h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-light btn-sm" href="{{ route('publicacoes.index') }}">Voltar</a>
      </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger">
        <div class="fw-bold mb-1">Erros de validação</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="post" action="{{ $mode === 'edit' ? route('publicacoes.update', $test) : route('publicacoes.store') }}" class="row g-3">
      @csrf
      @if($mode === 'edit')
        @method('PUT')
      @endif

      <div class="col-12 col-md-4">
        <label class="form-label">Tipo de Teste</label>
        <select name="tipo_teste" class="form-select" required>
          @foreach($tipoTesteOptions as $opt)
            <option value="{{ $opt }}" {{ old('tipo_teste', $test->tipo_teste) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">#Número do Ticket</label>
        <input type="text" name="numero_ticket" class="form-control" value="{{ old('numero_ticket', $test->numero_ticket) }}" required>
      </div>

      <div class="col-12">
        <label class="form-label">Resumo da Tarefa</label>
        <input type="text" name="resumo_tarefa" class="form-control" value="{{ old('resumo_tarefa', $test->resumo_tarefa) }}" required>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Estrutura</label>
        @php
          $estruturas = is_array($test->estrutura) ? $test->estrutura : (empty($test->estrutura) ? [] : [$test->estrutura]);
          $estruturaStr = old('estrutura', implode(', ', $estruturas));
        @endphp
        <input type="text" name="estrutura" class="form-control" value="{{ $estruturaStr }}" placeholder="Ex.: Módulo A, Serviço X">
        <div class="form-hint">Separe por vírgula para múltiplos valores.</div>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Atribuído a</label>
        <input type="text" name="atribuido_a" class="form-control" value="{{ old('atribuido_a', $test->atribuido_a) }}">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Resultado</label>
        <input type="text" name="resultado" class="form-control" value="{{ old('resultado', $test->resultado) }}" placeholder="Aprovado, Reprovado, Validado...">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Data do Teste</label>
        <input type="date" name="data_teste" class="form-control" value="{{ old('data_teste', optional($test->data_teste)->format('Y-m-d')) }}">
      </div>

      <div class="col-12 col-md-4">
        <label class="form-label">Link da tarefa</label>
        <input type="url" name="link_tarefa" class="form-control" value="{{ old('link_tarefa', $test->link_tarefa) }}" placeholder="https://...">
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Sprint</label>
        @php $currentSprint = old('sprint', $test->sprint); @endphp
        <select id="sprint" name="sprint" class="form-select">
          <option value="">-- Selecionar --</option>
          @foreach($sprints as $sp)
            <option value="{{ $sp }}" {{ $currentSprint === $sp ? 'selected' : '' }}>{{ $sp }}</option>
          @endforeach
          <option value="__outra__">Outra...</option>
        </select>
        <input id="sprint_other" type="text" name="sprint_other" class="form-control mt-2 d-none" placeholder="Digite a nova Sprint">
        <div class="form-hint">Ao cadastrar uma Sprint nova, ela aparecerá automaticamente no portal.</div>
      </div>

      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('publicacoes.index') }}" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>

  <script>
    (function(){
      const sprintSelect = document.getElementById('sprint');
      const sprintOther = document.getElementById('sprint_other');
      function toggleOther(){
        if (sprintSelect.value === '__outra__') {
          sprintOther.classList.remove('d-none');
          sprintOther.required = true;
          sprintOther.focus();
        } else {
          sprintOther.classList.add('d-none');
          sprintOther.required = false;
        }
      }
      sprintSelect.addEventListener('change', toggleOther);
      toggleOther();
    })();
  </script>
</body>
</html>

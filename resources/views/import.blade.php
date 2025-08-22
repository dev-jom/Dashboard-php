<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Importar JSON - Tests</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Importar JSON</h1>
      <a href="/dashboard" class="btn btn-outline-secondary btn-sm">Voltar ao Dashboard</a>
    </div>

    <?php if (session('success')): ?>
      <div class="alert alert-success"><?= e(session('success')) ?></div>
    <?php endif; ?>

    <?php if ($errors->any()): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors->all() as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="post" action="<?= route('import.run') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label for="json" class="form-label">Cole aqui o JSON</label>
            <textarea class="form-control" id="json" name="json" rows="14" placeholder='[{"numero_ticket":"ABC-123","resumo_tarefa":"Corrigir login","link_tarefa":"https://...","estrutura":"Auth","atribuido_a":"João","resultado":"Aprovado","data_teste":"2025-08-20"}]'><?= e(old('json')) ?></textarea>
            <div class="form-text">Aceita: array de objetos ou {"items": [ ... ]}. Campos suportados: numero_ticket/id/ticket/numero, resumo_tarefa/titulo/title/descricao, link_tarefa/url/link, estrutura, atribuido_a/responsavel, resultado/status, tipo_teste/tipo, data_teste/data/date.</div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="1" id="truncate" name="truncate">
            <label class="form-check-label" for="truncate">
              Zerar tabela antes de importar (truncate)
            </label>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Importar</button>
            <a href="/import" class="btn btn-outline-secondary">Limpar</a>
          </div>
        </form>
      </div>
    </div>

    <div class="mt-4">
      <h2 class="h5">Exemplo de JSON</h2>
      <pre class="bg-white p-3 border rounded small"><code>[{
  "numero_ticket": "ABC-123",
  "resumo_tarefa": "Corrigir fluxo de login",
  "link_tarefa": "https://seu-tracker/tickets/ABC-123",
  "estrutura": "Autenticação",
  "atribuido_a": "João",
  "resultado": "Aprovado",
  "tipo_teste": "Funcional",
  "data_teste": "2025-08-20"
},
{
  "ticket": 456,
  "titulo": "Ajustar layout header",
  "url": "https://seu-tracker/tickets/456",
  "sistema": "Frontend",
  "responsavel": "Maria",
  "status": "Reprovado",
  "tipo": "UI",
  "data": "20/08/2025"
}]
</code></pre>
    </div>
  </div>
</body>
</html>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acesso Restrito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/style.css">
  <style>
    body { background-color: #171D20; color: #e9ecef; }
    .card { background-color: #22272a; border: 1px solid rgba(255,255,255,.08); }
    .card-header { background: transparent; border-bottom: 1px solid rgba(255,255,255,.08); color: #fff; }
    .form-control, .form-select { background-color: #1d2225; border-color: rgba(255,255,255,.12); color: #e9ecef; }
    .form-control:focus { background-color: #1d2225; color: #fff; border-color: #4ea1ff; box-shadow: 0 0 0 .25rem rgba(78,161,255,.15); }
    .btn-primary { background-color: #0042cf; border-color: #002d6d; }
    .btn-primary:hover { background-color: #003575; border-color: #002d6d; }
    a { color: #4ea1ff; }
    a:hover { color: #76b7ff; }
    .card-body { color: #fff; }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h1 class="h5 mb-0">Acesso Restrito</h1>
            <i class="fa-solid fa-lock"></i>
          </div>
          <div class="card-body">
            @if ($errors->any())
              <div class="alert alert-danger">
                <div class="fw-bold mb-1">Erro de autenticação</div>
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="post" action="{{ route('portal.login.submit') }}" class="g-3">
              @csrf
              <input type="hidden" name="intended" value="{{ $intended }}">

              <div class="mb-3">
                <label class="form-label">Usuário</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" autofocus required>
              </div>

              <div class="mb-4">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Entrar</button>
              </div>
            </form>
          </div>
        </div>
        <div class="text-center mt-3">
          <a href="{{ route('dashboard') }}" class="small">Voltar ao Dashboard</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

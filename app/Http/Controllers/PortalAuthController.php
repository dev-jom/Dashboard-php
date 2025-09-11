<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortalAuthController extends Controller
{
    // exibe o formulário de login. Se "intended" vier na query, mantém para redirecionar após login
    public function show(Request $request)
    {
        $intended = $request->query('intended', route('publicacoes.index'));
        return view('auth.portal_login', ['intended' => $intended]);
    }

    // processa o login simples usando credenciais do .env
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'intended' => ['nullable', 'string'],
        ]);

        $user = env('PORTAL_LOGIN_USER');
        $pass = env('PORTAL_LOGIN_PASS');

        if ($user && $pass && $request->input('username') === $user && $request->input('password') === $pass) {
            $request->session()->put('portal_authed', true);
            $redirectTo = $request->input('intended') ?: route('publicacoes.index');
            return redirect()->to($redirectTo);
        }

        return back()->withErrors(['username' => 'Credenciais inválidas'])->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('portal_authed');
        return redirect()->route('portal.login');
    }

    // rota "gateway": se autenticado, manda para publicacoes; senão, manda para login guardando a intended
    public function gateway(Request $request)
    {
        if ($request->session()->get('portal_authed')) {
            return redirect()->route('publicacoes.index');
        }
        return redirect()->route('portal.login', ['intended' => route('publicacoes.index')]);
    }
}

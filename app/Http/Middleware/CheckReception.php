<?php

namespace App\Http\Middleware;

use Closure;

class CheckReception
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verifica se está logado, se não tiver redireciona
        if ( !auth()->check() )
            //return redirect()->route('login');
            return response()->json(["Erro 401"], 401);

        // Recupera o type do usuário logado
        $type = auth()->user()->type;

        // Verifica se é RECEPÇÃO, se não manda uma msg de erro.
        if ( $type != 'RECEPCAO' )
            return response()->json(["Erro 401"], 401);

        return $next($request);
    }
}

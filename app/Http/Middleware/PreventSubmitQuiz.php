<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class PreventSubmitQuiz
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

        $arrJawaban = $request->session()->get('jawaban');
        $arrKunci = $request->session()->get('kunci');
        if (Auth::check() && ($arrJawaban==$arrKunci)) {
            return $next($request);
        }
        return redirect('/');
    }
}

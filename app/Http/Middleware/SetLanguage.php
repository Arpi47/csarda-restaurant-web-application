<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    public function handle(Request $request, Closure $next)
    {
        $language = $request->header(
            'Accept-Language',
            'en'
        );
        if(
            in_array(
                $language,
                [
                    'en',
                    'hu',
                    'sr_lat',
                    'sr_cyrl'
                ]
            )
        ){
            App::setLocale($language);
        }
        return $next($request);
    }
}
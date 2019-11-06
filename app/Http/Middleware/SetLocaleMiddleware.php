<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class SetLocaleMiddleware
 * @package App\Http\Middleware
 */
class SetLocaleMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $allowedLanguageKeys = [
            'lang',
            'lang_id',
            'language_id',
            'languageId',
            'language',
        ];
        foreach ($allowedLanguageKeys as $languageKey) {
            if (!empty($request->header($languageKey))){
                $lang = $request->header($languageKey);
                config(['app.locale' => $lang]);
                app('translator')->setLocale($lang);
                break;
            }
        }
        return $next($request);
    }
}

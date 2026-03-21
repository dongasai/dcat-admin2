<?php

namespace Dcat\Admin\Http\Middleware;

use Dcat\Admin\Support\AdminConfig;
use Dcat\Admin\Support\AdminConfigInitializer;
use Illuminate\Http\Request;

class Session
{
    public function handle(Request $request, \Closure $next)
    {
        // 初始化配置前缀
        AdminConfigInitializer::initialize($request);

        if (! AdminConfig::route('enable_session_middleware') && ! AdminConfig::get('multi_app')) {
            return $next($request);
        }

        $path_prefix = '';
        $path_arr = parse_url(config('app.url'));

        if (array_key_exists('path', $path_arr) && ! empty($path_arr['path'])) {
            $path_prefix = rtrim($path_arr['path'], '/');
        }

        $path = $path_prefix.'/'.trim(AdminConfig::routePrefix(), '/');

        config(['session.path' => $path]);

        return $next($request);
    }
}

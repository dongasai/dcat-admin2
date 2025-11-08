<?php

namespace Dcat\Admin\Support;

use Illuminate\Http\Request;

/**
 * Admin Config 初始化器
 *
 * 根据当前请求路径自动设置配置前缀，支持多后台
 */
class AdminConfigInitializer
{
    /**
     * 配置前缀映射
     *
     * @var array
     */
    protected static $prefixMap = [
        'admin' => 'admin',
        'merchant-admin' => 'merchant-admin',
    ];

    /**
     * 默认配置前缀
     *
     * @var string
     */
    protected static $defaultPrefix = 'admin';

    /**
     * 是否已初始化
     *
     * @var bool
     */
    protected static $initialized = false;

    /**
     * 初始化配置前缀
     *
     * @param Request|null $request
     * @return void
     */
    public static function initialize(?Request $request = null): void
    {
        if (static::$initialized) {
            return;
        }

        $request = $request ?: request();
        $prefix = static::detectPrefix($request);

        AdminConfig::setPrefix($prefix);
        static::$initialized = true;
    }

    /**
     * 检测当前请求应该使用的配置前缀
     *
     * @param Request $request
     * @return string
     */
    protected static function detectPrefix(Request $request): string
    {
        $path = $request->path();

        // 检查路径前缀
        foreach (static::$prefixMap as $pathPrefix => $configPrefix) {
            if ($request->is($pathPrefix . '*') ||
                str_starts_with($path, $pathPrefix . '/') ||
                $path === $pathPrefix) {
                return $configPrefix;
            }
        }

        // 检查路由参数
        $route = $request->route();
        if ($route && $route->getPrefix()) {
            $routePrefix = trim($route->getPrefix(), '/');
            foreach (static::$prefixMap as $pathPrefix => $configPrefix) {
                if ($routePrefix === $pathPrefix) {
                    return $configPrefix;
                }
            }
        }

        // 返回默认前缀
        return static::$defaultPrefix;
    }

    /**
     * 添加配置前缀映射
     *
     * @param string $pathPrefix  URL路径前缀
     * @param string $configPrefix 配置文件前缀
     * @return void
     */
    public static function addPrefixMap(string $pathPrefix, string $configPrefix): void
    {
        static::$prefixMap[$pathPrefix] = $configPrefix;
    }

    /**
     * 设置默认配置前缀
     *
     * @param string $prefix
     * @return void
     */
    public static function setDefaultPrefix(string $prefix): void
    {
        static::$defaultPrefix = $prefix;
    }

    /**
     * 获取所有配置前缀映射
     *
     * @return array
     */
    public static function getPrefixMap(): array
    {
        return static::$prefixMap;
    }

    /**
     * 重置初始化状态
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$initialized = false;
        AdminConfig::setPrefix(static::$defaultPrefix);
    }
}
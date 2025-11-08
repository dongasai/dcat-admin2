<?php

namespace Dcat\Admin\Support;

/**
 * Admin Config 助手类
 *
 * 用于替代硬编码的 config('admin.') 调用，支持多后台配置
 */
class AdminConfig
{
    /**
     * 当前配置前缀
     *
     * @var string
     */
    protected static $configPrefix = 'admin';

    /**
     * 设置配置前缀
     *
     * @param string $prefix
     * @return void
     */
    public static function setPrefix(string $prefix): void
    {
        static::$configPrefix = $prefix;
    }

    /**
     * 获取当前配置前缀
     *
     * @return string
     */
    public static function getPrefix(): string
    {
        return static::$configPrefix;
    }

    /**
     * 获取配置值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return config(static::$configPrefix . '.' . $key, $default);
    }

    /**
     * 获取目录配置
     *
     * @param string $path
     * @return string
     */
    public static function directory(string $path = ''): string
    {
        $directory = static::get('directory', 'Admin');

        return ucfirst($directory) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * 获取路由配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function route(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('route', []);
        }

        return static::get("route.{$key}", $default);
    }

    /**
     * 获取路由前缀
     *
     * @return string
     */
    public static function routePrefix(): string
    {
        return static::route('prefix', 'admin');
    }

    /**
     * 获取数据库配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function database(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('database', []);
        }

        return static::get("database.{$key}", $default);
    }

    /**
     * 获取认证配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function auth(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('auth', []);
        }

        return static::get("auth.{$key}", $default);
    }

    /**
     * 获取上传配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function upload(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('upload', []);
        }

        return static::get("upload.{$key}", $default);
    }

    /**
     * 获取HTTPS配置
     *
     * @return bool
     */
    public static function isHttps(): bool
    {
        return static::get('https', false) || static::get('secure', false);
    }

    /**
     * 获取扩展配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function extension(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('extension', []);
        }

        return static::get("extension.{$key}", $default);
    }

    /**
     * 获取默认头像
     *
     * @return string
     */
    public static function defaultAvatar(): string
    {
        return static::get('default_avatar', '@admin/images/default-avatar.jpg');
    }

    /**
     * 获取应用标题
     *
     * @return string
     */
    public static function title(): string
    {
        return static::get('title', 'Admin');
    }

    /**
     * 获取应用名称
     *
     * @return string
     */
    public static function name(): string
    {
        return static::get('name', 'Dcat Admin');
    }

    /**
     * 获取Logo
     *
     * @return string
     */
    public static function logo(): string
    {
        return static::get('logo', '<b>Dcat</b> Admin');
    }

    /**
     * 获取迷你Logo
     *
     * @return string
     */
    public static function logoMini(): string
    {
        return static::get('logo-mini', '<b>D</b>');
    }

    /**
     * 获取布局配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function layout(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('layout', []);
        }

        return static::get("layout.{$key}", $default);
    }

    /**
     * 检查是否启用权限
     *
     * @return bool
     */
    public static function permissionEnabled(): bool
    {
        return static::get('permission.enable', true);
    }

    /**
     * 检查是否启用菜单绑定权限
     *
     * @return bool
     */
    public static function menuBindPermission(): bool
    {
        return static::get('menu.bind_permission', true);
    }

    /**
     * 获取菜单配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function menu(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('menu', []);
        }

        return static::get("menu.{$key}", $default);
    }

    /**
     * 获取网格配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function grid(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('grid', []);
        }

        return static::get("grid.{$key}", $default);
    }

    /**
     * 获取助手类配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function helpers(string $key = null, $default = null)
    {
        if ($key === null) {
            return static::get('helpers', []);
        }

        return static::get("helpers.{$key}", $default);
    }
}
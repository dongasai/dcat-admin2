<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\Lang;

class Menu
{
    /**
     * 外部设置的激活菜单ID.
     *
     * @var int|null
     */
    protected static $activeId;

    /**
     * 外部设置的激活菜单路径.
     *
     * @var string|null
     */
    protected static $activePath;

    protected static $helperNodes = [
        [
            'id'        => 1,
            'title'     => 'Helpers',
            'icon'      => 'fa fa-keyboard-o',
            'uri'       => '',
            'parent_id' => 0,
        ],
        [
            'id'        => 2,
            'title'     => 'Extensions',
            'icon'      => '',
            'uri'       => 'auth/extensions',
            'parent_id' => 1,
        ],
        [
            'id'        => 3,
            'title'     => 'Scaffold',
            'icon'      => '',
            'uri'       => 'helpers/scaffold',
            'parent_id' => 1,
        ],
        [
            'id'        => 4,
            'title'     => 'Icons',
            'icon'      => '',
            'uri'       => 'helpers/icons',
            'parent_id' => 1,
        ],
    ];

    protected $view = 'admin::partials.menu';

    /**
     * 设置激活的菜单ID.
     *
     * @param  int  $id
     * @return void
     */
    public static function setActiveId(int $id)
    {
        static::$activeId = $id;
    }

    /**
     * 设置激活的菜单路径.
     *
     * @param  string  $path
     * @return void
     */
    public static function setActivePath(string $path)
    {
        static::$activePath = $path;
    }

    /**
     * 重置外部设置的激活状态.
     *
     * @return void
     */
    public static function resetActive()
    {
        static::$activeId = null;
        static::$activePath = null;
    }

    public function register()
    {
        if (! admin_has_default_section(Admin::SECTION['LEFT_SIDEBAR_MENU'])) {
            admin_inject_default_section(Admin::SECTION['LEFT_SIDEBAR_MENU'], function () {
                $menuModel = config('admin.database.menu_model');

                return $this->toHtml((new $menuModel())->allNodes()->toArray());
            });
        }

        if (config('app.debug') && config('admin.helpers.enable', true)) {
            $this->add(static::$helperNodes, 20);
        }
    }

    /**
     * 增加菜单节点.
     *
     * @param  array  $nodes
     * @param  int  $priority
     * @return void
     */
    public function add(array $nodes = [], int $priority = 10)
    {
        admin_inject_section(Admin::SECTION['LEFT_SIDEBAR_MENU_BOTTOM'], function () use (&$nodes) {
            return $this->toHtml($nodes);
        }, true, $priority);
    }

    /**
     * 转化为HTML.
     *
     * @param  array  $nodes
     * @return string
     *
     * @throws \Throwable
     */
    public function toHtml($nodes)
    {
        $html = '';

        foreach (Helper::buildNestedArray($nodes) as $item) {
            $html .= $this->render($item);
        }

        return $html;
    }

    /**
     * 设置菜单视图.
     *
     * @param  string  $view
     * @return $this
     */
    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * 渲染视图.
     *
     * @param  array  $item
     * @return string
     */
    public function render($item)
    {
        return view($this->view, ['item' => &$item, 'builder' => $this])->render();
    }

    /**
     * 判断是否选中.
     *
     * @param  array  $item
     * @param  null|string  $path
     * @return bool
     */
    public function isActive($item, ?string $path = null)
    {
        // 优先检查外部设置的激活ID
        if (static::$activeId !== null && isset($item['id'])) {
            if ($item['id'] == static::$activeId) {
                return true;
            }
            // 如果是父菜单，检查子菜单是否匹配
            if (!empty($item['children'])) {
                foreach ($item['children'] as $child) {
                    if (isset($child['id']) && $child['id'] == static::$activeId) {
                        return true;
                    }
                }
            }
        }

        // 检查外部设置的激活路径
        if (static::$activePath !== null) {
            if (empty($item['children'])) {
                if (empty($item['uri'])) {
                    return false;
                }

                $menuPath = trim($this->getPath($item['uri']), '/');
                $activePath = trim(static::$activePath, '/');

                return $menuPath === $activePath;
            }

            // 有子菜单时，检查子菜单是否匹配
            foreach ($item['children'] as $v) {
                if (empty($v['uri'])) {
                    continue;
                }
                $childPath = trim($this->getPath($v['uri']), '/');
                $activePath = trim(static::$activePath, '/');

                if ($childPath === $activePath) {
                    return true;
                }
            }
        }

        // 默认行为：基于当前请求路径判断
        if (empty($path)) {
            $path = request()->path();
        }

        if (empty($item['children'])) {
            if (empty($item['uri'])) {
                return false;
            }

            $menuPath = trim($this->getPath($item['uri']), '/');

            // 精确匹配或前缀匹配（支持 /create、/edit 等子路径）
            return $menuPath == $path || str_starts_with($path, $menuPath . '/');
        }

        foreach ($item['children'] as $v) {
            $childPath = trim($this->getPath($v['uri']), '/');
            if ($path == $childPath || ($childPath && str_starts_with($path, $childPath . '/'))) {
                return true;
            }
            if (! empty($v['children'])) {
                if ($this->isActive($v, $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 判断节点是否可见.
     *
     * @param  array  $item
     * @return bool
     */
    public function visible($item)
    {
        if (
            ! $this->checkPermission($item)
            || ! $this->checkExtension($item)
            || ! $this->userCanSeeMenu($item)
        ) {
            return false;
        }

        $show = $item['show'] ?? null;
        if ($show !== null && ! $show) {
            return false;
        }

        return true;
    }

    /**
     * 判断扩展是否启用.
     *
     * @param $item
     * @return bool
     */
    protected function checkExtension($item)
    {
        $extension = $item['extension'] ?? null;

        if (! $extension) {
            return true;
        }

        if (! $extension = Admin::extension($extension)) {
            return false;
        }

        return $extension->enabled();
    }

    /**
     * 判断用户.
     *
     * @param  array|\Dcat\Admin\Models\Menu  $item
     * @return bool
     */
    protected function userCanSeeMenu($item)
    {
        $user = Admin::user();

        if (! $user || ! method_exists($user, 'canSeeMenu')) {
            return true;
        }

        return $user->canSeeMenu($item);
    }

    /**
     * 判断权限.
     *
     * @param $item
     * @return bool
     */
    protected function checkPermission($item)
    {
        $permissionIds = $item['permission_id'] ?? null;
        $roles = array_column(Helper::array($item['roles'] ?? []), 'slug');
        $permissions = array_column(Helper::array($item['permissions'] ?? []), 'slug');

        if (! $permissionIds && ! $roles && ! $permissions) {
            return true;
        }

        $user = Admin::user();

        if (! $user || $user->visible($roles)) {
            return true;
        }

        foreach (array_merge(Helper::array($permissionIds), $permissions) as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string  $text
     * @return string
     */
    public function translate($text)
    {
        $titleTranslation = 'menu.titles.'.trim(str_replace(' ', '_', strtolower($text)));

        if (Lang::has($titleTranslation)) {
            return __($titleTranslation);
        }

        return $text;
    }

    /**
     * @param  string  $uri
     * @return string
     */
    public function getPath($uri)
    {
        return $uri
            ? (url()->isValidUrl($uri) ? $uri : admin_base_path($uri))
            : $uri;
    }

    /**
     * @param  string  $uri
     * @return string
     */
    public function getUrl($uri)
    {
        return $uri ? admin_url($uri) : $uri;
    }
}

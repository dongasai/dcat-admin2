<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\HasPermissions;
use Dcat\Admin\Support\AdminConfig;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements AuthenticatableContract, Authorizable
{
    use Authenticatable,
        HasPermissions,
        HasDateTimeFormatter;

    const DEFAULT_ID = 1;

    protected $fillable = ['username', 'password', 'name', 'avatar'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = AdminConfig::database('connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(AdminConfig::database('users_table'));
    }

    /**
     * Get avatar attribute.
     *
     * @return mixed|string
     */
    public function getAvatar()
    {
        $avatar = $this->avatar;

        if ($avatar) {
            if (! URL::isValidUrl($avatar)) {
                $avatar = Storage::disk(AdminConfig::upload('disk'))->url($avatar);
            }

            return $avatar;
        }

        return admin_asset(AdminConfig::defaultAvatar() ?: '@admin/images/default-avatar.jpg');
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = AdminConfig::database('role_users_table');

        $relatedModel = AdminConfig::database('roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id')->withTimestamps();
    }

    /**
     * 判断是否允许查看菜单.
     *
     * @param  array|Menu  $menu
     * @return bool
     */
    public function canSeeMenu($menu)
    {
        return true;
    }
}

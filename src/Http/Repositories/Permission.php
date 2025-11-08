<?php

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\AdminConfig;

class Permission extends EloquentRepository
{
    public function __construct()
    {
        $this->eloquentClass = AdminConfig::database('permissions_model');

        parent::__construct();
    }
}

<?php

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\AdminConfig;

class Role extends EloquentRepository
{
    public function __construct($relations = [])
    {
        $this->eloquentClass = AdminConfig::database('roles_model');

        parent::__construct($relations);
    }
}

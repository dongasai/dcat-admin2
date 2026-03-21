<?php

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\AdminConfig;

class Menu extends EloquentRepository
{
    public function __construct($modelOrRelations = [])
    {
        $this->eloquentClass = AdminConfig::database('menu_model');

        parent::__construct($modelOrRelations);
    }
}

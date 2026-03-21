<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Support\AdminConfig;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = ['name', 'is_enabled', 'version', 'options'];

    protected $casts = [
        'options' => 'json',
    ];

    /**
     * {@inheritDoc}
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

        $this->setTable(AdminConfig::database('extensions_table') ?: 'admin_extensions');
    }
}

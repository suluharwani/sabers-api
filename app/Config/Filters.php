<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Filters\AuthFilter;

class Filters extends BaseConfig
{
    public $aliases = [
        'auth' => AuthFilter::class
    ];

    public $globals = [
        'before' => [
            'auth' => ['except' => ['api/login', 'api/register', 'api/users','api/sliders','api/sponsors','api/contacts','api/clients','api/projects','api/projects/*','api/users/*','api/sliders/*','api/sponsors/*','api/contacts/*','api/clients/*','api/blogs','api/blogs/*']]
        ],
        'after' => [
            // 'toolbar',
        ]
    ];

    public $methods = [];
    public $filters = [];
}
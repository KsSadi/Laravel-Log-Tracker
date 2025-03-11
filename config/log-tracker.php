<?php

return [
    'route_prefix' => 'log-tracker',
    'middleware' => ['web', 'auth'],
    'log_levels' => [
        'emergency' => ['color' => '#DC143C', 'icon' => 'fas fa-skull-crossbones'],
        'alert'     => ['color' => '#FF0000', 'icon' => 'fas fa-bell'],
        'critical'  => ['color' => '#FF4500', 'icon' => 'fas fa-exclamation-triangle'],
        'error'     => ['color' => '#FF6347', 'icon' => 'fas fa-exclamation-circle'],
        'warning'   => ['color' => '#FFA500', 'icon' => 'fas fa-exclamation-triangle'],
        'notice'    => ['color' => '#32CD32', 'icon' => 'fas fa-info-circle'],
        'info'      => ['color' => '#1E90FF', 'icon' => 'fas fa-info-circle'],
        'debug'     => ['color' => '#696969', 'icon' => 'fas fa-bug'],
        'total'     => ['color' => '#008000', 'icon' => 'fas fa-file-alt'],
    ],

    'per_page' => 50,
    'max_file_size' => 50,
    'allow_delete' => false,
    'allow_download' => true,
];

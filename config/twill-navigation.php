<?php

return [
	'dashboard' => [
	    'title' => 'New Upload',
	    'module' => false,
	    'route' => 'admin.dashboard'
	],
    'payrollUploads' => [
        'title' => 'Upload History',
        'module' => true
    ],
    'settings' => [
        'title' => 'Settings',
        'route' => 'admin.settings',
        'params' => ['section' => 'settings'],
        'primary_navigation' => [
            'settings' => [
                'title' => 'Settings',
                'route' => 'admin.settings',
                'params' => ['section' => 'settings']
            ],
        ]
    ],
];

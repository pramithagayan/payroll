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
        'params' => ['section' => 'notification'],
        'primary_navigation' => [
            'notification' => [
                'title' => 'Notification Settings',
                'route' => 'admin.settings',
                'params' => ['section' => 'notification']
            ],
        ]
    ],
];

<?php

return [
    'config' => [
        'language'       => env('APP_TINYMCE_LOCALE', 'en_US'),
		'plugins'        => 'codesample fullscreen',
		'toolbar'        => 'undo redo style | fontfamily fontsize | alignleft aligncenter alignright alignjustify | bullist numlist | copy cut paste pastetext | hr | codesample | link image quicktable | fullscreen',
		'toolbar_sticky' => true,
		'min_height'     => 1000,
		'license_key'    => 'gpl',
		'valid_elements' => '*[*]',
    ]
];

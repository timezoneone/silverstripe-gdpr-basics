<?php

use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;

HTMLEditorConfig::get('gdpr-basic')
    ->enablePlugins([
        'sslink' => 'vendor/silverstripe/admin/client/dist/js/TinyMCE_sslink.js',
        'sslinkexternal' => 'vendor/silverstripe/admin/client/dist/js/TinyMCE_sslink-external.js',
    ])
    ->setOptions([
        'skin' => 'silverstripe',
        'importcss_append' => true,
        'extended_valid_elements' => 'small',
        'formats' => [
            'small'=> ['inline'=>'small']
        ],
        'ol[start|type]',
    ])
    ->setButtonsForLine(1, array(
        'bold',
        'italic',
        'sslink',
        'unlink',
        'bullist',
        'numlist'
    ))
    ->setButtonsForLine(2, null)
    ->setButtonsForLine(3, null);

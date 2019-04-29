<?php

use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;

HTMLEditorConfig::get('gdpr-basic')
    ->setButtonsForLine(1, [
        'bold',
        'italic',
        'link',
        'unlink',
        'bullist',
        'numlist'
    ])->setButtonsForLine(2, null)
    ->setButtonsForLine(3, null);


<?php

$basic = HtmlEditorConfig::get('gdpr-basic')
            ->setButtonsForLine( 1, array(
                'bold',
                'italic',
                'link',
                'unlink',
                'bullist',
                'numlist'
            ))
            ->setButtonsForLine(2, null)
            ->setButtonsForLine(3, null);


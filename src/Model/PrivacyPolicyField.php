<?php

namespace TimeZoneOne\GDPR\Model;

use SilverStripe\UserForms\Model\EditableFormField\EditableLiteralField;


if(!class_exists(EditableLiteralField::class)) {
    return false;
}

class PrivacyPolicyField extends EditableLiteralField {

    private static $singular_name = 'Privacy Policy Disclosure';

    private static $plural_name = 'Privacy Policy Disclosure';

    private static $table_name = 'PrivacyPolicyField';

}

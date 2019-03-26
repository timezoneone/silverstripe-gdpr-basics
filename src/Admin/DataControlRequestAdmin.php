<?php


namespace TimeZoneOne\GDPR\Admin;

use SilverStripe\Admin\ModelAdmin;
use TimeZoneOne\GDPR\Model\DataControlRequest;

class DataControlRequestAdmin extends ModelAdmin
{

    private static $menu_title = 'Data Control Requests';

    private static $url_segment = 'gdpr-data-requests';

    private static $menu_priority = -100;

    private static $managed_models = array(
    	DataControlRequest::class
    );

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);
        if($this->modelClass == DataControlRequest::class){
            $gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
        }
        return $form;
    }

}

<?php 
class DataControlRequestAdmin extends ModelAdmin {

    private static $menu_title = 'Data Control Requests';

    private static $url_segment = 'gdpr-data-requests';

    private static $menu_priority = -100;

    private static $managed_models = array(
    	'DataControlRequest'
    );

    public function getEditForm($id = null, $fields = null) {
	    $form = parent::getEditForm($id, $fields);
	    $gridFieldName = $this->sanitiseClassName($this->modelClass);
	    if($gridFieldName=='DataControlRequest'){
	    	$gridField = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
	    }
	    return $form;
	  }

}
<?php

class DataControlRequest extends DataObject{

    private static $db = array(
        'FirstName' => 'Varchar(255)',
        'LastName' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'SecurityID' => 'Varchar(255)',
        'RequiredAction' => 'Enum("Provide data, Delete Data")',
        'Status' => 'Enum("Awaiting Verification, Ready to action, In progress, Complete")',
        'DateRequested' => 'Date'
    );

    private static $summary_fields = array('FirstName','LastName','Email','RequiredAction','Status');

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        return $fields;
    }

    public function onAfterWrite(){

        $from = 'test@timezoneone.com'; //Data Control Officer email
        $to = $this->Email;
        $subject= 'Data Control Request Verification';
        $body = 'Hi ' . $this->FirstName ."\n". "\n";
        $body .= 'We have received a request to ' . strtolower($action) . '. If you did not make this request, please ignore this email. Otherwise, please click the link below so that we can confirm your ownership of this email address and process your request'."\n". "\n";
        $body .= ''; 
        $email = new Email($from, $to, $subject, $body);
        $email->sendPlain();
    }

}


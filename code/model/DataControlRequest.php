<?php

class DataControlRequest extends DataObject{

    private static $db = array(
        'FirstName' => 'Varchar(255)',
        'LastName' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Verification' => 'Varchar(255)',
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
        $config = SiteConfig::current_site_config();

        if($this->Status === 'Awaiting Verification'){
            $from = $config->DataProtectionOfficer()->Email; 
            $to = $this->Email;
            $subject= 'Data Control Request Verification';
            $body = 'Hi ' . $this->FirstName ."\n"."\n";
            $body .= 'We have received a request to '.strtolower($this->RequiredAction).'. If you did not make this request, please ignore this email. Otherwise, please click the link below so that we can confirm your ownership of this email address and process your request'."\n". "\n";
            $body .= SiteConfigGDPR::siteURL() . '/data-control/confirm?verification='.$this->Verification.'&request='.$this->ID."\n"."\n"; 
            $body .= 'Kind Regards,'."\n";
            $body .= $config->DataProtectionOfficer()->FirstName.' '.$config->DataProtectionOfficer()->LastName."\n";
            $body .= 'Data Protection Officer (DPO)'."\n";
            $body .= $config->Title;
            $email = new Email($from, $to, $subject, $body);
            $email->sendPlain();
        }

        if($this->Status === 'Ready to action'){
            //@TODO
            //send confirmation to user and send notification to DataProtection Officer
        }

    }

}


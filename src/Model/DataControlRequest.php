<?php


namespace TimeZoneOne\GDPR\Model;

use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;
use TimeZoneOne\GDPR\Extension\SiteConfigGDPR;

class DataControlRequest extends DataObject 
{

    private static $db = [
        'FirstName' => 'Varchar(255)',
        'LastName' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Verification' => 'Varchar(255)',
        'RequiredAction' => 'Enum("Provide Data, Delete Data")',
        'Status' => 'Enum("Awaiting Verification, Ready to action, In progress, Complete")',
        'DateRequested' => 'Date',
        'IsEUResident' => 'Boolean'
    ];

    private static $summary_fields = [
        'FirstName',
        'LastName',
        'Email',
        'RequiredAction',
        'Status',
        'DateRequested',
        'IsEUResident'
    ];

    private static $table_name = 'DataControlRequest';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

    public function onAfterWrite()
    {
        $config = SiteConfig::current_site_config();

        if($this->Status === 'Awaiting Verification'){
            $from = $config->DataProtectionOfficer()->Email; 
            $to = $this->Email;
            $subject= 'Data Control Request Verification';
            $body = 'Hi ' . $this->FirstName ."\n"."\n";
            $body .= 'We have received a request to '.strtolower($this->RequiredAction).'. If you did not make this request, please ignore this email. Otherwise, please click the link below so that we can confirm your ownership of this email address and process your request'."\n". "\n";
            $body .= SiteConfigGDPR::siteURL() . 'data-control/confirm?verification='.$this->Verification.'&request='.$this->ID."\n"."\n";
            $body .= 'Kind Regards,'."\n";
            $body .= $config->DataProtectionOfficer()->FirstName.' '.$config->DataProtectionOfficer()->LastName."\n";
            $body .= 'Data Protection Officer (DPO)'."\n";
            $body .= $config->Title;
            try {
                $email = new Email($from, $to, $subject, $body);
                $email->sendPlain();
            } catch (\Exception $e) {}
        }

        if($this->Status === 'Ready to action'){
            //send confirmation to user and send notification to DataProtection Officer
            $DPO = $config->DataProtectionOfficer();
            $from = 'data-control@'.$_SERVER['HTTP_HOST']; 
            $to = $DPO->Email;
            $subject= 'Data Control Request: '.$this->RequiredAction;
            $body = 'Hi ' . $DPO->FirstName ."\n"."\n";
            $body .= 'You have received a request to '.strtolower($this->RequiredAction).' from '.$this->FirstName.' '.$this->LastName.' ('.$this->Email.')'."\n". "\n";

            if($this->IsEUResident){
                $body .= $this->FirstName.' '.$this->LastName. ' is an EU resident'."\n". "\n";
            }

            if($this->RequiredAction==='Provide Data'){
                $body .= 'You must provide '.$this->FirstName.' '.$this->LastName.' with all of their personal data. You must provide this free of charge. This is not limited to just the data collect via forms on the website, but also includes any data that may be stored by third-parties on your behalf (for example, in a CRM or email list management system).';
            }

            if($this->RequiredAction==='Delete Data'){
                $body .= 'You must remove all personal data that you have for '.$this->FirstName.' '.$this->LastName.'. Please let '.$this->FirstName.' know when this has been completed. This is not limited to just the data collect via forms on the website, but also includes any data that may be stored by third-parties on your behalf (for example, in a CRM or email list management system).';
            }

            try {
                $email = new Email($from, $to, $subject, $body);
                $email->sendPlain();
            } catch (\Exception $e) {}
        }

    }

}


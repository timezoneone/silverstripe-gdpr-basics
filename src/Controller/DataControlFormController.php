<?php

namespace TimeZoneOne\GDPR\Controller;

use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\SiteConfig\SiteConfig;
use TimeZoneOne\GDPR\Extension\SiteConfigGDPR;
use TimeZoneOne\GDPR\Model\DataControlRequest;

class DataControlFormController extends \PageController  {

    public function init() {
        parent::init();
    }

    private static $allowed_actions = [
        'verify',
        'confirm'
    ];

    public function index(){

        $config = SiteConfig::current_site_config();
        if(!$config->DataControlFormsActive){
            return $this->httpError(404);
        }else{
            $html = DBHTMLText::create();
            $html->setValue('<p>Use the form below to submit a request for a copy of your data or to have your data removed.</p>');
            return $this->customise(array(
                'Title' => 'Data Control',
                'Content' =>  $html,
                'Form' => $this->DataRequestForm()
            ))->renderWith('Page');
        }

    }

    public function verify($request){

        $config = SiteConfig::current_site_config();
        $formData = $request->postVars();
        if(empty($formData) || !$config->DataControlFormsActive){
            return $this->httpError(404);

        }else{
            //store record in Database.
            $record = new DataControlRequest();
            $record->FirstName = filter_var($formData['FirstName'], FILTER_SANITIZE_STRING);
            $record->LastName = filter_var($formData['LastName'], FILTER_SANITIZE_STRING);
            $record->Email = filter_var($formData['Email'], FILTER_SANITIZE_EMAIL);
            $record->Verification = filter_var($formData['SecurityID'], FILTER_SANITIZE_STRING);
            $record->IsEUResident = !empty($formData['IsEUResident']) ? (bool)$formData['IsEUResident'] : false;
            $record->RequiredAction = isset($formData['action_RemoveData']) ? 'Delete Data' : 'Provide data';
            $record->Status = 'Awaiting Verification';
            $record->write();
            $usersRequest = isset($formData['action_RemoveData']) ? 'delete your data' : 'provide a copy of your data';

            $config = SiteConfig::current_site_config();
            $dataProtectionOfficer = $config->DataProtectionOfficer();

            $content = DBHTMLText::create();
            $content->setValue('<p>Before we can action your request to '.
                $usersRequest .
                ', we need to verify that you are the owner of this email address. Please click the verification link in the email we\'ve sent you.</p><p>If you have any trouble, please contact our <a href="mailto:'.
                $dataProtectionOfficer->Email.
                '">Data Protection Officer, '.
                $dataProtectionOfficer->FirstName.' '. $dataProtectionOfficer->LastName.
                '</a>');
            return $this->customise(array(
                'Title' => 'Confirm ownership',
                'Content' => $content
            ))->renderWith('Page');

        }

    }

    public function confirm($request){

        $data = $request->getVars();
        $config = SiteConfig::current_site_config();
        $htmlMessage = DBHTMLText::create();

        if( empty($data) || !isset($data['verification']) || !isset($data['request']) || !$config->DataControlFormsActive){
            return $this->httpError(404);
        }else{

            $Verification = filter_var($data['verification'], FILTER_SANITIZE_SPECIAL_CHARS);
            $ID = filter_var($data['request'], FILTER_SANITIZE_SPECIAL_CHARS);

            $UsersRequest = isset($formData['action_RemoveData']) ? 'delete your data' : 'provide a copy of your data';

            $config = SiteConfig::current_site_config();
            $dataProtectionOfficer = $config->DataProtectionOfficer();

            //look up record in database..
            $record = DataControlRequest::get()->filter(array('Verification'=>$Verification, 'ID'=> $ID))->First();

            if(!$record){
                $htmlMessage->setValue("<p>Please sumbit a new request <a href=\"/data-control\">here</a> or contact our <a href=\"mailto:'.$dataProtectionOfficer->Email.'\">Data Protection Officer</a>.</p>");
                return $this->customise(array(
                    'Title' => 'Sorry, we couldn\'t find your original request.',
                    'Content' => $htmlMessage
                ))->renderWith('Page');
            }else if($record->Exists()){
                //update record in Database.
                $record->Status = 'Ready to action';
                $record->write();
            }

            return $this->customise(array(
                'Title' => 'Request Received',
                'Content' => $htmlMessage->setValue('<p>Thank you. Your request to '.$UsersRequest.' has been received. Our <a href="mailto:'.$dataProtectionOfficer->Email.'">Data Protection Officer</a> will be in touch.</p>')
            ))->renderWith('Page');
        }
    }

    public function DataRequestForm(){

        if(SiteConfigGDPR::is_enable_for_request()){
            $checkbox = HiddenField::create('IsEUResident')->setValue(1);
        }else{
            $checkbox = CheckboxField::create('IsEUResident', 'I am an EU resident');
        }

        $form = new Form($this, 'request',
            new FieldList(
                TextField::create('FirstName', 'First Name'),
                TextField::create('LastName', 'Last Name'),
                EmailField::create('Email', 'Email'),
                $checkbox
            ),
            new FieldList(
                FormAction::create('RequestData', 'Provide data')
                    ->setUseButtonTag(true)
                    ->setTitle('Request Data')
                    ->addExtraClass('button button-primary feature'),
                FormAction::create('RemoveData', 'Delete Data')
                    ->setUseButtonTag(true)
                    ->setTitle('Remove Data')
                    ->addExtraClass('button')
            ),
            new RequiredFields([
                'FirstName',
                'LastName',
                'Email'
            ])
        );

        $form->setFormAction(Controller::join_links(BASE_URL, 'data-control', 'verify'));
        return $form;
    }
    public function getControllerName()
    {
        return DataControlFormController::class;
    }
}

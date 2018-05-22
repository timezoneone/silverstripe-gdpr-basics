<?php

class DataControlFormController extends Page_Controller  {

    public function init() {
        parent::init();
    }

    private static $allowed_actions = array(
        'index',
        'verify',
        'confirm'
    );

    public function index(){

        $config = SiteConfig::current_site_config();

        if(!($config->GDPRIsActive && $config->DataControlFormsActive)){

           return $this->httpError(404);

       }

        $Page = Page::create();
        $Page->ID = -1 * rand(1,10000000);
        $controller = Page_Controller::create($Page);
        $controller->init();

        $Page = $controller->customise(array(
            'Title' => 'Data Control',
            'Content' => DBField::create_field('HTMLText', '<p>Use the form below to submit a request for a copy of your data or to have your data removed.</p>'),
            'Form' => $this->DataRequestForm()
        ));

        return $Page->renderWith('Page');

    }

    public function verify($request){

        $config = SiteConfig::current_site_config();

        $formData = $request->postVars();

        if(empty($formData) || !($config->GDPRIsActive && $config->DataControlFormsActive)){

           return $this->httpError(404);

        }else{
            //store record in Database.
            $record = new DataControlRequest();
            $record->FirstName  = filter_var($formData['FirstName'], FILTER_SANITIZE_STRING);
            $record->LastName   = filter_var($formData['LastName'], FILTER_SANITIZE_STRING);
            $record->Email      = filter_var($formData['Email'], FILTER_SANITIZE_EMAIL);
            $record->Verification = filter_var($formData['SecurityID'], FILTER_SANITIZE_STRING);
            $record->RequiredAction  = isset($formData['action_RemoveData']) ? 'Delete Data' : 'Provide data';
            $record->Status     = 'Awaiting Verification';
            $record->write();

            $UsersRequest = isset($formData['action_RemoveData']) ? 'delete your data' : 'provide a copy of your data';

            $Page = Page::create();
            $Page->ID = -1 * rand(1,10000000);
            $controller = Page_Controller::create($Page);
            $controller->init();
            $config = SiteConfig::current_site_config();
            $dataProtectionOfficer = $config->DataProtectionOfficer();

            $Page = $controller->customise(array(
                'Title' => 'Confirm ownership',
                'Content' => DBField::create_field('HTMLText', '<p>Before we can action your request to '.$UsersRequest.', we need to verify that you are the owner of this email address. Please click the verification link in the email we\'ve sent you.</p><p>If you have any trouble, please contact our <a href="mailto:'.$dataProtectionOfficer->Email.'">Data Protection Officer, '. $dataProtectionOfficer->FirstName.' '. $dataProtectionOfficer->LastName.'</a>')
            ));

            return $Page->renderWith('Page');

        }

    }

    public function confirm($request){

        $data = $request->getVars();

        if( empty($data) || !isset($data['verification']) || !isset($data['request']) || !($config->GDPRIsActive && $config->DataControlFormsActive)){

           return $this->httpError(404);

        }else{

            $Verification = filter_var($data['verification'], FILTER_SANITIZE_SPECIAL_CHARS);
            $ID = filter_var($data['request'], FILTER_SANITIZE_SPECIAL_CHARS);

            //look up record in database..
            $record = DataControlRequest::get()->filter(array('Verification'=>$Verification, 'ID'=> $ID))->First();

            if($record->Exists()){
                //update record in Database.
                $record->Status     = 'Ready to action';
                $record->write();
            }

            $UsersRequest = isset($formData['action_RemoveData']) ? 'delete your data' : 'provide a copy of your data';
            $Page = Page::create();
            $Page->ID = -1 * rand(1,10000000);
            $controller = Page_Controller::create($Page);
            $controller->init();
            $config = SiteConfig::current_site_config();
            $dataProtectionOfficer = $config->DataProtectionOfficer();

            $Page = $controller->customise(array(
                'Title' => 'Request Received',
                'Content' => DBField::create_field('HTMLText', '<p>Thank you. Your request to '.$UsersRequest.' has been received. Our <a href="mailto:'.$dataProtectionOfficer->Email.'">Data Protection Officer</a> will be in touch.</p>')
            ));

            return $Page->renderWith('Page');

        }

    }

    public function DataRequestForm(){
        $form = new Form($this, 'request',
            new FieldList(
                TextField::create('FirstName', 'First Name'),
                TextField::create('LastName', 'Last Name'),
                EmailField::create('Email', 'Email')
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

}
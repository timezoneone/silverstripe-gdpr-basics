<?php

class DataControlFormController extends Page_Controller  {

    public function init() {
        parent::init();
    }

    private static $allowed_actions = array(
        'index',
        'verify'
    );

    public function index(){

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

        $formData = $request->postVars();

        //array(5) { ["FirstName"]=> string(4) "Test" ["LastName"]=> string(4) "TEst" ["Email"]=> string(21) "david@timezoneone.com" ["SecurityID"]=> string(40) "7a05edc54b187530991c984ab12819be57a401e8" ["action_RequestData"]=> string(12) "Request Data" }

        // array(5) { ["FirstName"]=> string(4) "Test" ["LastName"]=> string(4) "TEst" ["Email"]=> string(21) "david@timezoneone.com" ["SecurityID"]=> string(40) "7a05edc54b187530991c984ab12819be57a401e8" ["action_RemoveData"]=> string(11) "Remove Data" }

        if(empty($formData)){

           return $this->httpError(404);

        }else{

            //store record in Database.
            //send verification email.
            $UsersRequest = isset($formData->action_RemoveData) ? 'delete your data' : 'provide a copy of your data';

            $Page = Page::create();
            $Page->ID = -1 * rand(1,10000000);
            $controller = Page_Controller::create($Page);
            $controller->init();

            $Page = $controller->customise(array(
                'Title' => 'Confirm ownership',
                'Content' => DBField::create_field('HTMLText', '<p>Before we can action your request to '.$UsersRequest.', we need to verify that you are the owner of this email address. Please click the verification link in the email we\'ve sent you.</p><p>If you have any trouble, please contact our <a href="mailto:">Data Protection Officer</a>'),
                'Form' => $this->DataRequestForm()
            ));

            return $Page->renderWith('Page');

        }

    }

    public function RequestData(){
        var_dump('RequestData');
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
                FormAction::create('RemoveData', '\DELETE DATA')
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
<?php
namespace Login\Controller;

use Login\Forms\Login\Login;
use Login\Forms\Login\LoginValidator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class LoginController extends AbstractActionController
{

    /**
     * @description creates a login form and processes said form.
     * @return ViewModel
     */
    public function LoginAction()
    {

        //Check if the user is already logged in if they are redirect them
        $auth = $this->getServiceLocator()->get('AuthService');
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('index');
        }

        $form = new Login();
        $request = $this->getRequest();

        if ($request->isPost()) {

            //Validate the form
            $formValidator = new LoginValidator();
            {
                $form->setInputFilter($formValidator->getInputFilter());
                $form->setData($request->getPost());
            }

            if ($form->isValid()) {

                $formData = $form->getData();
                $dbAdapter = 'Database connection info';

                /*Check the details against a db/api or whatever it is you need to check with */
                $authAdapter = new AuthAdapter($dbAdapter,
                    'users', //Table name
                    'username', //Username column
                    'password' //Password column
                );

                $authAdapter->setIdentity($formData['username']);
                $authAdapter->setCredential(md5($formData['password']));
                // Perform the authentication query, saving the result
                $result = $auth->authenticate($authAdapter);

                //Write the data to a storage object
                if ($result->isValid()) {
                    $data = $authAdapter->getResultRowObject(null,'password');
                    $auth->getStorage()->write(
                        $data
                    );

                    return $this->redirect()->toRoute('index');

                }

            }

            $this->flashMessenger()->addErrorMessage(
                'Validation failed'
            );


        }

        $viewModel = new ViewModel(
            array(
                'form' => $form,
                'errorMessages' => $this->flashMessenger()->getErrorMessages(),
                'successMessages' => $this->flashMessenger()->getCurrentSuccessMessages(),
            )
        );

        $viewModel->setTerminal(true); //Remove this if you want your layout to be shown

        return $viewModel;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Nimol
 * Date: 12/8/2014
 * Time: 3:11 PM
 */

namespace Login\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LogoutController extends AbstractActionController
{
    /**
     * @description logs a user out of the application
     * @return ViewModel
     */
    public function LogoutAction()
    {
        $auth = $this->getServiceLocator()->get('AuthService');
        $auth->clearIdentity();

        $this->flashmessenger()->addSuccessMessage("You've been logged out");
        return $this->redirect()->toRoute('login');
    }
}
<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }    
    
    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {

            $user->sendActivationEmail();			
			
			if (($user->saveIncomes()) && ($user->saveExpenses()) && ($user->savePayments())) {
				
				$this->redirect('/signup/success');
			}
		
        } else {

            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }

    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
	
	//Activate a new account
	public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');        
    }
	
	//Show the activation success page
	public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }	
}

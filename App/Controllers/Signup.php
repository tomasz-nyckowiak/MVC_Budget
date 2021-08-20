<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\DefaultCategories;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {

            $def_cat = new DefaultCategories();
			
			$ID = DefaultCategories::getUserID();
			
			if ($def_cat->save($ID)) {
				
				$this->redirect('/signup/success');
			}
			//$this->redirect('/signup/success');			

        } else {

            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);

        }
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
}

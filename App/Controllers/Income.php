<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;

class Income extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Income/new.html');
    }
	
	public function addAction()
    {        
		$income = new Incomes($_POST);				
		
		if ($income->save()) {

            $this->redirect('/income/success');			

        } else {

            View::renderTemplate('Income/new.html', [
                'income' => $income
            ]);

        }        
    }
	
	public function successAction()
    {
        View::renderTemplate('Income/success.html');
    }
	
}

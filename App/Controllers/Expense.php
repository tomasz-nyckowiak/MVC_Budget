<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expenses;

class Expense extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Expense/new.html');
    }
	
	public function addAction()
    {        
		$expense = new Expenses($_POST);				
		
		if ($expense->save()) {

            $this->redirect('/expense/success');			

        } else {

            View::renderTemplate('Expense/new.html', [
                'expense' => $expense
            ]);

        }        
    }
	
	public function successAction()
    {
        View::renderTemplate('Expense/success.html');
    }
}

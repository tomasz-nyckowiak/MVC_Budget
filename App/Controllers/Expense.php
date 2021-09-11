<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expenses;
use \App\Dates;

class Expense extends Authenticated
{
    public function newAction()
    {
        $ID = $_SESSION['user_id'];
		$categories = [];
		
		$categories['names'] = Expenses::categoriesAssignedToUser($ID);		
		$categories['payments'] = Expenses::paymentsMethodsAssignedToUser($ID);
		$categories['today'] = Dates::currentDate();
		View::renderTemplate('Expense/new.html', $categories);
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

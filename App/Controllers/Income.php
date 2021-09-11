<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Incomes;
use \App\Dates;

class Income extends Authenticated
{
    public function newAction()
    {
		$ID = $_SESSION['user_id'];
		$categories = [];
		
		$categories['names'] = Incomes::categoriesAssignedToUser($ID);
		$categories['today'] = Dates::currentDate();
		View::renderTemplate('Income/new.html', $categories);		
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

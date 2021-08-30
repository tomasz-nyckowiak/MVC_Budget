<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balancesheet;
use \App\Models\DefaultCategories;

class Balance extends \Core\Controller
{
    public function showAction()
    {
        $balance = new Balancesheet();
		
		$ID = $_SESSION['user_id'];
		$arguments = [];
		//$arguments2 = [];
		
		//$arguments['incomes'] = Balancesheet::step1($ID);
		//$arguments['test'] = Balancesheet::step2($ID);
		//$arguments['krok3'] = Balancesheet::step3($ID);
		$arguments['incomes'] = Balancesheet::step4($ID);
		$arguments['expenses'] = Balancesheet::step4Expenses($ID);
		$arguments['finalSums'] = Balancesheet::totalAmount($ID);
		$arguments['chart'] = Balancesheet::chart($ID);
		View::renderTemplate('Balance/show.html', $arguments);
		
		//$arguments['incomes'] = Balance::showIncomes($firstDay, $lastDay);
		//View::renderTemplate('Balance/show.html');
		
		/*if ($balance->showIncomes($ID)) {
			$this->redirect('/balance/show');
		}*/
		
		/*if ($balance->showIncomes($ID)) {

            View::renderTemplate('Balance/show.html');			

        } else {

            View::renderTemplate('Balance/error.html');
        }*/
		
    }
}

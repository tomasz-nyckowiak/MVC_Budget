<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balancesheet;
use \App\Dates;

class Balance extends Authenticated
{
    public function showAction()
    {
        $balance = new Balancesheet();
		
		$ID = $_SESSION['user_id'];
		$arguments = [];
		
		$arguments['incomes'] = $balance->IncomesFinalStep($ID);
		$arguments['expenses'] = $balance->ExpensesFinalStep($ID);
		$arguments['finalSums'] = $balance->totalAmounts($ID);		
		$arguments['message'] = Dates::showMessage();		
		$arguments['pieChart'] = $balance->chart($ID);
		
		View::renderTemplate('Balance/show.html', $arguments);
    }
}

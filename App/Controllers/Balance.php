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
		
		$arguments['message'] = Dates::showMessage();
		$arguments['incomes'] = $balance->getIncomes($ID);
		$arguments['expenses'] = $balance->getExpenses($ID);
		$arguments['finalSums'] = $balance->totalAmounts($ID);			
		$arguments['pieChart'] = $balance->chart($ID);
		
		View::renderTemplate('Balance/show.html', $arguments);
    }
}

<?php

namespace App\Models;

use PDO;
use \App\Dates;

class Balancesheet extends \Core\Model
{	
	//Wyliczamy sumy kwot dla wszystkich kategorii przychodów dla zadanego okresu czasu
	public function getIncomes($user_id)
	{		
		$dates = [];
		$dates = Dates::showMessage();
		
		$tab = [];
		foreach ($dates as $key => $value) {			
			array_push($tab, "$value");			
		}
				
		$date_one = $tab[0];
		$date_two = $tab[1];
		
		$sql = "SELECT incomes.income_category_assigned_to_user_id AS number, SUM(incomes.amount) AS sum, incomes_categories_assigned_to_users.name FROM incomes, incomes_categories_assigned_to_users WHERE incomes.date_of_income BETWEEN '$date_one' AND '$date_two' AND incomes.user_id = '$user_id' AND incomes.user_id = incomes_categories_assigned_to_users.user_id AND incomes.income_category_assigned_to_user_id = incomes_categories_assigned_to_users.id GROUP BY incomes.income_category_assigned_to_user_id";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$incomes = $stmt->fetchAll();
		
		return $incomes;
	}	

	//Wyliczamy sumy kwot dla wszystkich kategorii wydatków dla zadanego okresu czasu
	public function getExpenses($user_id)
	{		
		$dates = [];
		$dates = Dates::showMessage();
		
		$tab = [];
		foreach ($dates as $key => $value) {			
			array_push($tab, "$value");			
		}
				
		$date_one = $tab[0];
		$date_two = $tab[1];
		
		$sql = "SELECT expenses.expense_category_assigned_to_user_id AS number, SUM(expenses.amount) AS sum, expenses_categories_assigned_to_users.name FROM expenses, expenses_categories_assigned_to_users WHERE expenses.date_of_expense BETWEEN '$date_one' AND '$date_two' AND expenses.user_id = '$user_id' AND expenses.user_id = expenses_categories_assigned_to_users.user_id AND expenses.expense_category_assigned_to_user_id = expenses_categories_assigned_to_users.id GROUP BY expenses.expense_category_assigned_to_user_id";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$expenses = $stmt->fetchAll();
		
		return $expenses;
	}
	
	//Wyliczamy sumę całkowitą przychodów i wydatków
	public function totalAmounts($user_id)
	{		
		$sums_of_incomes_categories = Balancesheet::getIncomes($user_id);		
		$incomes_total_amount = 0;		
			
		foreach ($sums_of_incomes_categories as ["sum" => $value]) {			
			$incomes_total_amount += $value;
		}
	
		$sums_of_expenses_categories = Balancesheet::getExpenses($user_id);		
		$expenses_total_amount = 0;		
		
		foreach ($sums_of_expenses_categories as ["sum" => $value]) {			
			$expenses_total_amount += $value;
		}		
		
		//Bilans końcowy (przychody - wydatki)
		$balance_sheet = $incomes_total_amount - $expenses_total_amount;
		
		if ($balance_sheet > 0) {
			$final_message = "Gratulacje. Świetnie zarządzasz finansami!";
		}
		else if ($balance_sheet < 0) {
			$final_message = "Uważaj, wpadasz w długi!";
		}
		else if ($balance_sheet == 0) {
			$final_message = "Wyszedłeś na 0!";
		}
		
		$total_amounts = [];
		
		$amounts['totalIncomes'] = $incomes_total_amount;
		$amounts['totalExpenses'] = $expenses_total_amount;
		$amounts['total'] = $balance_sheet;
		$amounts['message'] = $final_message;
		$total_amounts[] = $amounts;
		
		return $total_amounts;		
	}
	
	//Wykres / CHART
	public function chart($user_id)
	{
		$tab_expenses = [];		
		$tab_expenses = Balancesheet::getExpenses($user_id);
		
		$tab_size_expenses = count($tab_expenses);					
		
		foreach ($tab_expenses as ["sum" => $amount, "name" => $name]) {			
			$tab_expenses['name'] = $name;
			$tab_expenses['sum'] = $amount;								
		}		
		
		$expenses = [];
		$expensesArray = [];
		$expensesOnChart = [];
		
		for ($y = 0; $y < $tab_size_expenses; $y++) {			
			$forChart['category'] = $tab_expenses[$y]['name'];
			$forChart['amount'] = $tab_expenses[$y]['sum'];
			$expenses[] = $forChart;						
		}
		
		$expensesArray = json_decode(json_encode($expenses), True);
		
		foreach ($expensesArray as $chartPie) {					
			array_push($expensesOnChart, array("label"=>$chartPie['category'], "y"=>$chartPie['amount']));		
		}
		
		json_encode($expensesOnChart, JSON_NUMERIC_CHECK);
		
		return $expensesArray;		
	}
}

?>
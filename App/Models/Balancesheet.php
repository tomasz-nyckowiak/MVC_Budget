<?php

namespace App\Models;

use PDO;

//Balance model
class Balancesheet extends \Core\Model
{
	public $errors = [];
		
	public function __construct($data = [])
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		};
	}	
	
	//Wyciągamy kategorie przychodów i odpowiadające im numery id dla danego użytkownika i wstawiamy do osobnych tablic
	public static function step1($user_id)
	{
		$sql = "SELECT id, name FROM incomes_categories_assigned_to_users WHERE user_id = '$user_id'";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$i = 0;		
		$incomes_array = array();	
		$tab_id_incomes = array();
		$incomes_categories = array();			
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						
			$incomes_array[$i]['id'] = $row['id'];
			$incomes_array[$i]['name'] = $row['name'];
			$temp_id_inc = $incomes_array[$i]['id'];
			$temp_incomes = $incomes_array[$i]['name'];
			array_push($tab_id_incomes, "$temp_id_inc");
			array_push($incomes_categories, "$temp_incomes");
			$i++;
		}		
		
		return array($tab_id_incomes, $incomes_categories);
	}
	
	//Zwrócenie tablicy numerów id, odpowiadających danej kategorii przychodów (jeśli znajdują się w tabeli) dla danego użytkownika z tabeli "przychody"
	public static function step2($user_id)
	{
		$sql = "SELECT DISTINCT income_category_assigned_to_user_id FROM incomes WHERE user_id = '$user_id' GROUP BY income_category_assigned_to_user_id";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$existing_numbers_id_of_categories_in_incomes = array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$id_number_of_income_category = $row['income_category_assigned_to_user_id'];
			array_push($existing_numbers_id_of_categories_in_incomes, "$id_number_of_income_category");			
		}
		
		return $existing_numbers_id_of_categories_in_incomes;
	}
	
	//Wyliczamy sumy kwot dla wszystkich kategorii przychodów dla zadanego okresu czasu i wstawiamy do tablicy
	public static function step3($user_id)
	{
		$date_one = "2021-08-01";
		$date_two = "2021-08-31";
		
		$sql = "SELECT income_category_assigned_to_user_id, SUM(amount) AS sum FROM incomes WHERE date_of_income BETWEEN '$date_one' AND '$date_two' AND user_id = '$user_id' GROUP BY income_category_assigned_to_user_id";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$sums_of_incomes_categories = array();		
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{				
			$temp_sum_incomes = $row['sum'];			
			array_push($sums_of_incomes_categories, "$temp_sum_incomes");			
		}
		
		return $sums_of_incomes_categories;
	}
	
	//Jeśli kategoria (jej nr id) znajduje się w tablicy "przychody", to odpowiadająca numerowi id kwota zostanie wyciągnięta z tablicy sum i przypisana do nowej zmiennej, a następnie wstawiona do finalnej tablicy; jeśli jej nie ma, to przypisujemy jej wartość 0
	public static function step4($user_id)
	{	
		$incomes_array = [];		
		$incomes_array = Balancesheet::step1($user_id);
		list($firstArray, $secondArray) = $incomes_array;
		
		$tab_size_incomes = count($firstArray);
		$incomes_id_for_categories = [];
		$incomes_categories = [];
		
		foreach ($firstArray as $key => $value) {			
			array_push($incomes_id_for_categories, "$value");			
		}
		
		foreach ($secondArray as $key => $value) {			
			array_push($incomes_categories, "$value");			
		}
				
		$existing_numbers_id_of_categories_in_incomes = [];		
		$existing_numbers_id_of_categories_in_incomes = Balancesheet::step2($user_id);		
		
		$sums_of_incomes_categories = [];		
		$sums_of_incomes_categories = Balancesheet::step3($user_id);
				
		$tab_incomes = [];
		
		for ($x = 0; $x < $tab_size_incomes; $x++)
		{				
			if (in_array("$incomes_id_for_categories[$x]",  $existing_numbers_id_of_categories_in_incomes))
			{
				$pulled_out_amount_inc = array_shift($sums_of_incomes_categories);
				if (is_null($pulled_out_amount_inc)) $pulled_out_amount_inc = 0;
				$tab_incomes[$x] = $pulled_out_amount_inc;
			}
			else $tab_incomes[$x] = 0;				
		}
		
		$final_tab_for_incomes = [];
		
		for ($x = 0; $x < $tab_size_incomes; $x++)
		{				
			if ($tab_incomes[$x] != 0)
			{
				$income['category'] = $incomes_categories[$x];
				$income['amount'] = $tab_incomes[$x];
				$final_tab_for_incomes[] = $income;																				
			}				
		}		
		
		return $final_tab_for_incomes;
	}	
	
	//Wydatki / EXPENSES
	/*public static function showExpenses($user_id)
    {		
		if (empty($this->errors)) {

            
        }

        return false;
    }*/
	
	//Wyciągamy kategorie wydatków i odpowiadające im numery id dla danego użytkownika i wstawiamy do osobnych tablic
	public static function step1Expenses($user_id)
	{
		$sql = "SELECT id, name FROM expenses_categories_assigned_to_users WHERE user_id = '$user_id'";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$i = 0;		
		$expenses_array = [];	
		$tab_id_expenses = [];
		$expenses_categories = [];			
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						
			$expenses_array[$i]['id'] = $row['id'];
			$expenses_array[$i]['name'] = $row['name'];
			$temp_id_exp = $expenses_array[$i]['id'];
			$temp_expenses = $expenses_array[$i]['name'];
			array_push($tab_id_expenses, "$temp_id_exp");
			array_push($expenses_categories, "$temp_expenses");
			$i++;
		}		
		
		return array($tab_id_expenses, $expenses_categories);		
	}
	
	//Zwrócenie tablicy numerów id, odpowiadających danej kategorii wydatków (jeśli znajdują się w tabeli) dla danego użytkownika z tabeli "wydatki"
	public static function step2Expenses($user_id)
	{
		$sql = "SELECT DISTINCT expense_category_assigned_to_user_id FROM expenses WHERE user_id = '$user_id' GROUP BY expense_category_assigned_to_user_id";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$existing_numbers_id_of_categories_in_expenses = [];
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$id_number_of_expense_category = $row['expense_category_assigned_to_user_id'];
			array_push($existing_numbers_id_of_categories_in_expenses, "$id_number_of_expense_category");			
		}
		
		return $existing_numbers_id_of_categories_in_expenses;
	}
	
	//Wyliczamy sumy kwot dla wszystkich kategorii wydatków dla zadanego okresu czasu i wstawiamy do tablicy
	public static function step3Expenses($user_id)
	{
		$date_one = "2021-08-01";
		$date_two = "2021-08-31";
		
		$sql = "SELECT expense_category_assigned_to_user_id, SUM(amount) AS sum FROM expenses WHERE date_of_expense BETWEEN '$date_one' AND '$date_two' AND user_id = '$user_id' GROUP BY expense_category_assigned_to_user_id";					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		
		$sums_of_expenses_categories = [];		
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{				
			$temp_sum_expenses = $row['sum'];			
			array_push($sums_of_expenses_categories, "$temp_sum_expenses");			
		}
		
		return $sums_of_expenses_categories;
	}
	
	//Jeśli kategoria (jej nr id) znajduje się w tablicy "wydatki", to odpowiadająca numerowi id kwota zostanie wyciągnięta z tablicy sum i przypisana do nowej zmiennej, a następnie wstawiona do finalnej tablicy; jeśli jej nie ma, to przypisujemy jej wartość 0
	public static function step4Expenses($user_id)
	{	
		$expenses_array = [];		
		$expenses_array = Balancesheet::step1Expenses($user_id);
		list($firstArray, $secondArray) = $expenses_array;
		
		$tab_size_expenses = count($firstArray);
		$expenses_id_for_categories = [];
		$expenses_categories = [];
		
		foreach ($firstArray as $key => $value) {			
			array_push($expenses_id_for_categories, "$value");			
		}
		
		foreach ($secondArray as $key => $value) {			
			array_push($expenses_categories, "$value");			
		}
				
		$existing_numbers_id_of_categories_in_expenses = [];		
		$existing_numbers_id_of_categories_in_expenses = Balancesheet::step2Expenses($user_id);		
		
		$sums_of_expenses_categories = [];		
		$sums_of_expenses_categories = Balancesheet::step3Expenses($user_id);
				
		$tab_expenses = [];
		
		for ($x = 0; $x < $tab_size_expenses; $x++)
		{				
			if (in_array("$expenses_id_for_categories[$x]",  $existing_numbers_id_of_categories_in_expenses))
			{
				$pulled_out_amount_exp = array_shift($sums_of_expenses_categories);
				if (is_null($pulled_out_amount_exp)) $pulled_out_amount_exp = 0;
				$tab_expenses[$x] = $pulled_out_amount_exp;
			}
			else $tab_expenses[$x] = 0;				
		}
		
		$final_tab_for_expenses = [];
		$i = 0;
		
		for ($x = 0; $x < $tab_size_expenses; $x++)
		{				
			if ($tab_expenses[$x] != 0)
			{
				//$expense['category'] = $expenses_categories[$x];
				//$expense['amount'] = $tab_expenses[$x];
				//$final_tab_for_expenses[] = $expense;	
				$final_tab_for_expenses[$i]['category'] = $expenses_categories[$x];
				$final_tab_for_expenses[$i]['amount'] = $tab_expenses[$x];
				$i++;
			}				
		}		
		
		return $final_tab_for_expenses;
	}
	
	//Wyliczamy sumę całkowitą przychodów i wydatków
	public static function totalAmount($user_id)
	{
		$sums_of_incomes_categories = [];		
		$sums_of_incomes_categories = Balancesheet::step3($user_id);
		
		$incomes_total_amount = 0;		
		
		foreach ($sums_of_incomes_categories as $key => $value) {			
			$incomes_total_amount += $value;
		}
		
		$sums_of_expenses_categories = [];		
		$sums_of_expenses_categories = Balancesheet::step3Expenses($user_id);
		
		$expenses_total_amount = 0;		
		
		foreach ($sums_of_expenses_categories as $key => $value) {			
			$expenses_total_amount += $value;
		}
				
		$total_amounts = [];
		
		//Bilans końcowy (przychody - wydatki)
		$balance_sheet = $incomes_total_amount - $expenses_total_amount;
		
		if ($balance_sheet > 0)
		{
			$final_message = "Gratulacje. Świetnie zarządzasz finansami!";
		}
		else if ($balance_sheet < 0)
		{
			$final_message = "Uważaj, wpadasz w długi!";
		}
		else if ($balance_sheet == 0)
		{
			$final_message = "Wyszedłeś na 0!";
		}
		
		$amounts['totalIncomes'] = $incomes_total_amount;
		$amounts['totalExpenses'] = $expenses_total_amount;
		$amounts['total'] = $balance_sheet;
		$amounts['message'] = $final_message;
		$total_amounts[] = $amounts;
		
		return $total_amounts;
	}
	
	//Wykres / CHART
	public static function chart($user_id)
	{		
		$tab_expenses = [];		
		$tab_expenses = Balancesheet::step4Expenses($user_id);
		
		$tab_size_expenses = count($tab_expenses);					
		
		foreach ($tab_expenses as $key => $value) {			
			$tab_expenses['category'] = $key;
			$tab_expenses['amount'] = $value;								
		}

		/*for ($y = 0; $y < $tab_size_expenses; $y++)
		{			
			echo $tab_expenses[$y]['category'] . " : " . $tab_expenses[$y]['amount'];
			echo "<br>";
		}*/
		
		$expenses = [];
		$expensesOnChart = [];
		
		for ($y = 0; $y < $tab_size_expenses; $y++)
		{			
			$forChart['category'] = $tab_expenses[$y]['category'];
			$forChart['amount'] = $tab_expenses[$y]['amount'];
			$expenses[] = $forChart;
			
			/*if ($tab_expenses[$y] != 0)
			{			
				$forChart['category'] = "$expenses_categories[$y]";
				$forChart['amount'] = "$tab_expenses[$y]";
				$expenses[] = $forChart;
			}*/			
		}
		
		foreach ($expenses as $chartPie) {					
			array_push($expensesOnChart, array("label"=>$chartPie['category'], "y"=>$chartPie['amount']));		
		}
		
		return $expensesOnChart;
	}
	
	
	
	
}

?>
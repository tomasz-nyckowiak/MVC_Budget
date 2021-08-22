<?php

namespace App\Models;

use PDO;

//Expense model
class Expenses extends \Core\Model
{
	public $errors = [];
		
	public function __construct($data = [])
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		};
	}
	
	public function save()
    {		
		if (empty($this->errors)) {

            $user_id = $_SESSION['user_id'];
			$amount = $_POST['amount'];
			$date = $_POST['date'];
			$payment_method = $_POST['paymentsMethods'];
			$chosen_category = $_POST['inlineRadioOptions'];
			$comment = $_POST['comment'];
			
			$category_id = Expenses::copyNumberOfCategory($user_id, $chosen_category);		
			$payment_id = Expenses::copyNumberOfPaymentMethod($user_id, $payment_method);		

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
                    VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $category_id, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $payment_id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', $date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function copyNumberOfCategory($ID, $category)
	{
		$sql = "SELECT id FROM 	expenses_categories_assigned_to_users WHERE user_id = '$ID' AND name = '$category'";
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);		
		
		$col = implode(" ", $row);
		return $col;
	}
	
	public function copyNumberOfPaymentMethod($ID, $payment)
	{
		$sql = "SELECT id FROM 	payments_methods_assigned_to_users WHERE user_id = '$ID' AND name = '$payment'";
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);		
		
		$col = implode(" ", $row);
		return $col;
	}
}

?>
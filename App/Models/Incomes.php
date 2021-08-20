<?php

namespace App\Models;

use PDO;

//Income model
class Incomes extends \Core\Model
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
			$chosen_category = $_POST['gridRadios'];
			$comment = $_POST['comment'];
			
			$category_id = Incomes::copyNumberOfCategory($user_id, $chosen_category);			

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
                    VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':income_category_assigned_to_user_id', $category_id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', $date, PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	public function copyNumberOfCategory($ID, $category)
	{
		$sql = "SELECT id FROM 	incomes_categories_assigned_to_users WHERE user_id = '$ID' AND name = '$category'";
		
		$db = static::getDB();
        $stmt = $db->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);		
		
		$col = implode(" ", $row);
		return $col;
	}
}

?>
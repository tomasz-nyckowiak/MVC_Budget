<?php

namespace App\Models;

use PDO;

//DefaultCategories model
class DefaultCategories extends \Core\Model
{
	public $errors = [];
		
	public function __construct($data = [])
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		};
	}	
	
	public static function getUserID()
	{
		$sql = 'SELECT id FROM users ORDER BY id DESC LIMIT 1';					

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		//convert the array content to string and store in variable 
		$col = implode(" ", $row);
		return $col;
	}
	
	public function save($user_id)
    {		
		if (empty($this->errors)) {

            $sql = "INSERT INTO incomes_categories_assigned_to_users (user_id, name) SELECT users.id, incomes_default_categories.name FROM users, incomes_default_categories WHERE users.id = '$user_id'";					

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            return $stmt->execute();
        }

        return false;
    }
}

?>
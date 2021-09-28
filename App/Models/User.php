<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Emails;
use \Core\View;

class User extends \Core\Model
{
    //Error messages
    public $errors = [];
    
    //Class constructor
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    //Save the user model with the current property values
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
			
			$token = new Token();
			$hashed_token = $token->getHash();
			$this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (username, email, password, activation_hash)
                    VALUES (:name, :email, :password_hash, :activation_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    //Validate current property values, adding valiation error messages to the errors array property
    public function validate()
    {
        //Name		
		if ((strlen($this->name)<3) || (strlen($this->name)>20))
		{			
			$this->errors['name'] = "Imię musi posiadać od 3 do 20 znaków!";
		}

        //Email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['email'] = 'Podaj poprawny adres e-mail!';
        }
        if (static::emailExists($this->email)) {
            $this->errors['email'] = 'Istnieje już konto przypisane do tego adresu e-mail!';
        }

        //Password
        if (strlen($this->password) < 6) {
            $this->errors['pass'] = 'Hasło musi posiadać co najmniej 6 znaków!';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors['pass'] = 'Hasło musi posiadać przynajmniej 1 literę!';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors['pass'] = 'Hasło musi posiadać przynajmniej 1 cyfrę!';
        }
		
		//Terms
		if (!isset($_POST['terms']))
		{			
			$this->errors['terms'] = "Potwierdź akceptację regulaminu!";
		}
		
		//Are you a Bot?
		$secret = "6LdPfn4bAAAAAFcstiMgxbhsgjhILw4RjK_YWcbd";
		
		$check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
		
		$response = json_decode($check);
		
		if ($response->success==false)
		{			
			$this->errors['bot'] = "Potwierdź, że nie jesteś botem!";
		}		
    }

    //See if a user record already exists with the specified email
    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    //Find a user model by email address
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    //Authenticate a user by email and password
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user && $user->is_active) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }
	
	//Find a user model by ID
	public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    //Remember the login by inserting a new unique token into the remembered_logins table
	 public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  //30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
	
	//Send an email to the user containing the activation link
	public function sendActivationEmail()
	{
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Emails::send($this->email, 'Aktywacja konta', $text, $html);
	}
	
	//Activate the user account with the specified activation token
	public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users SET is_active = 1, activation_hash = null WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();                
    }
	
	/*Copy default data to proper records:
	- incomes_default_categories --> incomes_categories_assigned_to_users
	- expenses_default_categories --> expenses_categories_assigned_to_users
	- payments_deafult_methods --> payments_methods_assigned_to_users						
	*/
	
	//Incomes
	public function saveIncomes()
    {		
		if (empty($this->errors)) {

            $sql = "INSERT INTO incomes_categories_assigned_to_users (user_id, name) SELECT users.id, incomes_default_categories.name FROM users, incomes_default_categories WHERE users.email = :email";					

            $db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	//Expenses
	public function saveExpenses()
    {		
		if (empty($this->errors)) {

            $sql = "INSERT INTO expenses_categories_assigned_to_users (user_id, name) SELECT users.id, expenses_default_categories.name FROM users, expenses_default_categories WHERE users.email = :email";					

            $db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
	
	//Payments
	public function savePayments()
    {		
		if (empty($this->errors)) {

            $sql = "INSERT INTO payments_methods_assigned_to_users (user_id, name) SELECT users.id, payments_deafult_methods.name FROM users, payments_deafult_methods WHERE users.email = :email";					

            $db = static::getDB();
            $stmt = $db->prepare($sql);
			
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }	
}

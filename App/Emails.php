<?php

namespace App;

ini_set('SMTP', 'smtp.gmail.com');
ini_set('smtp_port', 465);
 
class Emails
{	
	public static function send($to, $subject, $message, $header)
	{	
		$header = "From: twoj@email.com \nContent-Type:".
						' text/html;charset="UTF-8"'.
						"\nContent-Transfer-Encoding: 8bit";
		
		return mail($to, $subject, $message, $header);	
	}	
}

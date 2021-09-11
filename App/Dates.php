<?php

namespace App;
 
class Dates
{	
	public static function currentDate()
	{
		$current_date = date('Y-m-d');
		
		return $current_date;
	}

	public static function showMessage()
	{
		$temp = "showCM";
		
		if (isset($_POST['selectedCM'])) $temp = "showCM";
		if (isset($_POST['selectedPM'])) $temp = "showPM";
		if (isset($_POST['selectedCY'])) $temp = "showCY";
		if (isset($_POST['selectedCU'])) {		
			$temp1 = $_POST['date1'];
			$temp2 = $_POST['date2'];
			$first_date = date_create("$temp1");
			$second_date = date_create("$temp2");
			
			$temp = "showCU";	
		}

		$current_date = date('Y-m-d');
		$year = date('Y');
		$month = date('m');
		
		//Bieżący miesiąc / CURRENT MONTH
		if ($temp == "showCM") {
			$temp_date = date_create("$current_date");
			$proper_date =  date_format($temp_date, "Y-m");
			$days = cal_days_in_month(CAL_GREGORIAN, "$month", "$year");
			$date_one = "$proper_date-01";
			$date_two = "$proper_date-$days";				
		}			
		
		//Poprzedni miesiąc / PREVIOUS MONTH
		if ($temp == "showPM") {
			$temp_date = date_create("$current_date");
			date_modify($temp_date,"-1 month");
			$proper_month =  date_format($temp_date, "m");				
			$days = cal_days_in_month(CAL_GREGORIAN, "$proper_month", "$year");
			$date_one = "$year-$proper_month-01";
			$date_two = "$year-$proper_month-$days";				
		}
					
		//Bieżący rok / CURRENT YEAR
		if ($temp == "showCY") {
			$date_one = "$year-01-01";
			$date_two = "$year-12-31";				
		}				
		
		//Niestandardowy / CUSTOM
		if ($temp == "showCU") {
			$date_one =  date_format($first_date, "Y-m-d");
			$date_two =  date_format($second_date, "Y-m-d");							
		}		
				
		$dates = [];
		$dates['first_date'] = $date_one;
		$dates['second_date'] = $date_two;
				
		return $dates;		
	}	
}

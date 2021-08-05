<?php

namespace App\Controllers;

use \Core\View;

class Addexpense extends \Core\Controller
{
    public function addAction()
    {
        View::renderTemplate('Expense/add.html');
    }
}

<?php

namespace App\Controllers;

use \Core\View;

class Balance extends \Core\Controller
{
    public function showAction()
    {
        View::renderTemplate('Balance/show.html');
    }
}

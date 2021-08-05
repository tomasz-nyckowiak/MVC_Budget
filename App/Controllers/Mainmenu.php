<?php

namespace App\Controllers;

use \Core\View;

class Mainmenu extends \Core\Controller
{
    public function showAction()
    {
        View::renderTemplate('Mainmenu/show.html');
    }
}

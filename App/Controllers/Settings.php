<?php

namespace App\Controllers;

use \Core\View;

class Settings extends \Core\Controller
{
    public function showAction()
    {
        View::renderTemplate('Settings/show.html');
    }
}

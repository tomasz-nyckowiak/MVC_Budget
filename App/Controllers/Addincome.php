<?php

namespace App\Controllers;

use \Core\View;

class Addincome extends \Core\Controller
{
    public function addAction()
    {
        View::renderTemplate('Income/add.html');
    }
}

<?php

namespace App\Core;

use App\Core\Database\DbModel;

abstract class UserModel extends DbModel
{

    abstract public function getDisplayName (): string;

}

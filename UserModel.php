<?php

namespace Svv\Framework;

use Svv\Framework\Database\DbModel;

abstract class UserModel extends DbModel
{

    abstract public function getDisplayName (): string;

}

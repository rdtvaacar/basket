<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class User_product extends Model

{
    protected $connection = 'mysql2';
    protected $table      = 'user_product';

}

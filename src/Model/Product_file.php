<?php

namespace Acr\Ftr\Model;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Product_file extends Model

{
    protected $connection = 'mysql2';
    protected $table      = 'acr_files_childs';

}

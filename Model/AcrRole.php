<?php

namespace Acr\Ftr\Model;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;

class AcrRole extends Model

{
    protected $table = 'roles';

    protected $connection = 'mysql';
    /**
     * The database table used by the model.
     *
     * @var string
     */
}

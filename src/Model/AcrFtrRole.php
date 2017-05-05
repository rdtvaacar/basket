<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;
use DB;
use Acr\Ftr\Facades\AcrFtr;

class AcrFtrRole extends Model

{
    protected $table = 'roles';

    protected $connection = 'mysql';
    /**
     * The database table used by the model.
     *
     * @var string
     */
}

<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Product_yakas extends Model

{
    protected $connection = 'mysql2';
    protected $table      = 'product_yaka';

    /**
     * The database table used by the model.
     *
     * @var string
     */

    function yaka()
    {
        return $this->belongsTo('Acr\Ftr\Model\Yakas');
    }

}

<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Product_sizes extends Model

{
    protected $connection = 'mysql2';
    protected $table      = 'product_size';

    /**
     * The database table used by the model.
     *
     * @var string
     */

    function size()
    {
        return $this->belongsTo('Acr\Ftr\Model\Sizes');
    }

}

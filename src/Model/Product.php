<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Product extends Model

{
    protected $connection = 'mysql2';

    /**
     * The database table used by the model.
     *
     * @var string
     */

    function attributes()
    {
        return $this->belongsToMany('Acr\Ftr\Model\AcrFtrAttribute', 'attribute_product', 'product_id', 'attribute_id');
    }

    function u_kats()
    {
        return $this->belongsToMany('Acr\Ftr\Model\U_kat');
    }

    function my_product()
    {
        return $this->hasOne('Acr\Ftr\Model\Acrproduct');
    }
}

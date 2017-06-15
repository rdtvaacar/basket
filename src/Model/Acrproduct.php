<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Acrproduct extends Model

{
    protected $connection = 'mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */

    function u_kats()
    {
        return $this->belongsToMany('Acr\Ftr\Model\U_kat', 'product_u_kat', 'u_kat_id', 'product_id');
    }

    function product()
    {
        return $this->hasOne('Acr\Ftr\Model\Product', 'id', 'product_id');
    }

}

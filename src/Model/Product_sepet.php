<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Product_sepet extends Model

{
    protected $connection = 'mysql';
    protected $table      = 'product_sepet';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    function product()
    {
        return $this->hasOne('Acr\Ftr\Model\Product', 'id', 'product_id');
    }

    function acr_product()
    {
        return $this->hasOne('Acr\Ftr\Model\Acrproduct', 'product_id', 'product_id');
    }

    function sepet()
    {
        return $this->hasOne('Acr\Ftr\Model\Sepet', 'id', 'sepet_id');
    }

    function use_plus($product_id, $sepet_id)
    {
        $sorgu = Product_sepet::where('product_id', $product_id)->where('sepet_id', $sepet_id);
        $satir = $sorgu->first();
        $sorgu->update(['adet' => $satir->adet + 1]);
    }

}

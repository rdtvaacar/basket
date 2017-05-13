<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;
use DB;
use Acr\Ftr\Facades\AcrFtr;
use Acr\Ftr\Model\Acrproduct;

class Sepet extends Model

{
    protected $connection = 'mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */


    function create($session_id = null, $product_id)
    {
        $sepet_id      = self::product_sepet_id($session_id);
        $product_model = new Product();
        $product       = $product_model->where('id', $product_id)->first();
        if (empty($sepet_id)) {
            if (Auth::check()) {
                $sepet_id = Sepet::insertGetId(['user_id' => Auth::user()->id]);
            } else {
                $sepet_id = Sepet::insertGetId(['session_id' => $session_id]);
            }
        }
        if (Auth::check()) {
            Product_sepet::insert(['product_id' => $product_id, 'user_id' => Auth::user()->id, 'sepet_id' => $sepet_id, 'type' => $product->type]);

        } else {
            Product_sepet::insert(['product_id' => $product_id, 'sepet_id' => $sepet_id, 'type' => $product->type]);
        }
    }
    function product()
    {
        return $this->hasOne('Acr\Ftr\Model\Product', 'id', 'product_id');
    }
    function Acrproducts()
    {
        return $this->belongsToMany('Acr\Ftr\Model\Acrproduct', 'product_sepet', 'sepet_id', 'product_id')->withPivot('adet', 'lisans_ay');
    }

    function delete()
    {

    }

    function sepet_birle($session_id)
    {
        Sepet::where('session_id', $session_id)->where('siparis', 0)->update(['user_id' => Auth::user()->id]);
        $sepet_id = $this->sepet_birle();
        Product_sepet::where('sepet_id', $sepet_id)->update('user_id', Auth::user()->id);
    }

    function product_sepet_id($session_id = null)
    {
        if (Auth::check()) {
            $sepet_sorgu = Sepet::where('user_id', Auth::user()->id)->where('siparis', 0);
            if ($sepet_sorgu->count() > 0) {
                $sepet_id = $sepet_sorgu->first()->id;

            } else {
                $sepet_id = 0;
            }
        } else {
            $sepet_id = Sepet::where('session_id', $session_id)->where('siparis', 0)->first()->id;
        }
        return $sepet_id;
    }

    function product_sepet($session_id = null)
    {
        $sepet_id = self::product_sepet_id($session_id);
        return Product_sepet::where('sepet_id', $sepet_id)->with('product')->get();
    }

    function sepets($session_id = null)
    {

        $sepet_id = self::product_sepet_id($session_id);
        if ($sepet_id == 0) {
            return 0;
        }
        return Product_sepet::where('sepet_id', $sepet_id)->sum('adet');
    }

    function delete_all($session_id = null)
    {
        $sepet_id = self::product_sepet_id($session_id);
        return Product_sepet::where('sepet_id', $sepet_id)->delete();
    }

    function price_update($sepet_id, $total_price)
    {
        Sepet::where('id', $sepet_id)->where('siparis', 0)->update(['price' => $total_price]);
    }
}

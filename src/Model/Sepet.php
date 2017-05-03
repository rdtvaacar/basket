<?php

namespace Acr\Ftr\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;
use DB;
use Acr\Ftr\Facades\AcrFtr;

class Sepet extends Model

{
    protected $connection = 'mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */


    function create($data)
    {
        Sepet::insert($data);
    }

    function user_plus($product_id, $user_id)
    {
        $sorgu = Sepet::where('product_id', $product_id)->where('user_id', $user_id);
        $satir = $sorgu->first();
        $sorgu->update(['adet' => $satir->adet + 1]);
    }

    function session_plus($product_id, $session_id)
    {
        $sorgu = Sepet::where('product_id', $product_id)->where('session_id', $session_id);
        $satir = $sorgu->first();
        $sorgu->update(['adet' => $satir->adet + 1]);
    }

    function product()
    {
        return $this->hasOne('Acr\Ftr\Model\Product', 'id', 'product_id');
    }

    function delete()
    {

    }

    function sepet_birle($session_id)
    {
        Sepet::where('session_id', $session_id)->update(['user_id' => Auth::user()->id]);

    }

    function sepets($session_id = null)
    {

        if (Auth::check()) {
            return Sepet::where('user_id', Auth::user()->id)->sum('adet');
        } else {
            return Sepet::where('session_id', $session_id)->sum('adet');
        }
    }

    function delete_all($session_id = null)
    {

        if (Auth::check()) {
            return Sepet::where('user_id', Auth::user()->id)->delete();
        } else {
            return Sepet::where('session_id', $session_id)->delete();
        }
    }
}

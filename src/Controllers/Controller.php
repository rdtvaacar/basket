<?php

namespace Acr\Ftr\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Auth;

class Controller extends BaseController
{
    function uyariMsj($msj)
    {
        return '<div class="col-md-2"></div><div class="alert alert-danger col-md-8" style=" margin-left:auto; margin-right:auto;  text-align:center; ">' . $msj . '</div>';
    }

    static function basariliMsj($msj)
    {
        return '<div class="col-md-2"></div><div class="alert alert-success col-md-8"  style=" margin-left:auto; margin-right:auto; text-align:center; ">' . $msj . '</div>';
    }

    static function basarili()
    {
        return '<div class="alert alert-success" style=" padding:4px; margin-left:auto; margin-right:auto; width:400px; text-align:center; ">Başarıyla Güncellendi</div>';
    }

    function eklendi()
    {
        return '<div class="alert alert-success" style=" padding:4px; margin-left:auto; margin-right:auto; width:400px; text-align:center; ">Başarıyla Eklendi</div>';
    }
}

<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\acr_files;
use Acr\Ftr\Model\Acr_user;
use Acr\Ftr\Model\Acrproduct;
use Acr\Ftr\Model\Product;
use Acr\Ftr\Model\Attribute;
use Acr\Ftr\Model\Product_u_kat;
use Acr\Ftr\Model\Sepet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Acr\Ftr\Model\File_model;
use Acr\Ftr\Model\File_dosya_model;
use Auth;
use Acr\Ftr\Controllers\MailController;
use Illuminate\View\View;

class AcrFtrController extends Controller
{
    function index()
    {
        $user_model = new Acr_user();
        $products   = $user_model->find(Auth::user()->id)->products()->get();
        return View('acr_ftr::anasayfa');
    }

    function product_search($search)
    {
        $product_model = new Product();
        $products      = $product_model->where('product_name', 'like', "%$search%")->where('yayin', 1)->where('sil', 0)->get();
        return $products;
    }

    function product_search_row(Request $request)
    {
        $search   = $request->input('search');
        $products = self::product_search($search);
        $row      = '';
        foreach ($products as $product) {
            $row .= self::product_row($product);
        }
        return $row;
    }

    function product_row($product)
    {
        $row = '<tr>';
        $row .= '<td>' . $product->id . '</td>';
        $row .= '<td>' . $product->product_name . '</td>';
        foreach ($product->u_kats as $kat) {
            $row .= '<td>' . $kat->kat_isim . '</td>';
        }
        if ($product->u_kats->count() < 3) {
            $row .= '<td></td>';
            if ($product->u_kats->count() < 2) {
                $row .= '<td></td>';
                if ($product->u_kats->count() < 1) {
                    $row .= '<td></td>';
                }
            }
        }
        $row .= '<td>';
        $row .= '<div id="add_btn_' . $product->id . '">';
        if (!empty($product->my_product->id)) {
            $row .= self::delete_product_btn($product->id);

        } else {
            $row .= self::add_product_btn($product->id);;

        }
        $row .= '</div>';
        $row .= '</td>';
        $row .= '</tr>';
        return $row;
    }

    function new_product()
    {
        $product_model = new Product();
        $controller    = new AcrFtrController();
        $products      = $product_model->where('yayin', 1)->where('sil', 0)->with([
            'u_kats', 'my_product' => function ($q) {
                $q->where('sil', 0);
            }
        ])->get();
        //dd($products);
        return View('acr_ftr::new_product', compact('products', 'controller'));
    }

    function add_product(Request $request)
    {
        $acr_product_model = new Acrproduct();
        $id                = $request->input('id');
        if ($acr_product_model->where('product_id', $id)->count() > 0) {
            $acr_product_model->where('product_id', $id)->update(['sil' => 0]);
            $product_id = $id;
        } else {
            $data       = [
                'product_id' => $id,
                'user_id'    => Auth::user()->id
            ];
            $product_id = $acr_product_model->insertGetId($data);
        }


        return $this->delete_product_btn($product_id);
    }

    function delete_product(Request $request)
    {
        $acr_product_model = new Acrproduct();
        $id                = $request->input('id');
        $data              = [
            'sil' => 1
        ];
        $acr_product_model->where('product_id', $id)->update($data);
        return $this->add_product_btn($id);
    }

    function add_product_btn($product_id)
    {
        return '<span style="font-size: 16pt; color:#00AAA0; cursor:pointer;" onclick="add_product(' . $product_id . ')" class="fa fa-plus-square"></span>';
    }

    function delete_product_btn($product_id)
    {
        return '<span style="font-size: 16pt; color:#FF7A5A; cursor:pointer;" onclick="delete_product(' . $product_id . ')" class="fa fa-minus-square"></span>';
    }

    function my_product(Request $request)
    {
        $product_model = new Acrproduct();
        $sepet_model   = new Sepet();
        $controller    = new AcrFtrController();
        $products      = $product_model->where('yayin', 1)->where('sil', 0)->with([
            'u_kats'     => function ($query) {
                //  $query->where('u_kats.sil', 0)->where('u_kats.yayin', 1);
            },
            'product',
            'attributes' => function ($query) {
                $query->where('attributes.attribute_id', 0);
            }
        ])->get();
        //dd(Auth::user()->id);
        $session_id = $request->session()->get('session_id');
        if (Auth::check() && !empty($session_id)) {
            $sepet_model->sepet_birle($session_id);
            $request->session()->forget('session_id');
        }
        $sepet_count = $sepet_model->sepets($session_id);
        return View('acr_ftr::products', compact('products', 'controller', 'sepet_count'));
    }

    function attribute_modal(Request $request)
    {
        $att_model     = new Attribute();
        $product_model = new Acrproduct();
        $att_id        = $request->input('att_id');
        $product_id    = $request->input('product_id');
        $attributes    = $product_model->with([
            'attributes' => function ($query) use ($att_id) {
                $query->where('attributes.attribute_id', $att_id);
            }
        ])->where('id', $product_id)->get();
        $attribute     = $att_model->find($att_id);
        $row           = '<div class="modal-header">';
        $row           .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>';
        $row           .= '<h4 style="color: #ff1c19 " class="modal-title" id="myModalLabel">' . $attribute->att_name . '</h4>';
        $row           .= '</div>';
        $row           .= '<div class="modal-body">';
        $row           .= '<h4>Bu seçeneğin özellikleri</h4>';
        $row           .= '<ul style="list-style-image: url(/icon/16Tik.png); font-size: 14pt;">';
        foreach ($attributes[0]->attributes as $att) {
            $row .= '<li>' . $att->att_name . '</li>';
        }
        $row .= '</ul>';
        $row .= $attribute->att_text;
        $row .= '<div class="modal-footer">';
        $row .= '<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>';
        if (!empty($attribute->link)) {
            $row .= '<a href="' . $attribute->link . '" type="button" class="btn btn-primary">Detaylı İncele</a>';
        }
        $row .= '</div>';
        $row .= '</div>';
        return $row;
    }


}
<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\Sepet;
use Auth;
use Illuminate\Http\Request;
use Acr\Ftr\Model\Acrproduct;

class AcrSepetController extends Controller
{
    function index()
    {
        $user_model = new Acr_user();
        $sepets     = $user_model->find(Auth::user()->id)->sepets()->get();
        return View('acr_ftr::anasayfa');
    }

    function create(Request $request, $product_id = null)
    {
        $sepet_model = new Sepet();
        if (empty($product_id)) {
            $product_id = $request->input('product_id');
        }
        if (Auth::check()) {
            $data = [
                'user_id'    => Auth::user()->id,
                'product_id' => $product_id
            ];
            if ($sepet_model->where('product_id', $product_id)->where('user_id', Auth::user()->id)->count() > 0) {
                return $sepet_model->user_plus($product_id, Auth::user()->id);
            }
        } else {
            if (empty($request->session()->get('session_id'))) {
                $session_id = rand(100000, 9999999);
                $request->session()->put('session_id', $session_id);
            } else {
                $session_id = $request->session()->get('session_id');
            }
            $data = [
                'session_id' => $session_id,
                'product_id' => $product_id
            ];
            if ($sepet_model->where('product_id', $product_id)->where('session_id', $session_id)->count() > 0) {
                return $sepet_model->session_plus($product_id, $session_id);
            }
        }

        return $sepet_model->create($data);
    }

    function delete(Request $request)
    {
        $sepet_model = new Sepet();
        $sepet_id    = $request->input('sepet_id');
        $sepet_model->where('id', $sepet_id)->delete();
        $session_id = $request->session()->get('session_id');
        return $sepet_model->sepets($session_id);
    }

    function delete_all()
    {
        $sepet_model = new Sepet();
        $sepet_model->delete_all();
    }

    function products(Request $request)
    {
        $sepet_model    = new Sepet();
        $products_sorgu = $sepet_model->with('product');
        $session_id     = $request->session()->get('session_id');
        if (Auth::check()) {
            $products = $products_sorgu->where('user_id', Auth::user()->id)->get();
        } else {
            $products = $products_sorgu->where('session_id', $session_id)->get();
        }

        return self::sepet_row($products);
    }

    function price_set($product)
    {
        $price_not_dis = $product->product->price * $product->adet * $product->lisans_ay;
        if ($price_not_dis == 0) {
            $price_not_dis = 0.0001;
        }
        $price = $product->product->price * $product->adet * $product->lisans_ay;
        if ($product->adet > 1) {
            $price     = $product->product->dis_price * $product->adet * $product->lisans_ay;
            $dis_price = $price - ($price * $product->product->dis_person * $product->adet);
        } else {
            $dis_price = $price;
        }
        if ($product->lisans_ay > 1) {
            if ($product->adet == 1) {
                $dis_price = $product->product->dis_price * $product->adet * $product->lisans_ay;
            }
            $dis_price = $dis_price - ($dis_price * $product->product->dis_moon * $product->lisans_ay);
        } else {
            $dis_price = $dis_price;
        }

        if ((($dis_price / $price_not_dis) - ((100 - $product->product->max_dis) / 100)) < 0) {
            $price = ((100 - $product->product->max_dis) / 100) * $price_not_dis;
        } else {
            $price = $dis_price;
        }
        return $price;
    }

    function discount($price, $dis_price)
    {
        $discount = 100 - round($dis_price / $price, 2) * 100;
        if ($discount > 0) {
            $discount = ' <span style="color: #0b7c0f; font-size: 9pt;">%' . $discount . '</span>';
        } else {
            $discount = '';
        }
        return $discount;
    }

    function sepet_row($products)
    {
        $veri        = '';
        $total_price = [];
        foreach ($products as $product) {
            $price     = $product->product->price * $product->adet * $product->lisans_ay;
            $dis_price = self::price_set($product);
            $veri      .= '<tr class="sepet_row" id="sapet_row_' . $product->id . '">
                            <td>' . $product->product->product_name . '</td>
                            <td>
                            <input size="3" style="width: 30px; margin: 0; padding:2px;"  id="sepet_adet_' . $product->id . '" value="' . $product->adet . '"/> 
                             <span style="font-size:12pt; cursor:pointer;" onclick="sepet_adet_guncelle(' . $product->id . ')" class="fa fa-refresh"></span>
                            </td>
                             <td>';
            if ($price > $dis_price) {
                $veri .= '<strike style="color: #be3946; font-size: 9pt;">' . round($price, 2) . '</strike>   ' . self::discount($price, $dis_price);
                $veri .= ' <span style="color: #2d7c32; font-size: 12pt;">' . $total_price[] = round($dis_price, 2) . '₺</span> ';
            } else {
                $veri .= ' <span style="color: #2d7c32; font-size: 12pt;">' . $total_price[] = round($price, 2) . '₺</span>';
            }
            $veri .= '</td>';
            $veri .= '<td style="text-align: right"><span style="font-size:14pt; padding-top: 6px; cursor:pointer;" onclick="sepet_delete(' . $product->id . ')" class="fa fa-trash"></span></td>
                        </tr>';

        }
        $veri .= '<tr>
                  <td></td>
                  <td></td>';
        $veri .= '<td colspan="2">' . array_sum($total_price) . '₺</td>';
        $veri .= '</tr>';
        return $veri;
    }

    function sepet_row_detail($products)
    {
        $veri        = '';
        $total_price = [];
        foreach ($products as $product) {
            $price     = $product->product->price * $product->adet * $product->lisans_ay;
            $dis_price = self::price_set($product);

            $type = $product->product->type == 1 ? 'Lisans' : 'Ürün';
            $veri .= '<tr class="sepet_row" id="sapet_row_' . $product->id . '">
                            <td>' . $product->product->product_name . '</td>
                            <td>' . $type . '</td>
                            <td>
                            <div style="width: 100px; float: left;">
                            <input size="3" style="width: 30px; margin: 0; padding:2px;"  id="sepet_adet_' . $product->id . '" value="' . $product->adet . '"/> 
                             <span style="font-size:12pt; cursor:pointer;" onclick="sepet_adet_guncelle(' . $product->id . ')" class="fa fa-refresh"></span>
                             </div>';

            if ($product->product->type == 1) {
                $veri .= '<div style="width: 30%; float: right">
Kaç Aylık
                            <input size="3" style="width: 30px; margin: 0; padding:2px;"  id="sepet_lisans_ay_' . $product->id . '" value="' . $product->lisans_ay . '"/> 
                             <span style="font-size:12pt; cursor:pointer;" onclick="sepet_lisans_ay_guncelle(' . $product->id . ')" class="fa fa-refresh"></span>
                            </div>';
            }
            $veri .= '</td>';

            $veri .= '<td>';
            if ($price > $dis_price) {
                $veri .= '<strike style="color: #be3946; font-size: 9pt;">' . round($price, 2) . '</strike>   ' . self::discount($price, $dis_price) . '<br>';
                $veri .= ' <span style="color: #2d7c32; font-size: 12pt;">' . $total_price[] = round($dis_price, 2) . '₺</span> ';
            } else {
                $veri .= ' <span style="color: #2d7c32; font-size: 12pt;">' . $total_price[] = round($price, 2) . '₺</span>';
            }
            $veri .= '</td>';
            $veri .= '<td style="text-align: right"><span style="font-size:14pt; padding-top: 6px; cursor:pointer;" onclick="sepet_delete(' . $product->id . ')" class="fa fa-trash"></span></td>
                        </tr>';
        }
        $veri .= '<tr>
<td></td>
<td></td>';
        $veri .= '<td colspan="3">' . array_sum($total_price) . '₺</td>';
        $veri .= '</tr>';
        return $veri;
    }

    function sepet_adet_guncelle(Request $request)
    {
        $sepet_model = new Sepet();
        $sepet_id    = $request->input('sepet_id');
        $adet        = $request->input('adet');
        $sepet_model->where('id', $sepet_id)->update(['adet' => $adet]);
        $session_id = $request->session()->get('session_id');
        return $sepet_model->sepets($session_id);
    }

    function sepet_lisans_ay_guncelle(Request $request)
    {
        $sepet_model = new Sepet();
        $sepet_id    = $request->input('sepet_id');
        $lisans_ay   = $request->input('lisans_ay');
        $sepet_model->where('id', $sepet_id)->update(['lisans_ay' => $lisans_ay]);

    }

    function card(Request $request)
    {
        $sepet_model = new Sepet();
        $product_id  = $request->input('product_id');
        if (!empty($product_id)) {
            self::create($request, $product_id);
        }
        $products_sorgu = $sepet_model->with('product');
        $session_id     = $request->session()->get('session_id');


        if (Auth::check()) {
            $products = $products_sorgu->where('user_id', Auth::user()->id)->get();
        } else {
            $products = $products_sorgu->where('session_id', $session_id)->get();
        }


        $sepet_row = self::sepet_row_detail($products);
        return View('acr_ftr::card_sepet', compact('sepet_row', 'controller', 'sepet_count'));
    }

}
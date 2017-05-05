<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\Acr_Ftr_user;
use Acr\Ftr\Model\AcrFtrAdress;
use Acr\Ftr\Model\County;
use Acr\Ftr\Model\Product_sepet;
use Acr\Ftr\Model\Sepet;
use Acr\Ftr\Model\City;
use Auth;
use Illuminate\Http\Request;
use Acr\Ftr\Model\Acrproduct;
use Validator;
use Redirect;
use Acr\Ftr\Model\Bank;

class AcrSepetController extends Controller
{
    function index()
    {
        $user_model = new Acr_Ftr_user();
        $sepets     = $user_model->find(Auth::user()->id)->sepets()->get();
        return View('acr_ftr::anasayfa');
    }

    function create(Request $request, $product_id = null)
    {
        $sepet_model = new Sepet();
        $ps_model    = new Product_sepet();
        if (empty($product_id)) {
            $product_id = $request->input('product_id');
        }
        if (Auth::check()) {
            $sepet_id = $sepet_model->product_sepet_id();
            if ($ps_model->where('sepet_id', $sepet_id)->where('product_id', $product_id)->count() > 0) {
                return $ps_model->use_plus($product_id, $sepet_id);
            }
        } else {
            if (empty($request->session()->get('session_id'))) {
                $session_id = rand(100000, 9999999);
                $request->session()->put('session_id', $session_id);
            } else {
                $session_id = $request->session()->get('session_id');
            }
            $sepet_id = $sepet_model->product_sepet_id($session_id);

            if ($sepet_model->where('product_id', $product_id)->where('sepet_id', $sepet_id)->count() > 0) {
                return $sepet_model->use_plus($product_id, $sepet_id);
            }
        }
        $session_id = empty($session_id) ? null : $session_id;
        return $sepet_model->create($session_id, $product_id);
    }

    function delete(Request $request)
    {
        $sepet_model = new Sepet();
        $ps_model    = new Product_sepet();
        $sepet_id    = $request->input('sepet_id');
        $ps_model->where('id', $sepet_id)->delete();
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
        $sepet_model = new Sepet();
        $ps_model    = new Product_sepet();

        $session_id = $request->session()->get('session_id');
        $sepet_id   = $sepet_model->product_sepet_id($session_id);
        $products   = $ps_model->where('sepet_id', $sepet_id)->with('product')->get();
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
            $sepet_id  = $product->sepet_id;
            $price     = $product->product->price * $product->adet * $product->lisans_ay;
            $dis_price = self::price_set($product);
            $type      = $product->product->type == 1 ? 'Lisans' : 'Ürün';
            $veri      .= '<tr class="sepet_row" id="sapet_row_' . $product->id . '">
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
                $total_price[] = round($dis_price, 2);
                $veri          .= '<strike style="color: #be3946; font-size: 9pt;">' . round($price, 2) . '</strike>   ' . self::discount($price, $dis_price) . '<br>';
                $veri          .= ' <span style="color: #2d7c32; font-size: 12pt;">' . array_sum($total_price) . '₺</span> ';
            } else {
                $total_price[] = round($price, 2);
                $veri          .= ' <span style="color: #2d7c32; font-size: 12pt;">' . array_sum($total_price) . '₺</span>';
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
        $ps_model    = new Product_sepet();
        $sepet_id    = $request->input('sepet_id');
        $adet        = $request->input('adet');
        $ps_model->where('id', $sepet_id)->update(['adet' => $adet]);
        $session_id = $request->session()->get('session_id');
        return $sepet_model->sepets($session_id);
    }

    function sepet_lisans_ay_guncelle(Request $request)
    {
        $sepet_model = new Product_sepet();
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
        $session_id = $request->session()->get('session_id');
        $products   = $sepet_model->product_sepet($session_id);
        $sepet_row  = self::sepet_row_detail($products);
        $sepet_nav  = self::sepet_nav(1);
        return View('acr_ftr::card_sepet', compact('sepet_row', 'sepet_nav'));
    }

    function adress(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $sepet_nav    = self::sepet_nav(2);
        $adres_form   = self::adress_form($request);
        $adresses     = $adress_model->where('user_id', Auth::user()->id)->where('sil', 0)->with('city', 'county')->get();

        return View('acr_ftr::card_adress', compact('sepet_nav', 'adres_form', 'adresses'));
    }

    function payment(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $user_model   = new Acr_Ftr_user();
        $bank_model   = new Bank();
        $adress_id    = $request->input('adress');
        $adress_model->active_adress($adress_id);
        $banks            = $bank_model->where('active', 1)->where('sil', 0)->get();
        $iyzicoController = new iyzicoController();
        $sepet            = $user_model->find(Auth::user()->id)->sepet()->first();
        $odemeForm        = $iyzicoController->odemeForm(1, $sepet->price, $sepet->id);
        $sepet_nav        = self::sepet_nav(3);
        return View('acr_ftr::card_payment', compact('odemeForm', 'sepet_nav', 'banks'));
    }

    function sepet_total_price()
    {
        $sepet_model = new Sepet();
        $ps_model    = new Product_sepet();
        $sepet_id    = $sepet_model->product_sepet_id();
        $products    = $ps_model->where('sepet_id', $sepet_id)->with('product')->get();
        $price       = [];
        foreach ($products as $product) {
            $price[] = self::price_set($product);
        }
        $prices = array_sum($price);
        $prices = round($prices, 2);
        return $prices;
    }

    function paymet_havale_eft(Request $request)
    {
        $sepet_model = new Sepet();
        $bank_id     = $request->input('bank_id');
        $bank_model  = new Bank();
        $sepet_id    = $sepet_model->product_sepet_id();
        $price       = self::sepet_total_price();
        $dataSepet   = [
            'bank_id'      => $bank_id,
            'payment_type' => '1',
            'siparis'      => 1,
            'price'        => $price
        ];

        $sepet_model->where('id', $sepet_id)->update($dataSepet);
        $siparis   = $sepet_model->where('id', $sepet_id)->where('siparis', 1)->first();
        $bank      = $bank_model->where('id', $bank_id)->first();
        $sepet_nav = self::sepet_nav(4);

        return View('acr_ftr::card_result_bank', compact('sepet_nav', 'siparis', 'bank'));

    }

    function sepet_nav($step)
    {
        $navs = [
            1 => ['sepet', 'SEPET', 11],
            ['adress', 'TESLİMAT BİLGİLERİ', 10],
            ['payment', 'ÖDEME BİLGİLERİ', 9],
            ['result', ' ALIŞVERİŞ SONUCU', 8]
        ];

        $row = '<div id="breadcrumb">';
        $row .= '<ul class="crumbs">';
        foreach ($navs as $key => $nav) {
            $color = $key == $step ? 'color: red; background-position: 100% -96px;' : '';
            $row   .= '<li class="first ">';
            $row   .= '<a ';
            if ($step >= $key) {
                $row .= ' href="/acr/ftr/card/' . $nav[0] . '/"';
            }
            $row .= 'style="z-index:' . $nav[2] . '; ' . $color . '"><span></span>' . $key . ' ' . $nav[1] . '</a>';
            $row .= '</li>';
        }
        $row .= '</ul>';
        $row .= '</div>';
        return $row;
    }

    function adress_form(Request $request, $adress = null)
    {
        $city_model = new City();

        $cities = $city_model->get();

        $row = '<form method="post" action="/acr/ftr/card/adress/create">';
        $row .= csrf_field();
        $row .= '<div class="form-group">';
        $row .= '<label>Adres İsmi</label>';
        $row .= '<input required name="name" id="name" class="form-control" placeholder="Adres İsmi" value="' . @$adress->name . '">';
        $row .= '</div>';
        // citys
        $row .= '<div class="form-group">';
        $row .= '<label>Şehir</label>';
        $row .= '<select required name="city" id="city" class="form-control">';
        $row .= '<option value="">Şehir Seçiniz</option>';
        foreach ($cities as $city) {
            $select = $city->id == @$adress->city_id ? 'selected="selected"' : '';
            $row    .= '<option ' . $select . ' value="' . $city->id . '">';
            $row    .= $city->name;
            $row    .= '</option>';
        }
        $row .= '</select>';
        $row .= '</div>';
        $row .= '<div id="county">';

        if (!empty($adress->city_id)) {
            $row .= self::county_row($request, $adress->city_id, @$adress);
        }
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Açık Adres</label>';
        $row .= '<textarea required name="adress"  class="form-control" placeholder="Açık Adres">' . @$adress->adress . '</textarea>';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Posta Kodu</label>';
        $row .= '<input required name="post_code"  class="form-control" placeholder="Posta Kodu" value="' . @$adress->post_code . '">';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Telefon</label>';
        $row .= '<input required name="tel"  class="form-control" placeholder="Telefon" value="' . @$adress->tel . '">';
        $row .= '</div>';

        // kurumsal
        if (@$adress->type == 1 || empty($adress->type)) {
            $type_c_1 = 'checked';
            $type_c_2 = '';
        } else {
            $type_c_1 = '';
            $type_c_2 = 'checked';
        }
        $row .= '<div class="form-group">';
        $row .= '<label class="type_b">';
        $row .= '<input type="radio" name="type" value="1" class="flat-red" ' . $type_c_1 . '  style="position: absolute; opacity: 0;">';
        $row .= '<div style="margin-left: 10px; font-size: 14pt; float: right;">Bireysel </div>';
        $row .= '</label>';
        $row .= '<label  style="margin-left: 30px;"  class="type_k">';
        $row .= '<input  type="radio" name="type" value="2" class="flat-red" ' . $type_c_2 . ' style="position: absolute; opacity: 0;">';

        $row .= '<div  style="margin-left: 10px; font-size: 14pt; float: right;">Kurumsal </div>';
        $row .= '</label>';
        $row .= '</div>';

        // kurumsal fatura Bilgileri
        $display = @$adress->type == 1 || empty(@$adress->type) ? 'none' : 'normal';

        $row .= '<div id="kurumsal" style="display: ' . $display . '">';
        $row .= '<div class="form-group">';
        $row .= '<label>Kurum İsmi</label>';
        $row .= '<input name="campany"  class="form-control" placeholder="Kurum İsmi" value="' . @$adress->campany . '">';

        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Kurum Vergi No</label>';
        $row .= '<input name="tax_number"  class="form-control" placeholder="Kurum Vergi No" value="' . @$adress->tax_number . '">';

        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Kurum Vergi Dairesi</label>';
        $row .= '<input name="tax_office"  class="form-control" placeholder="Kurum Vergi Dairesi" value="' . @$adress->tax_office . '">';
        $row .= '</div>';
        // e fatura
        if (@$adress->e_fatura == 2) {
            $e_fatura_check = 'checked';
        } else {
            $e_fatura_check = '';
        }
        $row .= '<label for="e_fatura" class="">';
        $row .= '<input name="e_fatura" id="e_fatura" type="checkbox" ' . $e_fatura_check . ' class="minimal-red" value="2"  style="position: absolute; opacity: 0;">';

        $row .= '<div style="margin-left: 10px; font-size: 14pt; float: right;">E-Fatura Mükellefiyim</div>';
        $row .= '</label>';
        $row .= '</div>';

        $row .= '<input type="hidden" name="adress_id"  value="' . @$adress->id . '">';
        $row .= '<button type="submit" class="btn btn-primary">ADRES KAYDET <span class="fa fa-angle-double-right"></span> </button>';
        $row .= '</form>';
        $row .= '<div style="clear:both;"></div>';
        return $row;
    }

    function county_row(Request $request, $city_id = null, $adress = null)
    {
        $county_model = new County();
        if (empty($city_id)) {
            $city_id = $request->input('city_id');
        }
        $counties = $county_model->where('city_id', $city_id)->get();
// citys
        $row = '<div class="form-group">';
        $row .= '<label>İlçe</label>';
        $row .= '<select required name="county" class="form-control">';
        foreach ($counties as $county) {
            $select = $county->id == @$adress->county_id ? 'selected="selected"' : '';
            $row    .= '<option ' . $select . ' value="' . $county->id . '">';
            $row    .= $county->name;
            $row    .= '</option>';
        }
        $row .= '</select>';
        $row .= '</div>';

        return $row;
    }

    function adress_create(Request $request)
    {
        $rules   = array(
            'name'      => 'required', // make sure the email is an actual email
            'city'      => 'required', // password can only be alphanumeric and has to be greater than 3 characters
            'county'    => 'required',
            'post_code' => 'required',
            'tel'       => 'required'
        );
        $massage = [
            'name.required'      => 'Adres İsmi Giriniz',
            'city.required'      => 'Şehir Seçiniz.',
            'county.required'    => 'İlçe Seçiniz.',
            'post_code.required' => 'Posta Kodu Giriniz.',
            'tel.required'       => 'Telefon Numarası Giriniz.'
        ];
// run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules, $massage);
// if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect()->back()
                ->withErrors($validator)// send back all errors to the login form
                ->withInput($request->all()); //
        } else {
            $e_fatura     = empty($request->input('e_fatura')) ? 1 : $request->input('e_fatura');
            $adress_model = new AcrFtrAdress();
            $data         = [
                'user_id'    => Auth::user()->id,
                'name'       => $request->input('name'),
                'adress'     => $request->input('adress'),
                'city_id'    => $request->input('city'),
                'county_id'  => $request->input('county'),
                'post_code'  => $request->input('post_code'),
                'tel'        => $request->input('tel'),
                'type'       => $request->input('type'),
                'campany'    => $request->input('campany'),
                'tax_number' => $request->input('tax_number'),
                'tax_office' => $request->input('tax_office'),
                'e_fatura'   => $e_fatura,

            ];
            $adress_id    = $request->input('adress_id') ? $request->input('adress_id') : 0;
            $adress_model->create($adress_id, $data);
            return Redirect()->back();
        }

    }

    function card_adress_edit(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $sepet_nav    = self::sepet_nav(2);
        $adres_id     = $request->input('adres_id');
        $adress       = $adress_model->where('id', $adres_id)->with('city', 'county')->first();
        $adres_form   = self::adress_form($request, $adress);

        return View('acr_ftr::card_adress_edit', compact('sepet_nav', 'adres_form', 'adress'));
    }

    function adress_edit(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $adres_id     = $request->input('adres_id');
        $adress       = $adress_model->where('id', $adres_id)->first();
        return self::adress_form($request, $adress);
    }

    function adress_delete(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $adres_id     = $request->input('adres_id');
        $adress_model->where('id', $adres_id)->update(['sil' => 1]);

    }

}
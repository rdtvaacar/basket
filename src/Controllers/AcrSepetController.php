<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\AcrUser;
use Acr\Ftr\Model\Acr_user_table_conf;
use Acr\Ftr\Model\AcrFtrAdress;
use Acr\Ftr\Model\Company_conf;
use Acr\Ftr\Model\County;
use Acr\Ftr\Model\Product;
use Acr\Ftr\Model\Product_sepet;
use Acr\Ftr\Model\Sepet;
use Acr\Ftr\Model\City;
use App\Http\Controllers\MarketController;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Acr\Ftr\Model\Acrproduct;
use Validator;
use Redirect;
use Acr\Ftr\Model\Bank;

class AcrSepetController extends Controller
{
    protected $config_name;
    protected $config_user_name;
    protected $config_email;
    protected $config_lisans_durum;
    protected $config_lisans_baslangic;
    protected $config_lisans_bitis;

    function __construct()
    {
        $conf_table_model  = new Acr_user_table_conf();
        $conf_table        = $conf_table_model->first();
        $this->config_name = $conf_table->name;
    }

    function index()
    {
        $user_model = new AcrUser();
        $sepets     = $user_model->find(Auth::user()->id)->sepets()->get();
        return View('acr_ftr::anasayfa');
    }

    function orders(Request $request)
    {
        $sepet_model = new Sepet();
        $orders      = $sepet_model->where('user_id', Auth::user()->id)->where('siparis', 1)->orderBy('id', 'desc')->get();
        return View('acr_ftr::acr_orders', compact('orders'));
    }

    function admin_orders(Request $request)
    {
        $sepet_model = new Sepet();
        $orders      = $sepet_model->where('siparis', 1)->get();
        return View('acr_ftr::acr_admin_orders', compact('orders'));
    }

    function create(Request $request, $product_id = null)
    {
        $sepet_model = new Sepet();
        $ps_model    = new Product_sepet();
        if (empty($product_id)) {
            $product_id = $request->input('product_id');
        }
        if (Auth::check()) {
            $sepet_id = $sepet_model->product_sepet_id();;
            if ($ps_model->where('sepet_id', $sepet_id)->where('product_id', $product_id)->count() > 0) {
                return $ps_model->use_plus($product_id, $sepet_id);
            }
        } else {
            if (empty($request->session()->get('session_id'))) {
                $session_id = rand(1000000, 99999999);
                $request->session()->put('session_id', $session_id);
            } else {
                $session_id = $request->session()->get('session_id');
            }

            $sepet_id = $sepet_model->product_sepet_id($session_id);
            if ($ps_model->where('product_id', $product_id)->where('sepet_id', $sepet_id)->count() > 0) {
                return $ps_model->use_plus($product_id, $sepet_id);
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
        $products   = $ps_model->where('sepet_id', $sepet_id)->with([
            'product' => function ($query) {
                $query->where('sil', 0);
            }
        ])->get();
        return self::sepet_row($products);
    }

    function price_set($product)
    {
        $price_not_dis = $product->product->price * $product->adet * $product->lisans_ay;
        if ($price_not_dis == 0) {
            $price_not_dis = 1;
        }
        $priceData = empty($product->product->dis_price) ? $product->product->price : $product->product->dis_price;

        $price = $priceData * $product->adet * $product->lisans_ay;
        if ($product->adet > 1) {
            $price     = $product->product->dis_price * $product->adet * $product->lisans_ay;
            $dis_price = $price - ($price * $product->product->dis_person * $product->adet);
        } else {
            $dis_price = $price;
        }
        if ($product->lisans_ay > 1) {
            if ($product->adet == 1) {
                $dis_price = $priceData * $product->adet * $product->lisans_ay;
            }
            $dis_price = $dis_price - ($dis_price * $product->product->dis_moon * $product->lisans_ay);
        } else {
            $dis_price = $dis_price;
        }
        if ($product->lisans_ay == 1 && $product->adet == 1) {
            $dis_price = $product->product->price;
        }
        if ((($dis_price / $price_not_dis) - ((100 - $product->product->max_dis) / 100)) < 0) {
            if (((100 - $product->product->max_dis) / 100) > 0) {
                $price = ((100 - $product->product->max_dis) / 100) * $price_not_dis;
            } else {
                $price = $dis_price;
            }
        } else {
            $price = $dis_price;
        }
        return $price;
    }

    function discount($price = null, $dis_price = null, Request $request = null)
    {
        $price     = empty($price) ? $request->price : $price;
        $dis_price = empty($dis_price) ? $request->dis_price : $dis_price;
        $discount  = 100 - round($dis_price / $price, 2) * 100;
        if ($discount > 0) {
            $discount = ' <span style="color: #0b7c0f; font-size: 9pt;">%' . $discount . '</span>';
        } else {
            $discount = '';
        }
        return $discount;
    }

    function dis_rate($price, $dis_price)
    {
        $discount = 100 - ($dis_price / $price * 100);
        if ($discount > 0) {
            $discount = $discount;
        } else {
            $discount = 0;
        }
        return ($discount / 100);
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
                            <input size="3" type="number" style="width: 70px; margin: 0; padding:2px;" onchange="sepet_adet_guncelle(' . $product->id . ')" onkeyup="sepet_adet_guncelle(' . $product->id . ')"
                             id="sepet_adet_' . $product->id . '" value="' . $product->adet . '"/> 
                            </td>
                             <td>';
            if ($price > $dis_price) {
                $veri          .= '<span  id="product_dis_' . $product->id . '"><strike style="color: #be3946; font-size: 9pt;">' . round($price, 2) . '</strike>   ' . self::discount($price, $dis_price) . '</span>';
                $veri          .= ' <span id="product_price_' . $product->id . '" style="color: #2d7c32; font-size: 12pt;">' . round($dis_price, 2) . '₺</span> ';
                $total_price[] = round($dis_price, 2);
            } else {
                $veri          .= ' <span id="product_price_' . $product->id . '" style="color: #2d7c32; font-size: 12pt;">' . round($price, 2) . '₺</span>';
                $total_price[] = round($price, 2);
            }
            $veri .= '</td>';
            $veri .= '<td style="text-align: right"><span style="font-size:14pt; padding-top: 6px; cursor:pointer;" onclick="sepet_delete(' . $product->id . ')" class="fa fa-trash"></span></td>
                        </tr>';
        }
        $veri .= '<tr>
                  <td></td>
                  <td></td>';
        $veri .= '<td id="acr_sepet_total_price" colspan="2">' . array_sum($total_price) . '₺</td>';
        $veri .= '</tr>';

        return $veri;
    }

    function sepet_row_detail($products)
    {
        $ps_model    = new Product_sepet();
        $veri        = '';
        $total_price = [];
        foreach ($products as $product) {
            $sepet_id  = $product->sepet_id;
            $price     = $product->product->price * $product->adet * $product->lisans_ay;
            $dis_price = self::price_set($product);
            $dis_rate  = self::dis_rate($price, $dis_price);
            if ($product->dis_rate != $dis_rate) {
                $ps_model->where('id', $sepet_id)->update(['dis_rate' => $dis_rate]);
            }
            $type = $product->product->type == 1 ? 'Lisans' : 'Ürün';
            $veri .= '<tr class="sepet_row" id="sapet_row_' . $product->id . '">
                            <td>' . $product->product->product_name . '</td>
                            <td>' . $type . '</td>
                            <td>
                            <div class="col-md-6 col-xs-12" >
                            <input type="number" class="form-control" onchange="sepet_adet_guncelle(' . $product->id . ')" onkeyup="sepet_adet_guncelle(' . $product->id . ')" style="width: 70px;"  id="sepet_adet_' . $product->id . '" value="' . $product->adet . '"/> 
                             </div>';
            if ($product->product->type == 1) {
                $veri .= '<div class="col-md-6 col-xs-12">
<div class="col-md-6 col-xs-12">Kaç Aylık</div>
                            <div class="col-md-6 col-xs-12">
                            <input type="number" size="3" class="form-control" onchange="sepet_adet_guncelle(' . $product->id . ')"  onkeyup="sepet_lisans_ay_guncelle(' . $product->id . ')"  style="width: 70px;"   id="sepet_lisans_ay_' . $product->id . '" value="' . $product->lisans_ay . '"/> 
                            </div>
                            </div>';
            }
            $veri .= '</td>';
            $veri .= '<td>';
            $veri .= $product->product->price . '₺';
            $veri .= '</td>';
            $veri .= '<td>';
            if ($price > $dis_price) {
                $total_price[] = round($dis_price, 2);
                $veri          .= ' <span  id="product_dis_' . $product->id . '"><strike style="color: #be3946; font-size: 9pt;">' . round($price, 2) . '</strike> ' . self::discount($price, $dis_price) . '<br></span> ';
                $veri          .= ' <span id="product_price_' . $product->id . '" style="color: #2d7c32; font-size: 12pt;">' . round($dis_price, 2) . '₺</span> ';
            } else {
                $total_price[] = round($price, 2);
                $veri          .= ' <span id="product_price_' . $product->id . '"  style="color: #2d7c32; font-size: 12pt;">' . round($price, 2) . '₺</span>';
            }
            $veri .= '</td>';
            $veri .= '<td style="text-align: right"><span style="font-size:14pt; padding-top: 6px; cursor:pointer;" onclick="sepet_delete(' . $product->id . ')" class="fa fa-trash"></span></td>
                        </tr>';
        }
        $veri .= '<tr>
                    <td></td>
                    <td></td>
                     <td></td>
                      <td></td>';
        $veri .= '<td id="acr_sepet_total_price" colspan="2">' . array_sum($total_price) . '₺</td>';
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
        $order_id   = $request->input('order_id');
        $order_id   = empty($order_id) ? $sepet_model->product_sepet_id($session_id) : $order_id;
        $sepet_nav  = self::sepet_nav($order_id, 1);
        $order_link = empty($order_id) ? '' : '?order_id=' . $order_id;
        return View('acr_ftr::card_sepet', compact('sepet_row', 'sepet_nav', 'order_link'));
    }

    function adress(Request $request)
    {
        $sepet_model  = new Sepet();
        $adress_model = new AcrFtrAdress();
        $session_id   = $request->session()->get('session_id');
        if (Auth::check() && !empty($session_id)) {
            $sepet_model->sepet_birle($session_id);
            $request->session()->forget('session_id');
        }
        $order_id    = $request->input('order_id');
        $order_id    = empty($order_id) ? $sepet_model->product_sepet_id() : $order_id;
        $sepet_nav   = self::sepet_nav($order_id, 2);
        $adres_form  = self::adress_form($request);
        $adresses    = $adress_model->where('user_id', Auth::user()->id)->where('sil', 0)->with('city', 'county')->get();
        $order_link  = empty($order_id) ? '' : '?order_id=' . $order_id;
        $order_input = empty($order_id) ? '' : '<input name="order_id" type="hidden" value="' . $order_id . '"/>';
        self::ps_dis_rate_set($order_id); // product_sepet dis_rate oranlarını hesaplar
        return View('acr_ftr::card_adress', compact('sepet_nav', 'adres_form', 'adresses', 'order_input', 'order_link'));
    }

    function ps_dis_rate_set($order_id)
    {
        $ps_model = new Product_sepet();
        $pss      = $ps_model->where('sepet_id', $order_id)->get();
        foreach ($pss as $ps) {
            $price         = self::price_set($ps);
            $dis_not_price = self::product_not_dis_price($ps);
            $dis_rate      = self::dis_rate($dis_not_price, $price);
            $ps_model->where('id', $ps->id)->update(['dis_rate' => $dis_rate]);
        }
    }

    function payment(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $sepet_model  = new Sepet();
        $bank_model   = new Bank();
        $order_id     = $request->input('order_id');
        $order_id     = empty($order_id) ? $sepet_model->product_sepet_id() : $order_id;
        $adress_id    = $request->input('adress');
        if (!empty($adress_id)) {
            $adress_model->active_adress($adress_id);
            $sepet_model->where('id', $order_id)->update(['adress_id' => $adress_id]);
        }
        $order_link  = empty($order_id) ? '' : '?order_id=' . $order_id;
        $order_input = empty($order_id) ? '' : '<input name="order_id" type="hidden" value="' . $order_id . '"/>';
        $banks       = $bank_model->where('active', 1)->where('sil', 0)->get();
        $sepet_nav   = self::sepet_nav($order_id, 3);
        return View('acr_ftr::card_payment', compact('sepet_nav', 'banks', 'order_link', 'order_input'));


    }

    function sepet_total_price($sepet_id = null, Request $request = null)
    {
        $sepet_id      = empty($sepet_id) ? $request->sepet_id : $sepet_id;
        $ps_model      = new Product_sepet();
        $product       = $ps_model->where('id', $sepet_id)->with('product')->first();
        $price         = self::price_set($product);
        $not_dis_price = $product->product->price * $product->adet * $product->lisans_ay;
        $dis_rate      = self::dis_rate($not_dis_price, $price);
        if ($product->dis_rate != $dis_rate) {
            $ps_model->where('id', $sepet_id)->update(['dis_rate' => $dis_rate]);
        }
        $prices = round($price, 2);
        return $prices;
    }

    function product_not_dis_price($product)
    {
        $price = $product->product->price * $product->adet * $product->lisans_ay;
        return $price;
    }

    function not_dis_price($sepet_id = null, Request $request = null)
    {
        $ps_model    = new Product_sepet();
        $sepet_id    = empty($sepet_id) ? $request->sepet_id : $sepet_id;
        $productData = $ps_model->where('id', $sepet_id)->first();
        $products    = $ps_model->where('sepet_id', $productData->sepet_id)->with('product')->get();
        $price       = [];
        foreach ($products as $product) {
            $price[] = $product->product->price * $product->adet * $product->lisans_ay;
        }
        $prices = array_sum($price);
        $prices = round($prices, 2);
        return $prices;
    }

    function product_sepet_total_price($sepet_id = null, Request $request = null)
    {

        $ps_model    = new Product_sepet();
        $sepet_id    = empty($sepet_id) ? $request->sepet_id : $sepet_id;
        $productData = $ps_model->where('id', $sepet_id)->first();
        $products    = $ps_model->where('sepet_id', $productData->sepet_id)->with('product')->get();
        $price       = [];
        foreach ($products as $product) {
            $price[] = self::price_set($product);;
        }
        $prices = array_sum($price);
        $prices = round($prices, 2);
        return $prices;
    }

    function order_set($data, $sepet_id)
    {
        $sepet_model = new Sepet();
        $sepet_model->where('id', $sepet_id)->update($data);
    }

    function paymet_havale_eft(Request $request)
    {
        $sepet_model   = new Sepet();
        $bank_id       = $request->input('bank_id');
        $bank_model    = new Bank();
        $order_id      = $request->input('order_id');
        $sepet_id      = empty($order_id) ? $sepet_model->product_sepet_id() : $order_id;
        $ps_model      = new Product_sepet();
        $ps            = $ps_model->where('sepet_id', $sepet_id)->first();
        $price         = round(self::product_sepet_total_price($ps->id), 2);
        $not_dis_price = round(self::not_dis_price($ps->id), 2);
        $dis_rate      = self::dis_rate($not_dis_price, $price);
        $data_sepet    = [
            'siparis'      => 1,
            'price'        => $price,
            'bank_id'      => $bank_id,
            'payment_type' => 1,
            'dis_rate'     => $dis_rate
        ];
        self::order_set($data_sepet, $sepet_id);
        $siparis = $sepet_model->where('id', $sepet_id)->where('siparis', 1)->first();
        $ps      = $ps_model->where('sepet_id', $sepet_id)->with('product')->get();
        if (empty($sepet_id)) {
            return redirect()->to('/acr/ftr/orders');
        }
        $bank            = $bank_model->where('id', $bank_id)->first();
        $sepet_nav       = self::sepet_nav($sepet_id, 4);
        $adress_model    = new AcrFtrAdress();
        $user_adress     = $adress_model->where('id', $siparis->adress_id)->with('city', 'county')->first();
        $company_model   = new Company_conf();
        $company         = $company_model->first();
        $sepetController = new AcrSepetController();
        return View('acr_ftr::card_result_bank', compact('sepet_nav', 'siparis', 'bank', 'ps', 'user_adress', 'company', 'sepetController'));

    }

    function payment_bank_card(Request $request)
    {
        $sepet_model   = new Sepet();
        $order_id      = $request->input('order_id');
        $sepet_id      = empty($order_id) ? $sepet_model->product_sepet_id() : $order_id;
        $ps_model      = new Product_sepet();
        $ps            = $ps_model->where('sepet_id', $sepet_id)->first();
        $price         = round(self::product_sepet_total_price($ps->id), 2);
        $not_dis_price = round(self::not_dis_price($ps->id), 2);
        $dis_rate      = self::dis_rate($not_dis_price, $price);
        $data_sepet    = [
            'siparis'      => 1,
            'price'        => $price,
            'payment_type' => 2,
            'dis_rate'     => $dis_rate
        ];
        self::order_set($data_sepet, $sepet_id);
        $siparis = $sepet_model->where('id', $sepet_id)->where('siparis', 1)->first();
        $ps      = $ps_model->where('sepet_id', $sepet_id)->with('product')->get();
        if (empty($sepet_id)) {
            return redirect()->to('/acr/ftr/orders');
        }
        $sepet_nav        = self::sepet_nav($sepet_id, 4);
        $adress_model     = new AcrFtrAdress();
        $user_adress      = $adress_model->where('id', $siparis->adress_id)->with('city', 'county')->first();
        $company_model    = new Company_conf();
        $company          = $company_model->first();
        $sepetController  = new AcrSepetController();
        $iyzicoController = new iyzicoController();
        $odemeForm        = $iyzicoController->odemeForm(1, $price, $sepet_id);

        return View('acr_ftr::card_result_bank_card', compact('sepet_nav', 'siparis', 'odemeForm', 'ps', 'user_adress', 'company', 'sepetController'));

    }

    function sepet_nav($order_id = null, $step)
    {
        $sepet_link = empty($order_id) ? '' : '?order_id=' . $order_id;
        $navs       = [
            1 => ['sepet', 'SEPET', 11],
            ['adress', 'TESLİMAT BİLGİLERİ', 10],
            ['payment', 'ÖDEME YÖNETİ', 9],
            ['result', ' ÖDEME', 8]
        ];

        $row = '<div id="breadcrumb">';
        $row .= '<ul class="crumbs">';
        foreach ($navs as $key => $nav) {
            $color = $key == $step ? 'color: red; background-position: 100% -96px;' : '';
            $row   .= '<li class="first ">';
            $row   .= '<a ';
            if ($step >= $key) {
                $row .= ' href="/acr/ftr/card/' . $nav[0] . $sepet_link . '"';
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
        $row .= '<div class="form-group">';
        $row .= '<label>Alıcı İsmi</label>';
        $row .= '<input required name="invoice_name" id="invoice_name" class="form-control" placeholder="İsminiz" value="' . @$adress->invoice_name . '">';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>T.C. Kimlik No </label>';
        $row .= '<input  required name="tc" id="tc" class="form-control" placeholder="Kimlik Numaranız" value="' . @$adress->tc . '">';
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
        $row .= '<input name="company"  class="form-control" placeholder="Kurum İsmi" value="' . @$adress->company . '">';

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
                'user_id'      => Auth::user()->id,
                'name'         => $request->input('name'),
                'invoice_name' => $request->input('invoice_name'),
                'tc'           => $request->input('tc'),
                'adress'       => $request->input('adress'),
                'city_id'      => $request->input('city'),
                'county_id'    => $request->input('county'),
                'post_code'    => $request->input('post_code'),
                'tel'          => $request->input('tel'),
                'type'         => $request->input('type'),
                'company'      => $request->input('company'),
                'tax_number'   => $request->input('tax_number'),
                'tax_office'   => $request->input('tax_office'),
                'e_fatura'     => $e_fatura,
                'active'       => 1,

            ];
            $adress_id    = $request->input('adress_id') ? $request->input('adress_id') : 0;

            $adress_id = $adress_model->create($adress_id, $data);
            self::parasut_contact_update($adress_id);

            return Redirect()->back();
        }

    }

    function parasut_contact_update($adress_id)
    {
        $adress_model    = new AcrFtrAdress();
        $parasut         = new ParasutController();
        $adress_row      = $adress_model->where('id', $adress_id)->first();
        $parasut_contact = self::parasut_contact_data($adress_row);
        if (empty($adress_row->parasut_id)) {
            $contact_id = $parasut->contact($parasut_contact);
            $adress_model->where('id', $adress_id)->update(['parasut_id' => $contact_id]);
        } else {
            $parasut->contact_update($adress_row->parasut_id, $parasut_contact);
        }
    }

    function card_adress_edit(Request $request)
    {
        $adress_model = new AcrFtrAdress();
        $sepet_id     = $request->input('sepet_id');
        $sepet_nav    = self::sepet_nav($sepet_id, 2);
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

    function parasut_contact_data($adress_row)
    {

        if ($adress_row->type == 1) {
            $contact_type = 'person';
            $tax_number   = $adress_row->tc;
            $invoice_name = $adress_row->invoice_name;
            $contact_name = $adress_row->invoice_name;
        } else {
            $contact_type = 'company';
            $tax_number   = $adress_row->tax_number;
            $invoice_name = $adress_row->company;
            $contact_name = $adress_row->invoice_name;
        }
        $parasut_contact = [
            'name'                      => $invoice_name,
            'contact_type'              => $contact_type,
            'tax_number'                => $tax_number,
            'tax_office'                => $adress_row->office,
            'category_id'               => null,
            'city'                      => $adress_row->city->name,
            'district'                  => $adress_row->county->name,
            'address_attributes'        => [
                'address' => $adress_row->adress,
                'phone'   => $adress_row->tel,
                'fax'     => null,
            ],
            'contact_people_attributes' => [
                [
                    'name'  => $contact_name,
                    'phone' => $adress_row->tel,
                ],
            ],
        ];
        return $parasut_contact;
    }

    function orders_active(Request $request, $order_id = null)
    {
        $parasut = new ParasutController();

        $order_id    = empty($order_id) ? $request->input('order_id') : $order_id;
        $sepet_model = new Sepet();
        $ps_model    = new Product_sepet();
        $user_model  = new AcrUser();
        /*$parasut_conf     = new Parasut_conf();
        $parasut_conf_row = $parasut_conf->where('user_id', Auth::user()->id)->first();*/
        $adress_model = new AcrFtrAdress();
        $sepet_row    = $sepet_model->where('id', $order_id)->first();

        $adress_row = $adress_model->where('active', 1)->where('user_id', $sepet_row->user_id)->with('city', 'county')->first();


        if (empty($adress_row->parasut_id)) {
            $adress             = $adress_model->find($adress_row->id);
            $parasut_contact    = self::parasut_contact_data($adress_row);
            $parasut_contact_id = $parasut->contact($parasut_contact);
            $adress->parasut_id = $parasut_contact_id;
            $adress->save();

        } else {
            $parasut_contact_id = $adress_row->parasut_id;
        }

        $user                = $user_model->find($sepet_row->user_id);
        $user_row            = $user_model->where('id', $sepet_row->user_id)->first();
        $sepet               = $sepet_model->find($order_id);
        $sepet->active       = 1;
        $sepet->order_result = 2;
        $sepet->save();
        $orders = $ps_model->where('sepet_id', $order_id)->with('product', 'acr_product', 'sepet')->get();
        foreach ($orders as $order) {
            if ($order->type == 2) {
                $user->lisans_durum = 1;
                if (strtotime($user_row->lisans_bitis) < time()) {
                    $lisans_bitis = time();
                } else {
                    $lisans_bitis = strtotime($user_row->lisans_bitis);
                }
                $user->lisans_bitis = self::son_aktif_tarih($order->lisans_ay, $lisans_bitis);
                $user->save();
            }
            $parasut_product_data[] = [
                'product_id'    => $order->acr_product->parasut_id, // the parasut products
                'quantity'      => $order->adet,
                'unit_price'    => round(self::price_set($order) / $order->adet, 4),
                'discount'      => round($order->product->price * $order->sepet->dis_rate, 4),
                'vat_rate'      => $order->product->kdv,
                'discount_type' => 'amount',
                'discount_rate' => $order->sepet->dis_rate,
            ];
            $total_vat[]            = ($order->product->price - ($order->product->price * $order->sepet->dis_rate)) * $order->product->kdv;
        }
        $parasut_sale_data = [
            'description'        => $adress_row->invoice_name,
            'item_type'          => 'invoice',
            'contact_id'         => $parasut_contact_id,
            'gross_total'        => $sepet_row->price,
            'archived'           => null,
            'issue_date'         => date('Y-m-d'),
            'details_attributes' => $parasut_product_data,

        ];
        $invoice           = $parasut->sale($parasut_sale_data);
        //  dd($invoice_id);
        $payment_data = [
            "amount"        => $invoice->net_total,
            "date"          => date('Y-m-d'),
            // "description"   => "Açıklama",
            "account_id"    => $parasut->account_id,
            "exchange_rate" => "1.0"
        ];
        $e_arsiv      = [
            // "note"                      => "Fatura notu",
            "to"       => "urn=>mail=>",
            "scenario" => "commercial"
        ];

        $parasut->paid($invoice->id, $payment_data);
        // $parasut->e_arsiv($invoice->id, $e_arsiv);
        $market_controller = new MarketController();

        $market_controller->order_result(null, $order_id);

    }

    function son_aktif_tarih($ay = null, $lisans_bitis)
    {
        $ekle = $lisans_bitis - time();

        if ($ekle < 0) {
            $ekle = 0;
        }
        $odemeZaman = $ekle + mktime(0, 0, 0, date('m') + $ay, date('d'), date('Y'));
        return date('Y-m-d H:i:s', $odemeZaman);
    }

    function invers_son_aktif_tarih($ay = null, $lisans_bitis)
    {
        $ekle = $lisans_bitis - time();

        if ($ekle < 0) {
            $ekle = 0;
        }
        $odemeZaman = $ekle - mktime(0, 0, 0, date('m') + $ay, date('d'), date('Y'));
        return date('Y-m-d H:i:s', $odemeZaman);
    }

    function orders_deactive(Request $request, $order_id = null)
    {
        $order_id            = empty($order_id) ? $request->input('order_id') : $order_id;
        $sepet_model         = new Sepet();
        $ps_model            = new Product_sepet();
        $user_model          = new AcrUser();
        $sepet               = $sepet_model->find($order_id);
        $sepet->active       = 0;
        $sepet->order_result = 1;
        $sepet->save();
        $sepet_row = $sepet_model->where('id', $order_id)->first();
        $orders    = $ps_model->where('sepet_id', $order_id)->get();
        foreach ($orders as $order) {
            if ($order->type == 2) {
                $user               = $user_model->find($sepet_row->user_id);
                $user_row           = $user_model->where('id', $sepet_row->user_id)->first();
                $user->lisans_durum = 0;
                if (strtotime($user_row->lisans_bitis) < time()) {
                    $lisans_bitis = time();
                } else {
                    $lisans_bitis = strtotime($user_row->lisans_bitis);
                }
                $user->lisans_bitis = self::invers_son_aktif_tarih($order->lisans_ay, $lisans_bitis);
                $user->save();
            }
        }

    }

}
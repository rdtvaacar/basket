<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\acr_files;
use Acr\Ftr\Model\AcrUser;
use Acr\Ftr\Model\Acr_user_table_conf;
use Acr\Ftr\Model\AcrFtrIyzico;
use Acr\Ftr\Model\Acrproduct;
use Acr\Ftr\Model\Bank;
use Acr\Ftr\Model\Company_conf;
use Acr\Ftr\Model\Parasut_conf;
use Acr\Ftr\Model\Product;
use Acr\Ftr\Model\AcrFtrAttribute;
use Acr\Ftr\Model\Sepet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Acr\Ftr\Model\File_model;
use Acr\Ftr\Model\File_dosya_model;
use Auth;

class AcrFtrController extends Controller
{
    function index()
    {
        $user_model = new AcrUser();
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
        $parasut           = new ParasutController();
        $product_model     = new Product();
        $id                = $request->input('id');

        $product_row = $product_model->where('id', $id)->first();
        $data        = [
            'name'       => $product_row->product_name,
            'quantity'   => 1,
            'unit_price' => $product_row->price,
            'vat_rate'   => $product_row->kdv
        ];
        $product_id  = $parasut->product($data);
        if ($acr_product_model->where('product_id', $id)->count() > 0) {
            $acr_product_model->where('product_id', $id)->update(['sil' => 0]);
            $product_id = $id;
        } else {
            $data       = [
                'product_id' => $id,
                'parasut_id' => $product_id,
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
        $controller  = new AcrFtrController();
        $api         = self::my_product_api($request);
        $products    = $api['products'];
        $sepet_count = $api['sepet_counts'];
        return View('acr_ftr::products', compact('products', 'controller', 'sepet_count'));
    }

    function my_product_api(Request $request)
    {
        $product_model = new Acrproduct();
        $sepet_model   = new Sepet();
        $products      = $product_model->where('yayin', 1)->where('sil', 0)->with([
            'u_kats'  => function ($query) {
                //  $query->where('u_kats.sil', 0)->where('u_kats.yayin', 1);
            },
            'product' => function ($query) {
                $query->with([
                    'attributes' => function ($query) {
                        $query->where('attributes.attribute_id', 0);
                        $query->where('attributes.sil', 0);

                    }
                ]);
            },
        ])->get();
        $session_id    = session()->get('session_id');
        if (Auth::check() && !empty($session_id)) {
            $sepet_model->sepet_birle($session_id);
            session()->forget('session_id');
        }
        $sepet_count = empty($sepet_model->sepets($session_id)) ? 0 : $sepet_model->sepets($session_id);
        return ['products' => $products, 'sepet_counts' => $sepet_count];
    }

    function attribute_modal(Request $request)
    {
        $att_model     = new AcrFtrAttribute();
        $product_model = new Acrproduct();
        $att_id        = $request->input('att_id');
        $product_id    = $request->product_id;
        $attribute     = $att_model->where('id', $att_id)->first();
        $product       = $product_model->with([
            'product' => function ($query) use ($att_id) {
                $query->with([
                    'attributes' => function ($query) use ($att_id) {
                        $query->where('attributes.attribute_id', '!=', 0);
                        $query->where('attributes.attribute_id', $att_id);
                    }
                ]);

            }
        ])->where('id', $product_id)->first();
        $row           = '<div class="modal-header">';
        $row           .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>';
        $row           .= '<h4 style="color: #ff1c19 " class="modal-title" id="myModalLabel">' . $attribute->att_name . '</h4>';
        $row           .= '</div>';
        $row           .= '<div class="modal-body">';
        $row           .= '<h4>Bu seçeneğin özellikleri</h4>';
        $row           .= '<ul style="list-style-image: url(/icon/16Tik.png); font-size: 14pt;">';
        foreach ($product->product->attributes as $att) {
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

    function config(Request $request)
    {
        $bank_model            = new Bank();
        $user_conf_model       = new Acr_user_table_conf();
        $parasut_model         = new Parasut_conf();
        $company_conf_model    = new Company_conf();
        $user_table_conf_sorgu = $user_conf_model;
        $user_table_conf_sayi  = $user_table_conf_sorgu->count();

        if ($user_table_conf_sayi == 0) {
            $user_conf_model->insert(['user_id' => 1]);
        }
        $parasut_conf_sorgu = $parasut_model;
        $parasut_conf_sayi  = $parasut_conf_sorgu->count();
        if ($parasut_conf_sayi == 0) {
            $parasut_model->insert(['user_id' => 1]);
        }
        $user_table   = $user_table_conf_sorgu->first();
        $iyzi_model   = new AcrFtrIyzico();
        $banks        = $bank_model->where('sil', 0)->get();
        $company_conf = $company_conf_model->first();
        $bank_form    = self::bank_form($request);
        $iyzico       = $iyzi_model->first();
        $parasut_conf = $parasut_model->first();
        return View('acr_ftr::config', compact('banks', 'bank_form', 'iyzico', 'user_table', 'parasut_conf', 'company_conf'));
    }

    function user_table_update(Request $request)
    {
        $user_conf_model = new Acr_user_table_conf();
        $data            = [
            'user_id'          => Auth::user()->id,
            'name'             => $request->input('name'),
            'user_name'        => $request->input('user_name'),
            'email'            => $request->input('email'),
            'lisans_durum'     => $request->input('lisans_durum'),
            'lisans_baslangic' => $request->input('lisans_baslangic'),
            'lisans_bitis'     => $request->input('lisans_bitis')
        ];
        if ($user_conf_model->count() > 0) {
            $user_conf_model->where('id', $request->id)->update($data);
        } else {
            $user_conf_model->insert($data);
        }
        return redirect()->back();
    }

    function parasut_conf_update(Request $request)
    {
        $parasut_conf = new Parasut_conf();

        $data = [
            'user_id'       => Auth::user()->id,
            'client_id'     => $request->input('client_id'),
            'client_secret' => $request->input('client_secret'),
            'username'      => $request->input('username'),
            'password'      => $request->input('password'),
            'company_id'    => $request->input('company_id')

        ];
        if ($parasut_conf->count() > 0) {
            $parasut_conf->where('id', $request->id)->update($data);
        } else {
            $parasut_conf->insert($data);
        }
        $parasut    = new ParasutController();
        $account_id = $parasut->account_id();
        $parasut_conf->where('id', $request->id)->update(['account_id' => $account_id]);
        return redirect()->back();
    }

    function company_conf_update(Request $request)
    {
        $company_model = new Company_conf();

        $data = [
            'user_id' => Auth::user()->id,
            'name'    => $request->input('name'),
            'city'    => $request->input('city'),
            'county'  => $request->input('county'),
            'adress'  => $request->input('adress'),
            'tel'     => $request->input('tel'),
            'email'   => $request->input('email')

        ];
        if ($company_model->count() > 0) {
            $company_model->where('id', $request->id)->update($data);
        } else {
            $company_model->insert($data);
        }
        return redirect()->back();
    }

    function iyzico_update(Request $request)
    {
        $iyzi_model = new AcrFtrIyzico();
        $data       = [
            'user_id'        => Auth::user()->id,
            'setApiKey'      => $request->input('setApiKey'),
            'setSecretKey'   => $request->input('setSecretKey'),
            'setBaseUrl'     => $request->input('setBaseUrl'),
            'setCallbackUrl' => $request->input('setCallbackUrl'),
        ];

        $sayi = $iyzi_model->count();
        if ($sayi > 0) {
            $iyzi_model->where('id', $request->id)->update($data);
        } else {
            $iyzi_model->insert($data);
        };
        return redirect()->back();
    }

    function active_bank(Request $request)
    {
        $bank_model = new Bank();
        $bank_model->where('id', $request->input('bank_id'))->update(['active' => 1]);
    }

    function deactive_bank(Request $request)
    {
        $bank_model = new Bank();
        $bank_model->where('id', $request->input('bank_id'))->update(['active' => 2]);
    }

    function bank_edit(Request $request)
    {
        $bank_model = new Bank();
        $bank_id    = $request->input('bank_id');
        $bank       = $bank_model->where('id', $bank_id)->first();
        return self::bank_form($request, $bank);
    }

    function bank_delete(Request $request)
    {
        $bank_model = new Bank();
        $bank_id    = $request->input('bank_id');
        $bank_model->where('id', $bank_id)->update(['sil' => 1]);

    }

    function bank_create(Request $request)
    {
        $bank_model = new Bank();
        $data       = [
            'user_id'     => Auth::user()->id,
            'name'        => $request->input('name'),
            'bank_name'   => $request->input('bank_name'),
            'user_name'   => $request->input('user_name'),
            'iban'        => $request->input('iban'),
            'bank_number' => $request->input('bank_number'),
            'active'      => $request->input('active'),

        ];
        $bank_id    = empty($request->input('bank_id')) ? 0 : $request->input('bank_id');
        $bank_model->create($bank_id, $data);
        return redirect()->back();
    }

    function bank_form(Request $request, $bank = null)
    {

        $row = '<form method="post" action="/acr/ftr/bank/create">';
        $row .= csrf_field();
        $row .= '<div class="form-group">';
        $row .= '<label>Görünen İsim</label>';
        $row .= '<input required name="name" id="name" class="form-control" placeholder="Görünen İsim" value="' . @$bank->name . '">';
        $row .= '</div>';

        $row .= '<div class="form-group">';
        $row .= '<label>Banka İsmi </label>';
        $row .= '<input required name="bank_name"  class="form-control" placeholder="Banka Sahibi" value="' . @$bank->bank_name . '">';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Hesap Sahibi</label>';
        $row .= '<input required name="user_name"  class="form-control" placeholder="Hesap Sahibi" value="' . @$bank->user_name . '">';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>İban</label>';
        $row .= '<input required name="iban"  class="form-control" placeholder="İban" value="' . @$bank->iban . '">';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        $row .= '<label>Hesap Numarası</label>';
        $row .= '<input required name="bank_number"  class="form-control" placeholder="Hesap Numarası" value="' . @$bank->bank_number . '">';
        $row .= '</div>';
        $row .= '<div class="form-group">';
        if (@$bank->active == 1 || empty(@$bank->active)) {
            $aktif   = 'checked';
            $deaktif = '';
        } else {
            $aktif   = '';
            $deaktif = 'checked';
        }
        $row .= '<label>';
        $row .= '<input ' . $aktif . ' type="radio"  required name="active" class="flat-red" value="1">';
        $row .= 'Aktif Et';
        $row .= '</label>';
        $row .= '<label>';
        $row .= '<input ' . $deaktif . ' type="radio" required name="active" class="flat-red" value="2">';
        $row .= 'Deaktif Et';
        $row .= '</label>';
        $row .= '</div>';
        $row .= '<input type="hidden" name="bank_id"  value="' . @$bank->id . '">';
        $row .= '<button type="submit" class="btn btn-primary">BANKA BİLGİLERİNİ KAYDET </button>';
        $row .= '</form>';
        return $row;
    }

}
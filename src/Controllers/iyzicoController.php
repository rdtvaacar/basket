<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\AcrFtrAdress;
use Input, Auth, Request;
use App\Http\Controllers\marketController;
use App\Siparis;
use DB;
use Acr\Ftr\Model\AcrFtrIyzico;

class iyzicoController extends Controller
{

    function option()
    {
        include(base_path() . '/vendor/iyzico/iyzipay-php/IyzipayBootstrap.php');

        $options      = new \Iyzipay\Options();
        $iyzico_model = new AcrFtrIyzico();
        $options->setApiKey($iyzico_model->setApiKey);
        $options->setSecretKey($iyzico_model->setSecretKey);
        $options->setBaseUrl($iyzico_model->setBaseUrl);

        return $options;
    }

    function apiTest()
    {
        $iyzipayResource = \Iyzipay\Model\ApiTest::retrieve(Self::option());
# print result
        dd($iyzipayResource);
    }

    public function odemeForm($price = null, $paidPrice = null, $basketId = null)
    {
        
        self::odemeFormIc($price, $paidPrice, $basketId);
        ?>
        <html>
        <body>
        <div id="iyzipay-checkout-form" class="responsive"></div>
        </body>
        </html>

    <?php }

    public function odemeFormPopup($price = null, $paidPrice = null, $basketId = null)
    {
        self::odemeFormIc($price, $paidPrice, $basketId)

        ?>
        <html>
        <body>
        <div id="iyzipay-checkout-form" class="popup"></div>
        </body>
        </html>

    <?php }

    function odemeFormIc($price = null, $paidPrice = null, $basketId = null)
    {
        $adress_model = new AcrFtrAdress();
        $adresses     = $adress_model->where('user_id', Auth::user()->id)->where('active', 1)->with('city', 'county')->first();
        $sehir        = $adresses->city->name;
        $adres        = $adresses->adress;
        $user_name    = empty(Auth::user()->name) ? Auth::user()->ad : Auth::user()->name;
        $ad           = $adresses->type == 2 ? $adresses->campany : $user_name;
        # create request class
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice($price);
        $request->setPaidPrice($paidPrice);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basketId);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://konaksar.com/i_odemeSonuc");
        $request->toPKIRequestString();
        $request->setEnabledInstallments(array(2, 3, 6, 9));
        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(Auth::user()->id);
        $buyer->setName($ad);
        $buyer->setSurname($ad);
        $buyer->setGsmNumber(Auth::user()->tel);
        $buyer->setEmail(Auth::user()->email);
        $buyer->setIdentityNumber(rand(10000000000, 99999999999));
        $buyer->setLastLoginDate("2015-10-05 12:43:35");
        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($adres);
        $buyer->setIp(Request::ip());
        $buyer->setCity($sehir);
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName(Auth::user()->ad);
        $shippingAddress->setCity($sehir);
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress($adres);
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);
        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName(Auth::user()->ad);
        $billingAddress->setCity($sehir);
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($adres);
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);
        $basketItems     = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($basketId);
        $firstBasketItem->setName("Üyelik İşlemi");
        $firstBasketItem->setCategory1("konaksar.com");
        $firstBasketItem->setCategory2("Business");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice("1");
        $basketItems[0] = $firstBasketItem;

        $request->setBasketItems($basketItems);
# make request
        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Self::option());
# print result
        print_r($checkoutFormInitialize->getCheckoutFormContent());
    }

    function odemeSonuc()
    {
        $mail  = new MailController();
        $token = Input::get('token');

        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setToken($token);
        # make request
        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Self::option());
        # print result
        $siparis = Siparis::where('id', $checkoutForm->getBasketId())->first();

        switch ($siparis->urunIsim) {
            case 'sms100';
            case 'sms250';
            case 'sms500';
            case 'sms1000';
                $uyeSmsSorgu = DB::table('smspaketi')->where('uyeID', Auth::user()->id);
                if ($uyeSmsSorgu->count() > 0) {
                    $uyeSms = $uyeSmsSorgu->first();
                    $data   = [
                        'miktar' => $uyeSms->miktar + $siparis->paket
                    ];
                    DB::table('smspaketi')->where('uyeID', Auth::user()->id)->update($data);
                } else {
                    $data = [
                        'miktar' => $siparis->paket,
                        'uyeID'  => Auth::user()->id
                    ];
                    DB::table('smspaketi')->insert($data);
                }
                Siparis::where('id', $siparis->id)->update(['siparis_onay' => 1]);
                my::mail('acarbey15@gmail.com', 'Aydın ', 'Yeni SMS Siparişi', 'mail.yeniSiparis', Auth::user()->ad . '<br>' . Auth::user()->tel . '<br> Az önce ödeme yaptı <br> Adet : ' . $siparis->paket . '<br> Paket :  <br>Ödeme Şekli : Kredi Kartı <br>' . Auth::user()->email);
                return redirect()->to('i_smsBasarili');
                break;
            default;
                if ($checkoutForm->getStatus() == "success" && $checkoutForm->getPaymentStatus() == "SUCCESS" && $siparis->siparis_onay != 1) {

                    $siparis = Siparis::where('id', $checkoutForm->getBasketId())->first();
                    marketController::siparisOnayla($checkoutForm->getBasketId());
                    $mail->mailGonder('mail.orders');
                }
                return redirect()->to('i_basarili');
                break;
        }

    }
}
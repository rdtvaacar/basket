<?php

namespace Acr\Ftr\Controllers;

use Acr\Ftr\Model\AcrFtrAdress;
use Acr\Ftr\Model\Sepet;
use Auth;
use App\Http\Controllers\MarketController;
use App\Siparis;
use DB;
use Acr\Ftr\Model\AcrFtrIyzico;
use Request;

class iyzicoController extends Controller
{

    function option()
    {
        include(base_path() . '/vendor/iyzico/iyzipay-php/IyzipayBootstrap.php');

        $options      = new \Iyzipay\Options();
        $iyzico_model = new AcrFtrIyzico();
        $iyzico       = $iyzico_model->first();
        $options->setApiKey($iyzico->setApiKey);
        $options->setSecretKey($iyzico->setSecretKey);
        $options->setBaseUrl($iyzico->setBaseUrl);

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

        return '<div id="iyzipay-checkout-form" class="responsive"></div>';

    }

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
        $iyzico_model = new AcrFtrIyzico();
        $iyzico       = $iyzico_model->first();
        $adress_model = new AcrFtrAdress();
        $adresses     = $adress_model->where('user_id', Auth::user()->id)->where('active', 1)->with('city', 'county')->first();
        $sehir        = $adresses->city->name;
        $adres        = $adresses->adress;
        $user_name    = empty(Auth::user()->name) ? Auth::user()->ad : Auth::user()->name;
        $ad           = $adresses->type == 2 ? $adresses->company : $adresses->invoice_name;
        # create request class
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice($price);
        $request->setPaidPrice($paidPrice);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setBasketId($basketId);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($iyzico->setCallbackUrl);
        $request->toPKIRequestString();
        $request->setEnabledInstallments(array(2, 3, 6, 9));
        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId(Auth::user()->id);
        $buyer->setName($ad);
        $buyer->setSurname($ad);
        $buyer->setGsmNumber(Auth::user()->tel);
        $buyer->setEmail(Auth::user()->email);
        $buyer->setIdentityNumber(rand(10000000000, 99999999999));
        $buyer->setLastLoginDate(date('Y-m-d H:i:s', strtotime(Auth::user()->updated_at)));
        $buyer->setRegistrationDate(date('Y-m-d H:i:s', strtotime(Auth::user()->created_at)));
        $buyer->setRegistrationAddress($adres);
        $buyer->setIp(Request::ip());
        $buyer->setCity($sehir);
        $buyer->setCountry("Turkey");
        $buyer->setZipCode($adresses->post_code);
        $request->setBuyer($buyer);
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName(Auth::user()->name);
        $shippingAddress->setCity($sehir);
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress($adres);
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);
        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName(Auth::user()->name);
        $billingAddress->setCity($sehir);
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress($adres);
        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);
        $basketItems     = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($basketId);
        $firstBasketItem->setName("Satis");
        $firstBasketItem->setCategory1("Satis");
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

    function odemeSonuc(Request $request)
    {
        $sepet_model = new Sepet();

        $token   = $request->token;
        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setToken($token);
        # make request
        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Self::option());
        # print result
        $siparis = $sepet_model->where('id', $checkoutForm->getBasketId())->first();
        if ($checkoutForm->getStatus() == "success" && $checkoutForm->getPaymentStatus() == "SUCCESS" && $siparis->siparis_onay != 1) {
            $marketController = new MarketController();
            $marketController->siparisOnayla($checkoutForm->getBasketId());
        }


    }
}
@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/css/acr_ftr/sepet.css">
    <link rel="stylesheet" href="/plugins/iCheck/all.css">
@stop
@section('acr_ftr')
    <section class="invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <i class="fa fa-globe"></i> SİPARİŞ NUMARANIZ : <span class="text-red"><b><?php echo $siparis_id = empty($siparis->id) ? 0 : $siparis->id; ?></b></span>
                    <small class="pull-right">Tarih: {{date('d/m/Y',strtotime($siparis->updated_at))}}</small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-5 invoice-col">
                Satıcı
                <address>
                    <strong>{{$company->name}}</strong><br>
                    {{$company->adress}}<br>
                    {{$company->county}} / {{$company->city}}<br>
                    Phone: {{$company->tel}}<br>
                    Email: {{$company->email}}
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-5 invoice-col">
                Alıcı
                <address>
                    <strong>{{$user_adress->type ==2 ? $user_adress->company:$user_adress->invoice_name}}</strong><br>
                    {{$user_adress->adress}}<br>
                    {{$user_adress->county->name}} / {{$user_adress->city->name}}<br>
                    Telefon: {{$user_adress->tel}}<br>
                    Email: {{Auth::user()->email}}
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-2 invoice-col">

                <b>Sipariş NO:</b> <?php echo $siparis_id = empty($siparis->id) ? 0 : $siparis->id; ?><br>
                <b>Tarih:</b> {{date('d/m/Y',strtotime($siparis->updated_at))}}<br>
                <b>Hesap ID'niz:</b> {{Auth::user()->id}}
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Sira</th>
                        <th>Product</th>
                        <th>Ürün No #</th>
                        <th>Adet</th>
                        <th>Ay</th>
                        <th>Birim Fiyatı</th>
                        <th>İndirim Oranı</th>
                        <th>KDV</th>
                        <th>Fiyat</th>
                        <th>Toplam</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ps as $key=> $pss)
                        <?php
                        $toplam = $sepetController->price_set($pss);
                        $tKdv = $toplam * $pss->product->kdv / 100;
                        $toplamKdv[] = $tKdv;
                        $araToplam[] = $toplam - $tKdv;

                        ?>
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>{{$pss->product->product_name}}</td>
                            <td><span class="text-danger"><b>{{$u_kods[] = $pss->product->id}}</b></span></td>
                            <td>{{$pss->adet}}</td>
                            <td>{{$pss->lisans_ay}}</td>
                            <td>
                                <?php echo empty($pss->product->dis_price) || $pss->product->dis_price == 0 || ($pss->product->price == $pss->product->price) ? $pss->product->price :
                                    '<strike style="font-size: 10pt;">' . $pss->product->price . ' </strike> ' . $pss->product->dis_price ?>₺
                            </td>
                            </td>
                            <td>%{{round($pss->dis_rate,2) * 100}}</td>
                            <td><span style="font-size:8pt;" class="text-muted">%{{$pss->product->kdv}} </span>{{round($tKdv,2)}}₺</td>
                            <td>{{round($toplam - $tKdv,2)}}₺</td>
                            <td>{{round($toplam,2)}}₺</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">
                <p class="lead">Ödeme Yöntemi:</p>
                EFT / HAVALE
                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                    Ödemenizi kredi aşağıdaki banka hesabına yapınız açıklama kısmına kodunuzu ekleyiniz. <br> <span class="text-aqua">
                        Sipariş Kodunuz : <?php echo $siparis_id = empty($siparis->id) ? 0 : $siparis->id; ?> -
                        @foreach($u_kods as $key=> $u_kod)
                            {{$u_kod}}
                            @if(count($u_kods)>$key+1)
                                -
                            @endif
                        @endforeach
                    </span>
                </p>
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>Banka Adı</td>
                        <td>{{$bank->bank_name}}</td>
                    </tr>
                    <tr>
                        <td>Hesap Sahibi</td>
                        <td>{{$bank->user_name}}</td>
                    </tr>
                    <tr>
                        <td>İban Numarası</td>
                        <td>{{$bank->iban}}</td>
                    </tr>
                    <tr>
                        <td>Hesap Numarası</td>
                        <td>{{$bank->bank_number}}</td>
                    </tr>
                </table>
            </div>
            <!-- /.col -->
            <div class="col-xs-6">
                <p class="lead">Toplam Alış-veriş Miktarı {{date('d/m/Y',strtotime($siparis->updated_at))}}</p>

                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th style="width:50%">Ara Toplam:</th>
                            <td>{{round(array_sum($araToplam),2)}}₺</td>
                        </tr>
                        <tr>
                            <th>KDV</th>
                            <td>{{round(array_sum($toplamKdv),2)}}₺</td>
                        </tr>
                        <tr>
                            <th>Toplam:</th>
                            <td>
                                {{-- <strike style="font-size: 10pt;">{{round(array_sum($araToplam) + array_sum($toplamKdv),2)}}</strike>--}}
                                <span style="font-size: 14pt;" class="text-red">{{round((array_sum($araToplam) + array_sum($toplamKdv)),2)}}₺</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>

        <!-- /.row -->

        <!-- this row will not appear when printing -->
        {{--<div class="row no-print">
            <div class="col-xs-12">
                <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
                <button type="button" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment
                </button>
                <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
                    <i class="fa fa-download"></i> Generate PDF
                </button>
            </div>
        </div>--}}
    </section>
@stop

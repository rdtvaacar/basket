@extends('acr_ftr.index')
@section('header')
    <style>
        .kisiKarti ul {

            margin: 0;
            padding: 0;
            float: left;
            text-align: left;
            margin: 10px 0 10px 5px;
            font-size: 10pt;
        }

        .kisiKarti li {
            padding: 0;
            font-size: 16pt;
            margin: 0;
        }

        .kisiKarti h4, h3 {
            padding: 4px;
        }

        .tablo {
            width: 100%;
        }

        .tablo td {
            padding: 6px;
        }

        .stun {
            border-bottom: rgba(39, 41, 47, 1) 1px dotted;

        }

        .stun_1 {

        }

        .stun_2 {

        }

        .price-table .col-md-2, .price-table .col-md-4, .price-table .col-md-6 {
            padding: 0 1px;
            margin: 4px 0 2px 0;
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .price-table .col-md-3, .price-table .col-md-4, .price-table .col-md-6 {
                padding: 0 15px;
            }
        }

        @media (min-width: 1200px) {
            .price-table .col-md-3, .price-table .col-md-4, .price-table .col-md-6 {
                padding: 0 1px;
            }
        }

        /* Pricing Tables - Boxes */
        .price-table .peice-list {
            background: none repeat scroll 0 0 transparent;
            border: 0;
            padding: 0;
        }

        .price-table .price-col1 .peice-list {
            background-color: transparent;
        }

        .price-table .price-col2 .peice-list {
            background-color: #343844;
        }

        .price-table .price-col3 .peice-list {
            background-color: #3451c6;
        }

        .peice-list * {
            list-style: none;
            line-height: 1;
        }

        .peice-list .pack-price {
            background: none repeat scroll 0 0 rgba(255, 255, 255, 0.1);
            text-align: center;
            padding: 12px 0 18px;
            color: #EAEAEA;
            font-weight: 500;
            font-size: 15px;
        }

        .peice-list .pack-price span {
            color: #fff;
            font-weight: 900;
            font-size: 53px;
            display: block;
            padding: 10px 0;
        }

        .peice-list li {
            padding: 0.9375em;
            text-align: left;
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 200;
            border-bottom: 1px dotted rgba(255, 255, 255, 0.13);
            line-height: 27px;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
        }

        .peice-list .price-table-btn {
            background: none repeat scroll 0 0 rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 4px 0;
            -webkit-border-radius: 0 0 6px 6px;
            -moz-border-radius: 0 0 6px 6px;
            border-radius: 0 0 6px 6px;
        }

        .peice-list .price-table-btn p {
            padding: 36px 0 31px;
        }

        .peice-list .price-table-btn a {
            color: #333;
            font-size: 16px;
            font-weight: 800;
            background: #fff;
            padding: 17px;
            text-shadow: 0 0 0;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            -webkit-transition: all 1s ease; /* Safari 3.2+, Chrome */
            -moz-transition: all 0.3s ease; /* Firefox 4-15 */
            -o-transition: all 0.3s ease; /* Opera 10.5-12.00 */
            transition: all 0.3s ease; /* Firefox 16+, Opera 12.50+ */
        }

        .peice-list .price-table-btn a:hover {
            background: #000;
            color: #fff;
            text-decoration: none;
        }

        .price-table {
            padding: 0;
            overflow: hidden;
            margin: 0 2px;
        }

        .price-table p {
            text-align: center;
        }

        .price-table .title-2 {
            background: none repeat scroll 0 0 #3451c6;
            padding: 21px 0;
            text-align: center;
            letter-spacing: .07em;
            color: #fff;
            font-weight: 500;
            font-size: 17px;
            margin: 20px 0 2px 0;
            position: relative;
        }

        .price-table .price-col2 .title-2 {
            background-color: #343844;
        }

        .price-table .price-col3 .title-2 {
            background-color: #3451c6;
            font-size: 21px;
            margin: 0 0 2px;
            padding: 31px 0;
        }

        .price-table .price-col3 .price-table-btn {
            padding: 14px 0;
        }

        .price-table .price-col1 .peice-list li {
            text-align: left;
            color: #333;
            font-weight: 400;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.3);
        }

        .price-table .price-col1 .title-2 {
            background-color: #343844;
        }

        .peice-list .pack-price {
            padding: 14px 0 20px;
        }

        .peice-list .pack-price span {
            font-size: 33px;
        }

        .peice-list .pack-price span sub {
            font-size: 14px;
            padding-left: 2px;
            font-weight: 400;
            text-shadow: none;
            top: 0px;
            vertical-align: baseline;
            position: relative;
        }

        @media only screen and (min-width: 720px) and (max-width: 959px) {
            .peice-list .pack-price span {
                font-size: 33px;
            }

            .peice-list .price-table-btn a {
                font-size: 14px;
                padding: 9px 17px;
                border-radius: 6px
            }
        }
    </style>
@stop
@section('acr_ftr')
    <section class="content">
        <div class="row">
            <div class=" col-md-12">
                <form action="/acr/ftr/product" method="post"></form>
                <select class="form-control" id="kat_1" style="float: left; width:280px;">
                    <option value="">Seçiniz</option>
                    @foreach($p_kats as $kat)
                        <option value="{{$kat->id}}"><b>{{@$kat->kat_isim}}</b></option>
                    @endforeach
                </select>
                <div id="categories_1"></div>
                <div id="categories_2"></div>
                <div id="categories_3"></div>
                <div onmouseenter="sepet_goster()" onmouseleave="sepet_gizle()" style="position:relative;">
                    <a href="/acr/ftr/card/sepet" style="float: right;" class="btn btn-app">
                        <span class="badge bg-teal sepet_count" style="font-size: 12pt;"><?php echo $sepet_count ?></span>
                        <i class="fa  fa-shopping-cart"></i> SEPET
                    </a>
                    <div id="sepet_row" style="display: none;">
                        <div class="box box-warning" style="width: 550px; right:0; top: 60px; position: absolute; z-index: 1; ">
                            <div class="box-header with-border">Sepetiniz</div>
                            <div class="box-body">
                                <table width="100%" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th width="60%">Ürün</th>
                                        <th width="20%">Adet</th>
                                        <th>Fiyat</th>
                                        <th style="text-align: right">Sil</th>
                                    </tr>
                                    </thead>
                                    <tbody id="sepet_tbody"></tbody>
                                    <tfoot>
                                    <tr>
                                        <td><a style="float: left;" class="btn btn-warning" href="/acr/ftr/card/sepet">SATIN AL</a></td>
                                        <td colspan="3">
                                            <div style="font-size: 9pt; float: right; cursor:pointer;" onclick="sepet_delete_all()">Tümünü Sil</div>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear:both;"></div>
                <div class="box">
                    <div class="box-header with-border">ÜRÜNLER</div>
                    <div class="box-body">
                        <div class="price-table">
                            <?php
                            //  dd($products);
                            foreach ($products as $key =>$product) {
                            ?>
                            <div style="min-height:760px;" class="col-md-4 all_categories
                            @foreach($product->product->u_kats as $u_kat)
                                    kat_{{@$u_kat->id }}
                            @foreach($u_kat->u_kats as $uu_kat)
                                    kat_{{$uu_kat->id }}
                            @endforeach

                            @endforeach">
                                <div class="price-col2">
                                    <div class="title-2"><?php echo $product->product->product_name; ?></div>
                                    <div class="col-md-12" style="text-align: center;">
                                        @if(!empty($product->product->file))
                                            <a href="/acr/ftr/product/detail?product_id={{$product->product->id}}"> <img class="img-rounded"
                                                                                                                         style="cursor:pointer; margin-top: 10px;"
                                                                                                                         src="https://eticaret.webuldum.com/acr_files/{{$product->product->file->acr_file_id}}/thumbnail/{{$product->product->file->file_name}}.{{$product->product->file->file_type}}"/>
                                            </a>
                                            <hr style="padding: 0;">
                                        @endif
                                    </div>
                                    <ul class="peice-list">
                                        <?php  //dd($product->attributes);
                                        foreach ($product->product->attributes as $attribute) { ?>
                                        <li>
                        <span style="cursor:pointer;" onclick="urunGoster(<?php echo $attribute->id ?>,<?php echo $product->id ?>)"><?php echo $attribute->att_name ?> <span
                                    class="glyphicon glyphicon-question-sign"></span></span>
                                        </li>
                                        <?php } ?>
                                        <li class="pack-price">
                        <span>
                            <?php
                            if ($product->product->type == 1) {?>
                            <?php echo $product->product->price ?>
                            <sub> ₺  /Ay </sub>
                            <?php }else { ?>
                            <?php echo $product->product->price ?> <sub> ₺ / Adet</sub>
                            <?php }
                            ?>
                        </span>
                                            <br>
                                            <?php if ($product->product->dis_price && ($product->product->dis_moon > 0 || $product->product->dis_person > 0)) {
                                                echo '%' . $product->product->max_dis . ' varan indirim ';
                                            } ?>
                                        </li>

                                        <li style="text-align: center">
                                            <a href="/acr/ftr/product/detail?product_id=<?php echo $product->product->id ?> " class="btn btn-success  ">DETAYLI İNCELE</a>
                                        <!--<p><a href="/acr/ftr/card/sepet?product_id=<?php echo $product->product->id ?> " class="btn btn-success  ">SATIN AL</a>
                                                <button onclick="sepete_ekle(<?php echo $product->product->id ?>)" class="btn bg-orange margin">SEPETE EKLE</button>
                                            </p>-->
                                            <hr>
                                            <a class="text-yellow" href="/acr/ftr/card/sepet">Sepete Git (<span class="text-aqua sepet_count" style="font-size: 12pt;"><?php echo $sepet_count ?></span>)</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php }
                            ?>
                        </div>
                        <div class="modal fade" id="sepetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div id="sepetAciklama"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div style="text-align: center;" id="imageModal_div"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script>
        $('#kat_1').change(function () {
            var kat_id = $(this).val();
            categories(1, kat_id);
        })
        function categories(kat, kat_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/categories',
                data: 'kat_id=' + kat_id + '&kat=' + kat,
                success: function (veri) {
                    $('#categories_' + kat).html(veri)
                    $('.all_categories').fadeOut()
                    $('.kat_' + kat_id).fadeIn()

                }
            });
        }
        function urunGoster(att_id, product_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/attribute/modal',
                data: 'att_id=' + att_id + '&product_id=' + product_id,
                success: function (veri) {
                    $('#sepetModal').modal('show');
                    $('#sepetAciklama').html(veri);
                    //  window.history.pushState('Object', sayfaEki, 'https://okuloncesievrak.com/urunGoster/?sayfaEki=' + sayfaEki);
                }
            });
        }
        function sepete_ekle(product_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/create',
                data: 'product_id=' + product_id,
                success: function () {
                    var sepet_count = $('.sepet_count').html();
                    $('.sepet_count').html(parseInt(sepet_count) + 1)
                }
            });
        }
        function sepet_goster() {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/products',
                success: function (veri) {
                    console.log(veri)
                    $('#sepet_tbody').html(veri);
                    $('#sepet_row').show();
                }
            });
        }
        function sepet_gizle() {
            $('#sepet_row').hide();
        }
        function sepet_adet_guncelle(sepet_id) {
            var adet = $('#sepet_adet_' + sepet_id).val();
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/sepet_adet_guncelle',
                data: 'sepet_id=' + sepet_id + '&adet=' + adet,
                success: function (veri) {
                    $('.sepet_count').html(veri);
                    $.ajax({
                        type: 'post',
                        url: '/acr/ftr/product/sepet/sepet_total_price',
                        data: 'sepet_id=' + sepet_id,
                        success: function (msg) {
                            $('#product_price_' + sepet_id).html(msg + '₺');
                            $.ajax({
                                type: 'post',
                                url: '/acr/ftr/product/sepet/product_sepet_total_price',
                                data: 'sepet_id=' + sepet_id,
                                success: function (msg) {
                                    $('#acr_sepet_total_price').html(msg + '₺');
                                    $('#product_dis_' + sepet_id).hide();

                                }
                            });
                        }
                    });

                }
            });
        }

        function sepet_delete(sepet_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/delete',
                data: 'sepet_id=' + sepet_id,
                success: function (veri) {
                    $('.sepet_count').html(veri);
                    $('#sapet_row_' + sepet_id).fadeOut(400);
                }
            });
        }

        function image_viewer(product_id, image_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/image/modal',
                data: 'product_id=' + product_id + '&image_id=' + image_id,
                success: function (veri) {
                    $('#imageModal').modal('show');
                    $('#imageModal_div').html(veri);
                }
            });
        }

        function sepet_delete_all() {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/delete_all',
                success: function (veri) {
                    $('.sepet_count').html(0);
                    $('.sepet_row').fadeOut(400);
                }
            });
        }
    </script>
@stop
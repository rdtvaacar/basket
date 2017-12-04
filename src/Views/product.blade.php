@extends('acr_ftr.index')
@section('header')
    <style>
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        }

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
    <link rel="stylesheet" href="/blueimp/css/blueimp-gallery.min.css">

@stop
@section('acr_ftr')
    <section class="content">
        <div class="row">
            <div class=" col-md-8">
                <div class="box box-primary">
                    <div class="box-body">
                        <div class=" col-md-10">
                            <div id="product_img">
                                <img width="100%" class="img-thumbnail" src="//eticaret.webuldum.com/acr_files/{{$product->file->acr_file_id}}/medium/{{$product->file->file_name}}.{{$product->file->file_type}}"
                                     alt="{{$product->file->org_file_name}}"/>
                            </div>
                        </div>
                        <div class=" col-md-2">
                            <div style="overflow: auto; height:700px;" class="webkit-scrollbar-thumb">
                                @foreach($product->files as $file)
                                    <div onclick="product_image({{$product->id}},{{$file->id}})" style="float: left; cursor:pointer;">
                                        <img class="img-thumbnail" src="//eticaret.webuldum.com/acr_files/{{$file->acr_file_id}}/thumbnail/{{$file->file_name}}.{{$file->file_type}}" alt="{{$file->org_file_name}}"/>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class=" col-md-4">
                <div class="box box-primary">
                    <div class="box-header">
                        <strong>{{$product->product_name}}</strong>
                        <hr>
                    </div>
                    <div class="box-body">
                        <div class="text-green " style=" font-size: 3em; text-align: center"><strong>{{$product->price}}₺</strong></div>
                        <hr>
                        @if(count($product->product_yakas)>0)
                            <label>Yaka Seçiniz</label>
                            <select class="form-control" name="yaka">
                                <option value="">SEÇİNİZ</option>
                                @foreach($product->product_yakas as $yaka)
                                    <option value="{{$yaka->yaka->id}}">{{$yaka->yaka->name}}</option>
                                @endforeach
                            </select>
                        @endif
                        <hr>
                        @if(count($product->product_sizes)>0)
                            <label>Beden Seçiniz</label>
                            <select class="form-control" name="yaka">
                                <option value="">SEÇİNİZ</option>
                                @foreach($product->product_sizes as $size)
                                    <option value="{{$size->size->id}}">{{$size->size->name}}</option>
                                @endforeach
                            </select>
                        @endif
                        <hr>

                        <hr>
                        <a style="float: left" href="/acr/ftr/card/sepet?product_id=<?php echo $product->id ?> " class="btn btn-success  ">SATIN AL</a>
                        <button style="float: right" onclick="sepete_ekle(<?php echo $product->id ?>)" class="btn bg-orange ">SEPETE EKLE</button>
                    </div>
                    <div class="box-footer">
                        <a class="text-yellow" href="/acr/ftr/card/sepet">Sepete Git (<span class="text-aqua sepet_count" style="font-size: 12pt;"><?php echo $sepet_count ?></span>)</a>
                    </div>
                </div>
            </div>
            <div class=" col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3><strong>Ürün Özellikleri</strong></h3>
                    </div>
                    <div class="box-body">
                        @foreach($product->attributes as $attribute)
                            <div style="text-indent: 20px; padding-top: 10px;">
                                <strong>{{$attribute->att_name}}</strong>
                            </div>
                            <div style="text-indent: 20px; padding: 10px; border-bottom: 1px solid #d6dadf" class="text-muted">
                                {!! $attribute->att_text !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="/blueimp/js/blueimp-gallery.min.js"></script>
    <script>
        document.getElementById('links').onclick = function (event) {
            event = event || window.event;
            var target = event.target || event.srcElement,
                link = target.src ? target.parentNode : target,
                options = {index: link, event: event},
                links = this.getElementsByTagName('a');
            blueimp.Gallery(links, options);
        };
        function product_image(product_id, img_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/img',
                data: 'img_id=' + img_id + '&product_id=' + product_id,
                success: function (veri) {
                    $('#product_img').html(veri);
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
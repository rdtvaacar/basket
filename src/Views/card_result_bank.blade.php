@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/css/acr_ftr/sepet.css">
    <link rel="stylesheet" href="/plugins/iCheck/all.css">
@stop
@section('acr_ftr')
    <?php echo $sepet_nav ?>
    <div class="box box-warning" style="width: 100%; right:0; top: 60px; position: absolute; z-index: 1; ">
        <div class="box-header with-border">SİPARİŞ BİLGİlERİ
            <a href="/acr/ftr/orders" style="float: right;" class="btn btn-success">SİPARİŞLERİM</a>
        </div>
        <div class="box-body">

        </div>
    </div>

@stop
@section('footer')
    <script src="/plugins/iCheck/icheck.min.js"></script>
    <script>
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass   : 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass   : 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass   : 'iradio_flat-green'
        });

        $('#city').change(function () {
            city_id = $(this).val();
            county_get(city_id);
        });
        function county_get(city_id) {
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/card/adress/county',
                data   : 'city_id=' + city_id,
                success: function (veri) {
                    $('#county').html(veri);
                }
            });
        }

        function adress_delete(adres_id) {
            if (confirm('Adres bilgilerini silmek istediğinizden eminmisiniz.') == true) {
                $.ajax({
                    type   : 'post',
                    url    : '/acr/ftr/card/adress/delete',
                    data   : 'adres_id=' + adres_id,
                    success: function () {
                        $('#adres_div_' + adres_id).fadeOut(400);
                    }
                });
            }
        }


    </script>
@stop
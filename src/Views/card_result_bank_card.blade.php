@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/css/acr_ftr/sepet.css">
    <link rel="stylesheet" href="/plugins/iCheck/all.css">
@stop
@section('acr_ftr')
    <div class=" col-md-12">
        <div class="box box-warning">
            <div class="box-header with-border"><?php echo $sepet_nav ?>
                <a href="/acr/ftr/orders" style="float: right;" class="btn btn-success">SİPARİŞLERİM</a>
            </div>
            <div class="box-body">
                <div style="text-align: center; font-size: 18pt;" class="alert alert-danger"> SİPARİŞ NUMARANIZ : <?php echo $siparis_id = empty($siparis->id) ? 0 : $siparis->id; ?></div>
            </div>
            <div>
                <?php echo $odemeForm ?>
            </div>
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
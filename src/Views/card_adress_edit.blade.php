@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/css/acr_ftr/sepet.css">
@stop
@section('acr_ftr')
    <section class="content">
        <div class="row">
            <div class=" col-md-12">
                <div style="clear:both;"></div>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <?php echo $sepet_nav ?>

                        <button style="float: right;" data-toggle="modal" data-target="#myModal"
                                class="btn btn-success">YENİ ADRES EKLE
                        </button>
                    </div>
                    <div class="box-body">
                        <?php echo $adres_form ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="/plugins/iCheck/icheck.min.js"></script>
    <script>
        $('#city').change(function () {
            city_id = $(this).val();
            county_get(city_id);
        });

        function county_get(city_id, county_id) {
            $.ajax({
                type: 'post',
                url: '/acr/ftr/card/adress/county',
                data: 'city_id=' + city_id + '&county_id=' + county_id,
                success: function (veri) {
                    $('#county').html(veri);
                }
            });
        }

        $('.type_b').on('ifChecked', function (event) {
            $('#kurumsal').hide();
        });
        $('.type_k').on('ifChecked', function (event) {
            $('#kurumsal').show();
        });

    </script>
    @if(!empty($adress->city_id) || !empty(old('city_id')))
        <script>
            $(document).ready(function () {
                county_get({{!empty(old('city_id'))?old('city_id'):$adress->city_id}},{{!empty(old('county_id'))?old('county_id'):$adress->county_id}})
            })
        </script>
    @endif
@stop
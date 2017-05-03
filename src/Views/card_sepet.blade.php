@extends('acr_ftr.index')
@section('acr_ftr')
    <div class="box box-warning" style="width: 100%; right:0; top: 60px; position: absolute; z-index: 1; ">
        <div class="box-header with-border">Sepetiniz</div>
        <div class="box-body">
            <table width="100%" class="table table-striped">
                <thead>
                <tr>
                    <th width="40%">Ürün</th>
                    <th>Türü</th>
                    <th>Adet</th>
                    <th>Fiyat</th>
                    <th style="text-align: right">Sil</th>
                </tr>
                </thead>
                <tbody id="sepet_tbody"><?php echo $sepet_row; ?></tbody>
                <tfoot>
                <tr>
                    <td><a style="float: left;" class="btn btn-warning" href="/acr/ftr/card/">SATIN AL</a></td>

                    <td colspan="3">
                        <div style="font-size: 9pt; float: right; cursor:pointer;" onclick="sepet_delete_all()">Tümünü Sil</div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop
@section('footer')
    <script>
        function sepet_adet_guncelle(sepet_id) {

            var adet = $('#sepet_adet_' + sepet_id).val();
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/product/sepet/sepet_adet_guncelle',
                data   : 'sepet_id=' + sepet_id + '&adet=' + adet,
                success: function (veri) {
                    $('#sepet_count').html(veri);
                }
            });
        }
        function sepet_lisans_ay_guncelle(sepet_id) {

            var lisans_ay = $('#sepet_lisans_ay_' + sepet_id).val();
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/product/sepet/sepet_lisans_ay_guncelle',
                data   : 'sepet_id=' + sepet_id + '&lisans_ay=' + lisans_ay,
                success: function () {
                    $('#sepet_lisans_ay_tik_' + sepet_id).toggle(200);
                }
            });
        }

        function sepet_delete(sepet_id) {
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/product/sepet/delete',
                data   : 'sepet_id=' + sepet_id,
                success: function (veri) {
                    $('#sepet_count').html(veri);
                    $('#sapet_row_' + sepet_id).fadeOut(400);
                }
            });
        }
        function sepet_delete_all() {
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/product/sepet/delete_all',
                success: function (veri) {
                    $('#sepet_count').html(0);
                    $('.sepet_row').fadeOut(400);
                }
            });
        }
    </script>
@stop
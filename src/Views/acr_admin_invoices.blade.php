@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
@stop
@section('acr_ftr')
    <section class="content">
        <div class="row">
            <div class=" col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">Siparişler</div>
                    <div class="box-body">
                        <table width="100%" id="data_table" class="table table-striped">
                            <thead>
                            <tr>
                                <th>Son Güncelleme</th>
                                <th>SI. NO</th>
                                <th>Fatura İsmi</th>
                                <th>User_id</th>
                                <th>User</th>
                                <th>Ürünler</th>
                                <th>Fiyat</th>
                                <th>Oluşturma Tarihi</th>
                            </tr>


                            </thead>
                            <tbody id="sepet_tbody">
                            @foreach ($faturalar as $key=> $fatura)
                                <tr>
                                    <td>{{$fatura->updated_at}}</td>
                                    <td>{{$key+1}}</td>
                                    <td>{{$fatura->invoice_name}}</td>
                                    <td>{{$fatura->user->id}}</td>
                                    <td>{{$fatura->user->name}}<br>
                                        {{$fatura->user->$email}}<br>
                                        {{$fatura->user->tel}}</td>

                                    <td>
                                        @if(empty($fatura->cinsi))
                                            <table class="table">
                                                <tr>
                                                    <td>Adet</td>
                                                    <td>Fiyat</td>
                                                    <td>KDV</td>
                                                    <td>Toplam</td>
                                                </tr>
                                                @foreach ($fatura->products as $e_product)
                                                    <tr>
                                                        <td>{{$e_product->adet}}</td>
                                                        <td>{{$e_product->fiyat}}</td>
                                                        <td>{{$e_product->kdv}}</td>
                                                        <td>{{$e_product->toplam_fiyat  }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        @else
                                            {{$fatura->cinsi}}
                                        @endif
                                    </td>
                                    <td>{{$fatura->fiyat}}</td>
                                    <td>{{$fatura->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script>
        $('#data_table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "language": {
                "sProcessing": "İşleniyor...",
                "lengthMenu": "Sayfada _MENU_ satır gösteriliyor",
                "zeroRecords": "Gösterilecek sonuç yok.",
                "info": "Toplam _PAGES_ sayfadan _PAGE_. sayfa gösteriliyor",
                "infoEmpty": "Gösterilecek öğe yok",
                "infoFiltered": "(filtered from _MAX_ total records)",
                "search": "Arama yap",
                "oPaginate": {
                    "sFirst": "İlk",
                    "sPrevious": "Önceki",
                    "sNext": "Sonraki",
                    "sLast": "Son"
                }
            }
        });

        function sepet_adet_guncelle(sepet_id) {
            var adet = $('#sepet_adet_' + sepet_id).val();
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/sepet_adet_guncelle',
                data: 'sepet_id=' + sepet_id + '&adet=' + adet,
                success: function (veri) {
                    $('.sepet_count').html(veri);
                }
            });
        }

        function sepet_lisans_ay_guncelle(sepet_id) {

            var lisans_ay = $('#sepet_lisans_ay_' + sepet_id).val();
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sepet/sepet_lisans_ay_guncelle',
                data: 'sepet_id=' + sepet_id + '&lisans_ay=' + lisans_ay,
                success: function () {
                    $('#sepet_lisans_ay_tik_' + sepet_id).toggle(200);
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

        function order_active(id) {
            var order_id = id;
            if ($('#order_input_' + id).is(':checked')) {

                $.ajax({
                    type: 'post',
                    url: '/acr/ftr/order/active/admin',
                    data: 'order_id=' + order_id,
                    success: function () {
                    }
                });
            } else {
                $.ajax({
                    type: 'post',
                    url: '/acr/ftr/order/deactive',
                    data: 'order_id=' + order_id,
                    success: function () {
                    }
                });
            }
        }
    </script>
@stop
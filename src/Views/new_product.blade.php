@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/plugins/datatables/dataTables.bootstrap.css">
@stop
@section('acr_ftr')
    <section class="content">
        <div class="row">
            <div class="box box-success">
                <div class="box-header with-border">Paraşüt Fatura Bilgileri</div>
                <div class="box-body">
                    <div class=" col-md-12">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>İsim</th>
                                <th>Sıra</th>
                                <th>Kategori 1</th>
                                <th>Kategori 2</th>
                                <th>Kategori 3</th>
                                <th>Ekle</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($products as $product) {
                                echo $controller->product_row($product);
                            } ?>
                            </tbody>
                        </table>
                        <div id="search_div" class="">

                        </div>
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
        function product_sort_edit(product_id) {
            var sira = $('#product_sira_' + product_id).val();
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/sort/edit',
                data: 'sira=' + sira + '&product_id=' + product_id,
                success: function (veri) {
                }
            })
        }

        function product_search() {
            var search = $('#search').val();
            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/search_row',
                data: 'search=' + search,
                success: function (veri) {
                    $('#search_div').html(veri);
                }
            })
        }

        function add_product(id) {

            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/add',
                data: 'id=' + id,
                success: function (veri) {
                    $('#add_btn_' + id).html(veri);
                }
            })
        }

        function delete_product(id) {

            $.ajax({
                type: 'post',
                url: '/acr/ftr/product/delete',
                data: 'id=' + id,
                success: function (veri) {
                    $('#add_btn_' + id).html(veri);
                }
            })
        }

        $(function () {
            $("#example1").DataTable();
        })
    </script>
@stop
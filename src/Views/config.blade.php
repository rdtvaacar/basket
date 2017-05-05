@extends('acr_ftr.index')
@section('header')
    <link rel="stylesheet" href="/css/acr_ftr/sepet.css">
    <link rel="stylesheet" href="/plugins/iCheck/all.css">
@stop
@section('acr_ftr')
    <div class="col-md-6">
        <div class="box box-warning" style="width: 100%; right:0; top: 60px; position: absolute; z-index: 1; ">
            <div class="box-header with-border">BANKA BİLGİLERİ
                <button style="float: right;" data-toggle="modal" data-target="#myModal" class="btn btn-success">YENİ BANKA EKLE</button>
            </div>
            <div class="box-body">
                <?php
                echo csrf_field();
                foreach ($banks as $bank) {
                $checked = @$bank->active== 1 ? 'checked':'';
                ?>
                <div id="bank_div_{{$bank->id}}" style="width: 100%; cursor:pointer;" class="box-header with-border">
                    <label style="width:80%; cursor:pointer;">
                        <div style="float: left; " class="borderTd">
                            <input type="checkbox" name="bank_id" name="bank_id" value="<?php echo $bank->id ?>" class="flat-red" <?php echo $checked ?> style="position: absolute; opacity: 0;"></div>
                        <div style="float: left; width: 90%; margin-left: 20px;">
                            <div style="font-size: 14pt; float: left; width: 80%; "><?php echo $bank->name ?> - <span style="font-weight: 200;"><?php echo $bank->bank_name . '/' . $bank->user_name ?></span></div>


                        </div>
                    </label>
                    <div style="font-size: 16pt; float: right; width: 15%; ">
                        <span style="margin-left: 30px; cursor:pointer;" onclick="bank_edit(<?php echo $bank->id ?>)" class="fa fa-edit"></span>
                        <span style="margin-left: 30px; cursor:pointer;" onclick="bank_delete(<?php echo $bank->id ?>)" class="fa fa-trash"></span>
                    </div>
                </div>

                <?php }?>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <div id="myModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><img src="/icon/close48.png"/> </span></button>
                    <h4 class="modal-title">Yeni Banka Ekle</h4>
                </div>
                <div class="modal-body">
                    <div id="bank_form_div"><?php echo $bank_form ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">KAPAT</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
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
        function bank_edit(bank_id) {
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/bank/edit',
                data   : 'bank_id=' + bank_id,
                success: function (veri) {
                    $('#myModal').modal('show')
                    $('#bank_form_div').html(veri);
                }
            });
        }
        function bank_delete(bank_id) {
            if (confirm('Banka bilgilerini silmek istediğinizden eminmisiniz.') == true) {
                $.ajax({
                    type   : 'post',
                    url    : '/acr/ftr/bank/delete',
                    data   : 'bank_id=' + bank_id,
                    success: function () {
                        $('#bank_div_' + bank_id).fadeOut(400);
                    }
                });
            }
        }
        $('input').on('ifChecked', function (event) {
            var bank_id = $(this).val();
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/bank/active',
                data   : 'bank_id=' + bank_id,
                success: function () {

                }
            });

        });
        $('input').on('ifUnchecked', function (event) {
            var bank_id = $(this).val();
            $.ajax({
                type   : 'post',
                url    : '/acr/ftr/bank/deactive',
                data   : 'bank_id=' + bank_id,
                success: function () {

                }
            });

        });
    </script>
@stop
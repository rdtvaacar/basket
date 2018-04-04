<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-header with-border">{{$kat->kat_isim}}</div>
                <div class="box-body">
                    @foreach($kat->products as $product)
                        @if(!empty($product->my_product))
                            <div class="col-md-4">
                                <div style="width: 100%; text-align: center" class="img-thumbnail">
                                    <div class="box-header with-border">{{$product->product_name}}</div>
                                    <div style="margin-right: auto; margin-left: auto;" class="img-thumbnail">
                                        <img
                                                src="https://eticaret.webuldum.com/acr_files/{{@$product->file->acr_file_id}}/thumbnail//{{@$product->file->file_name}}.{{@$product->file->file_type}}"/>

                                    </div>
                                    <div style="clear:both;"></div>
                                    <div style="float: left; width: 100px; font-size: 22pt; padding: 2px; " class="alert alert-warning">{{$product->price}}<span style="font-size: 12pt;">₺</span></div>
                                    <a style="float: right" class="btn btn-success btn-lg" href="/acr/ftr/product/detail?product_id={{$product->id}}">Detay>></a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

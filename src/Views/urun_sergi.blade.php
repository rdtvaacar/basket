<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-header with-border">{{$kat->kat_isim}}</div>
                <div class="box-body">
                    @foreach($kat->products as $product)
                        <div class="img-thumbnail">
                            <div class="box-header with-border">{{$product->product_name}}</div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

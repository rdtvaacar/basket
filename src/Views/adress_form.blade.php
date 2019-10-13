<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h1 class="text-red" style="text-align: center;">ADRES BİLGİLERİ</h1>
        </div>
        <div class="box-body">
            <form action="/acr/ftr/card/adress/create" method="post">
                {{csrf_field()}}
                <div class="col-md-12">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="col-md-6 col-xs-12">
                    <label for="name">Adres Tanımı (İş adresi, Ev adresi vb)</label>
                    <input name="name" value="{{!empty(old('name'))?old('name'):@$adress->name}}" class="form-control"/>
                    <div style="clear:both;"></div>
                    <hr>
                    <label for="name">Alıcı Adı Soyadı</label>
                    <input name="invoice_name" value="@if(!empty(old('invoice_name'))){{old('invoice_name')}}@else{{!empty($adress->invoice_name)?@$adress->invoice_name:@$user->name}}@endif" class="form-control"/>
                    <div style="clear:both;"></div>
                    <hr>
                    <label for="name">T.C. Kimlik No (Zorunlu Değil)</label>
                    <input type="number" name="tc" value="@if(!empty(old('tc'))){{old('tc')}}@else{{!empty($adress->tc)?@$adress->tc:@$user->TC}}@endif" class="form-control"/>
                    <div style="clear:both;"></div>
                    <hr>
                    <label for="adress">Adres</label>
                    <textarea class="form-control" name="adress">@if(!empty(old('adress'))){{old('adress')}}@else{{!empty($adress->adress)?@$adress->adress:''}}@endif</textarea>
                </div>
                <div class="col-md-6 col-xs-12">
                    <label for="city_id">Şehir</label>
                    <select class="form-control" id="city" name="city_id">
                        <option value="0">SEÇİNİZ</option>
                        @if(!empty($cities))
                            @foreach($cities as $item)
                                <option {{!empty(old('city_id'))?old('city_id')==$item->id?'selected':'':@$adress->city_id==$item->id?'selected':''}} value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        @endif

                    </select>
                    <div style="clear:both;"></div>
                    <hr>
                    <label for="city_id">İlçe</label>
                    <div id="county">
                        <select class="form-control">
                            <option>ÖNCE ŞEHİR SEÇİNİZ</option>
                        </select>
                    </div>
                    <div style="clear:both;"></div>
                    <hr>
                    <label for="tel">Telefon</label>
                    <input name="tel" value="@if(!empty(old('tel'))){{old('tel')}}@else{{!empty($adress->tel)?@$adress->tel:@$user->tel}}@endif" class="form-control"/>
                    <hr>
                    <label for="name">Adres Tarifi (Gerekliyse)</label>
                    <textarea class="form-control" name="adres_tarifi">@if(!empty(old('adres_tarifi'))){{old('adres_tarifi')}}@else{{!empty($adress->adres_tarifi)?@$adress->adres_tarifi:''}}@endif</textarea>
                </div>
                <div style="clear:both;"></div>

                <div class="col-md-12">

                    <hr>
                    <button class="btn btn-primary btn-block btn-lg">ADRES BİLGİLERİNİ KAYDET</button>
                </div>
                <input name="page" id="page" class="form-control" value="odeme" type="hidden"/>
            </form>
        </div>

    </div>
</div>
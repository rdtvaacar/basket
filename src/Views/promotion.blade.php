@extends('acr_ftr.index')
@section('acr_ftr')
    <section class="content">
        <div class="row">
            <div class=" col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">PROMOSYONLARINIZ</div>
                    <div class="box-body">
                        <table class="table table-hover">
                            <tr>
                                <th>P.Kodu</th>
                                <th>Ürün</th>
                                <th>Oluşturma Tarihi</th>
                                <th>Durumu</th>
                            </tr>
                            @foreach ($prs as $pr)
                                <tr>
                                    <td>{{$pr->code}}</td>
                                    <td>{{$pr->ps->product->product_name}}</td>
                                    <td>{{$pr->created_at}}</td>
                                    <td>{!! $pr->active==1?'<span class="text-success">AKTİF</span>':'<span class="text-danger">KULLANILDI</span>' !!}</td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>
            </div>
            <div class=" col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">PROMOSYON KODU KULLAN</div>
                    <div class="box-body">
                        <form method="post" action="/acr/ftr/promotion/code/active">
                            {{csrf_field()}}
                            <label>Pormosyon Kodunuz</label>
                            <input name="code" value="{{@$code}}" class="form-control" style="font-size: large; padding: 10px;"/>
                            <button type="submit" class="btn btn-primary btn-block">PROMOSYON KODUNU AKTİF ET</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

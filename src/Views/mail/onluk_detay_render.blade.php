<table class="table table-bordered" width="100%">
    <tr>
        <th>
            Alıcı
        </th>
        <td>
            {{$sepet_row->adress->invoice_name}}
        </td>
        <th>
            Telefon
        </th>
        <td>
            {{$sepet_row->adress->tel}}
        </td>
    </tr>
    <tr>
        <th>
            Ürün
        </th>
        <td>
            {{$product->product->product_name}}
        </td>
        <th>
            Adres Tanımı
        </th>
        <td>
            {{$sepet_row->adress->name}}
        </td>
    </tr>

    <tr>
        <th colspan="4">
            Ürün Detayları
        </th>
    </tr>
    <tr>
        <th>
            Beden
        </th>
        <td colspan="3">
            {{$product->size->name}}
        </td>

    </tr>

    <tr>
        <th>
            Yaka
        </th>
        <td>
            {{$product->yaka->name}}
        </td>
        <th>
            Notlar
        </th>
        <td>
            @if (!empty($product->notes))
                @foreach ($product->notes as $note)
                    {{$note->note->name . ':' . @$note->name . '<br>'}}
                @endforeach
            @endif
        </td>
    </tr>

    <tr>
        <th>
            Alacak Kişi
        </th>
        <td>
            {{$sepet_row->adress->name}}
        </td>
        <th>
            Adres
        </th>
        <td>
            {{$sepet_row->adress->adress}}
        </td>
    </tr>


    <tr>
        <th>
            T.C.
        </th>
        <td>
            {{$sepet_row->adress->tc}}
        </td>
        <th>
            Şirket
        </th>
        <td>
            {{$sepet_row->adress->company}}
        </td>
    </tr>

    <tr>
        <th>
            Vergi Dairesi
        </th>
        <td>
            {{$sepet_row->adress->tax_number}}
        </td>
        <th>
            Vergi Numarası
        </th>
        <td>
            {{$sepet_row->adress->tax_office}}
        </td>
    </tr>

    <tr>
        <th>
            Şehir
        </th>
        <td>
            {{$sepet_row->adress->city->name}}
        </td>
        <th>
            İlçe
        </th>
        <td>
            {{$sepet_row->adress->county->name}}
        </td>
    </tr>
</table>
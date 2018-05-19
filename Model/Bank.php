<?php

namespace Acr\Ftr\Model;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model

{
    protected $connection = 'mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */

    function create($bank_id = null, $data)
    {

        if (empty($bank_id)) {
            return Bank::insertGetId($data);
        } else {
            Bank::where('id', $bank_id)->update($data);
            return $bank_id;
        }

    }


}

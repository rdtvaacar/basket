<?php

namespace Acr\Ftr\Model;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Promotion_user extends Model

{
    protected $connection = 'mysql';
    protected $table      = 'promotion_user';

    function ps()
    {
        return $this->hasOne('Acr\Ftr\Model\Product_sepet', 'id', 'ps_id');
    }

    function promosyon($ps, $user_id)
    {
        $pr_model = new Promotion_user();
        if ($ps->adet > 1) {
            for ($i = 1; $i < $ps->adet; $i++) {
                $data[] = [
                    'user_id' => $user_id,
                    'ps_id'   => $ps->id,
                    'code'    => uniqid(rand(100000, 999999))
                ];
            }
        }
        if (!empty($data)) {
            $pr_model->insert($data);
        }
    }

}

<?php

namespace Acr\Ftr\Controllers;


class FitController
{
    function connect()
    {
        $options = [
            'login'    => 'Hb2iphtC',
            'password' => 'HC%GKmP4'
        ];
        $soap    = new \SoapClient('https://earsivwstest.fitbulut.com/ClientEArsivServicesPort.svc', $options);
        $soap->
        dd($soap);
    }
}
<?php


namespace App\Input;


class AddressSaveInput extends Input
{
    public $id;
    public $name;
    public $addressDetail;
    public $city;
    public $country;
    public $county;
    public $isDefault;
    public $areaCode;
    public $postalCode = '';
    public $tel;
    public $province;


    public function rule()
    {
        return [
            'id'            => 'integer',
            'name'          => 'required | string',
            'addressDetail' => 'required | string',
            'city'          => 'required | string',
            'county'        => 'required | string',
            'isDefault'     => 'bool',
            'tel'           => 'regex:/^1[0-9]{10}$/',
            'province'      => 'required | string',
        ];
    }

}

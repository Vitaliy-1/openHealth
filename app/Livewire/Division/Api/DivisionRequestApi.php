<?php

namespace App\Livewire\Division\Api;

use App\Classes\eHealth\Api\DivisionApi;

class DivisionRequestApi
{

    public function getDivisionRequest($params = []):array
    {
        return (new DivisionApi())->_get($params);
    }

    public  function createDivisionRequest($data):array
    {
        return (new DivisionApi())->_create($data);
    }


    public function updateDivisionRequest($id, $data):array
    {
        $division = (new DivisionApi())->_update($id, $data);
        dd($division);
        return $division;
    }

    public function deactiveDivisionRequest($id):array
    {
        $division = (new DivisionApi())->_deactive($id);
        dd($division);
        return $division;
    }

    public function activateDivisionRequest($id):array
    {
        $division = (new DivisionApi())->_activate($id);
        dd($division);
        return $division;
    }




}

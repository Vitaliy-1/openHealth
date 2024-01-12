<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AjaxController extends Controller
{

    private  ?array $request;
    protected  string $action;

    public function __construct(Request $request)
    {
        $this->action = $request->input('action');
        $this->request = $request->all();

    }

    public function  handleRequest()
    {
        $data = $this->action($this->action,$this->request);

        dd($data);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    private function action($action, $request)
    {
        if (function_exists($this->$action($request))){
            return $this->$action($request);
        }
        abort(404);
    }

    public function koatauuLvl1(){

        dd($this->request);
    }

}

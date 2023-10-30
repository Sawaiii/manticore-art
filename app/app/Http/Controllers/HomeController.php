<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Razdel;
use Illuminate\Http\Request;
use Manticoresearch\Client;

class HomeController extends Controller
{


    public function index(Request $request)
    {
        $catalogItems = [];
        if (!empty($request->text)
        ) {
            $config = ['host' => 'manticore', 'port' => 9308];
            $client = new Client($config);
            $index = $client->index('prt_catalog');

            $searchData =  $index->search($request->text)->sort('Name', 'desc')->sort('ID','desc')->limit(100)->get();

            $ids = [];
            $poryadok = [];
            
            foreach ($searchData as $key =>  $searchDataItem) {
                $ids[] = $searchDataItem->getId();
                $poryadok[$searchDataItem->getId()] =  $key;
            }

            $catalogItems = Catalog::whereIn("id" , $ids)->get();

            $bimbam = [];
            foreach ($catalogItems as $item) {
                $bimbam[$poryadok[$item->"id"]] = $item;
            }

        return view('welcome',  [ "catalog" => $bimbam ] );
    }
}
}    

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

            $searchData = $index->search($request->text)->limit(50)->get();

            $ids = [];

            foreach ($searchData as $searchDataItem) {
                $ids[] = $searchDataItem->getId();
            }
        $catalogItems = Catalog::whereIn("id" , $ids)->get();
        }
        return view('welcome', [ "catalog" => $catalogItems] );
}
}

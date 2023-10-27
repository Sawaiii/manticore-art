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

    if (!empty($request->text)) {
        $config = ['host' => 'manticore', 'port' => 9308];
        $client = new Client($config);
        $index = $client->index('prt_catalog');

        $searchData = $index->search($request->text)->limit(100)->get();
        $ids = [];

        foreach ($searchData as $searchDataItem) {
            $ids[] = $searchDataItem->getId();
        }

        $catalogItems = Catalog::whereIn("id", $ids)->get();

      
        $catalogItems = $catalogItems->sort(function ($a, $b) use ($request) {
            $aFirstWord = strtok($a->Name, ' '); 
            $bFirstWord = strtok($b->Name, ' ');

            $aFirstWordMatch = strpos($aFirstWord, $request->text) === 0;
            $bFirstWordMatch = strpos($bFirstWord, $request->text) === 0;

            if ($aFirstWordMatch && !$bFirstWordMatch) {
                return -1;
            } elseif (!$aFirstWordMatch && $bFirstWordMatch) {
                return 1;
            }

            return 0;
        })->values()->all();
    }

    return view('welcome',  [ "catalog" => $catalogItems] );
}
}

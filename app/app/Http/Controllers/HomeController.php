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

        // Сортировка на основе соответствия первому слову
        $catalogItems = $catalogItems->sort(function ($a, $b) use ($request) {
            $aFirstWord = strtok($a->Name, ' '); // Замените 'your_text_column' на имя столбца, содержащего текст
            $bFirstWord = strtok($b->Name, ' ');

            if (starts_with($aFirstWord, $request->text) && !starts_with($bFirstWord, $request->text)) {
                return -1;
            } elseif (!starts_with($aFirstWord, $request->text) && starts_with($bFirstWord, $request->text)) {
                return 1;
            }

            return 0;
        })->values()->all();
    }

    return view('welcome',  [ "catalog" => $catalogItems] );
}
}

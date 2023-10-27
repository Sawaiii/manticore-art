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

        // Получить объекты Catalog
        $catalogItems = Catalog::whereIn("id", $ids)->get();

        // Сортировать результаты в PHP
        usort($catalogItems, function($a, $b) use ($request) {
    // Получите релевантность каждого элемента по введенному слову
    $relevanceA = $a->getRelevance($request->text);
    $relevanceB = $b->getRelevance($request->text);

    // Сравните релевантность элементов
    if ($relevanceA === $relevanceB) {
        return 0;
    }

    // Чем выше релевантность, тем ближе элемент к введенному слову
    return ($relevanceA > $relevanceB) ? -1 : 1;
});
    }

    return view('welcome', ["catalog" => $catalogItems]);
}
}

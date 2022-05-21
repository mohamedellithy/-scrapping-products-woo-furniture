<?php

namespace App\Http\Controllers;
use App\Product;
use App\FurnitureFetchApi\LaunchScrappingScript;
use App\ProductAttribute;
class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function testDb(){
        //$delete = ProductAttribute::truncate();
        // get all products
        dd(new LaunchScrappingScript());

        // here for fetch attributes
        // .$inserted_attributes = new InsertAttributes();
        // .$inserted_attributes->insert_attributes_general($products);

        // here for fetch single product
        // .$filtered_data       = new FetchSingleProduct($products);
        // .dd($filtered_data);

        // -- $products            = new FetchProducts();
        // -- $filtered_data       = new FetchSingleProduct($products);
        // -- dd($filtered_data);
    }
}

<?php
namespace App\FurnitureFetchApi\IkeaApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\IkeaApi\FilterFetchedProduct;
use App\FurnitureFetchApi\IkeaApi\InsertParentProduct;
class FetchSingleProduct extends HandleFurnitureRequest {
    public $endpoint = "";
    protected $products;
    public function __construct(FetchProducts $products){
        $this->set_time_out_for_long_resuest();
        // here fetch data
        $this->products  = $products->get_slugs();

        // fetch data from single product
        if(method_exists($this,'start_fetch_data')){

            // here start fetch data
            $this->start_fetch_data($products);
        }
    }

    public function start_fetch_data(FetchProducts $products){
        $results = [];

        // fetch data from single product
        if(method_exists($this,'loop_fetch_products_rounds')){

            // start first 20 products
            $this->loop_fetch_products_rounds();

            // rest of api caling fetch
            $paginate_count = $products->get_page_info() / 20;

            $start = 0; // start from 
            $end   = 0; // end at
            // for loop on round paginate
            for($round = 0; $round <= $paginate_count; $round++){
                $start += 20;
                $end    = $start+20;
               // set endpont to fetch api
                $this->endpoint = "https://sik.search.blue.cdtapps.com/ae/en/special/more-products?special=all&start={$start}&end={$end}&subcategories-style=tree-navigation&sort=RELEVANCE";

                // start resolve products
                $this->resolve_api($json= false);

                // set products from resolve
                $this->products =  $this->results['products']['specialPage']['productWindow'];

                // start first 20 products
                $this->loop_fetch_products_rounds();
            }

            // return results
            return $this->results;
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all products
        foreach($this->products as $product){

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($product);

            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);

            $this->results[$product['name']] = $product_filtered;
            // break;
        }
    }
}

<?php
namespace App\FurnitureFetchApi\EbarzaApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\EbarzaApi\FilterFetchedProduct;
use App\FurnitureFetchApi\EbarzaApi\InsertParentProduct;
class FetchSingleProduct extends HandleFurnitureRequest {
    public $endpoint = "";
    protected $slugs;
    public function __construct(FetchProducts $products){
        $this->set_time_out_for_long_resuest();
        // here fetch data
        $this->slugs  = $products->get_slugs();

        // fetch data from single product
        if(method_exists($this,'start_fetch_data')){

            // here start fetch data
            $this->start_fetch_data();
        }
    }

    public function start_fetch_data(){
        $results = [];

        // fetch data from single product
        if(method_exists($this,'loop_fetch_products_rounds')){

            // loop for each product
            $this->loop_fetch_products_rounds();
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all slugs
        foreach($this->slugs as $slug){

            //$slug = 'modern-carpet-rug-rhapsody-11729-u01';

            // access endpoint
            $this->endpoint = "https://www.ebarza.com/products/".$slug.".js";

            // start fetch data in single product
            $this->resolve_api($json = true,$params = 'single product');

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($this->results['single product']);

            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);

            $this->results[$slug] = $product_filtered;
            // *break;
        }
    }
}

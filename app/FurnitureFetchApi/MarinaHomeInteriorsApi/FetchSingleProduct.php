<?php
namespace App\FurnitureFetchApi\MarinaHomeInteriorsApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\MarinaHomeInteriorsApi\FilterFetchedProduct;
use App\FurnitureFetchApi\MarinaHomeInteriorsApi\InsertParentProduct;
class FetchSingleProduct extends HandleFurnitureRequest {
    public $endpoint;
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

    public function start_fetch_data($products){
        $results = [];

        // fetch data from single product
        if(method_exists($this,'loop_fetch_products_rounds')){
            // fetch products first round
            $this->loop_fetch_products_rounds();

            // rest of api caling fetch
            $paginate_count = $products->get_page_info() / 300;

            // for loop on round paginate
            for($round = 2; $round <= $paginate_count; $round++){
                // set endpont to fetch api
                $this->endpoint =  "https://www.marinahomeinteriors.com/rest/V1/products?searchCriteria[pageSize]=300&searchCriteria[currentPage]={$round}";

                // start resolve products
                $this->resolve_api();

                // set products from resolve
                $this->products =  $this->results['products']['items'] ?? [];

                // start first 20 products
                $this->loop_fetch_products_rounds();
            }

            //.$this->results = $results;
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all slugs
        foreach($this->products as $product){

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($product);

            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);

            $this->results    = $insert_new_item->result;
        }
    }

}

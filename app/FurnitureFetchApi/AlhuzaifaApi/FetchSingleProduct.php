<?php
namespace App\FurnitureFetchApi\AlhuzaifaApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\AlhuzaifaApi\FilterFetchedProduct;
use App\FurnitureFetchApi\AlhuzaifaApi\InsertParentProduct;
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
            $paginate_count = $products->get_page_info();

            // for loop on round paginate
            for($round = 2; $round <= $paginate_count; $round++){
                // set endpont to fetch api
                $this->endpoint = "https://alhuzaifa.com/wp-json/wp/v2/product?per_page=30&page={$round}";

                // start resolve products
                $this->resolve_api();

                // set products from resolve
                $this->products =  $this->results['products'];

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
            // set product type simple
            $product['type_product'] = 'simple';

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($product);


            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);

            $this->results[$product['title']['rendered']] = $product_filtered;
            // .break;
        }
    }
}

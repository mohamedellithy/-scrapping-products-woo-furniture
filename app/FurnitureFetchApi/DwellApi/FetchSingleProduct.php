<?php
namespace App\FurnitureFetchApi\DwellApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\DwellApi\FilterFetchedProduct;
use App\FurnitureFetchApi\DwellApi\InsertParentProduct;
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

        // fetch data from single product
        if(method_exists($this,'loop_fetch_products_rounds')){

            // start first category products
            $this->loop_fetch_products_rounds();

            // return results
            return $this->results;
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all products
        foreach($this->products['slugs'] as $product){
            // set endpont to fetch api
            $this->endpoint = "https://dwellstores.ae/collections/{$this->products['category']}/products/{$product}.js";

            // start fetch data in single product
            $this->resolve_api($json=true,$params = "single product");

            // handle single product details
            $single_product = $this->results['single product'] ?? [];

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($single_product);


            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);

            $this->results[] = $product_filtered; // $insert_new_item->result['variate'][1]->attributes;
            //*break;
        }
    }
}

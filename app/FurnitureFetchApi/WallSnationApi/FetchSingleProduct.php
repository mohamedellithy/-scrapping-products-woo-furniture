<?php
namespace App\FurnitureFetchApi\WallSnationApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\WallSnationApi\FilterFetchedProduct;
use App\FurnitureFetchApi\WallSnationApi\InsertParentProduct;
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

            // rest of api caling fetch
            $others_categories = $products->get_page_info();

            // for loop on other categories
            foreach($others_categories as $category){
                // set endpont to fetch api
                $this->endpoint = "https://www.wallsnation.com/collections/{$category}.oembed";

                // start resolve products
                $this->resolve_api();

                // set products from resolve
                $this->products =  ["category" => str_replace(" ","-",strtolower($category)),
                                      "slugs"    => collect($this->results['products']['products'])->pluck('product_id')->toArray()];

                // start fetch products
                $this->loop_fetch_products_rounds();
            }

            // return results
            return $this->results;
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all products
        foreach($this->products['slugs'] as $product){
            // set endpont to fetch api
            $this->endpoint = "https://www.wallsnation.com/collections/{$this->products['category']}/products/{$product}.js";

            // start fetch data in single product
            $this->resolve_api($json=true,$params = "single product");

            // handle single product details
            $single_product = $this->results['single product'] ?? [];

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($single_product);


            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);

            $this->results[$single_product['title']] = $product_filtered; // $insert_new_item->result['variate'][1]->attributes;
            //break;
        }
    }
}

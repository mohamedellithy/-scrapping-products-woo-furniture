<?php
namespace App\FurnitureFetchApi\DanubeHomeApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\DanubeHomeApi\FilterFetchedProduct;
use App\FurnitureFetchApi\DanubeHomeApi\InsertParentProduct;
use Log;
class FetchSingleProduct extends HandleFurnitureRequest {
    public $endpoint = "https://danubehome.com/ae/en/graphql?hash=3576528246";
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

            // start first 20 products
            $this->loop_fetch_products_rounds();

            // rest of api caling fetch
            $paginate_count = $products->get_page_info();

            // for loop on round paginate
            for($round = 2; $round <= $paginate_count; $round++){

                 // set endpont to fetch api
                 $this->endpoint = "https://danubehome.com/ae/en/graphql?hash=621218113&_sort_0={%22custom_sort%22:[{%22attribute%22:%22position%22,%22direction%22:%22ASC%22}]}&_filter_0={price_range:{eq:null},custom_filter:[]}&_pageSize_0=40&_currentPage_0={$round}";

                 // start resolve products
                 $this->resolve_api();

                 // set products from resolve
                 $this->products =  collect($this->results['products']['data']['products']['items'])->pluck('url_key')->toArray();

                 // start first 20 products
                 $this->loop_fetch_products_rounds();
            }

            // return results
            return $this->results;
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all products
        foreach($this->products as $slug):
            try{

                // $slug = 'modern-carpet-rug-rhapsody-11729-u01';

                // access endpoint
                $this->endpoint = "https://danubehome.com/ae/en/graphql?hash=3576528246&_filter_0={url_key:{eq:".$slug."}}";

                // start fetch data in single product
                $this->resolve_api();

                // start filter data fetched from single product
                $product_filtered = new FilterFetchedProduct($this->results);

                // try to insert filtered data
                $insert_new_item  = new InsertParentProduct($product_filtered);

                $this->results[$slug] = $product_filtered;

                // break;

            }catch(\Exception $e){
                Log::error("Error In fetch product :".json_encode($slug));
            }
        endforeach;
    }

}

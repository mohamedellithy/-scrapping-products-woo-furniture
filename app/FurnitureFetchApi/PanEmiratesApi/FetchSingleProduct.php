<?php
namespace App\FurnitureFetchApi\PanEmiratesApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\PanEmiratesApi\FilterFetchedProduct;
use App\FurnitureFetchApi\PanEmiratesApi\InsertParentProduct;
use Symfony\Component\DomCrawler\Crawler;
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
            $pages = $products->get_page_info();

            // for loop on other categories
            for($paginate = 2 ;$paginate<= $pages;$paginate++){
                // set endpont to fetch api
                $this->endpoint = "https://www.panemirates.com/uae/en/rss/1/productlistings?page={$paginate}";

                // start resolve products
                $this->resolve_api($json= false);

                // load xml and parse it to json
                $this->results['products'] =  simplexml_load_string($this->results['products']);
                $this->results['products'] =  json_encode($this->results['products']);
                $this->results['products'] =  json_decode($this->results['products'],true);

                // get slugs from products json
                if(!empty($this->results['products']['channel']['item'])):
                    $this->products = collect($this->results['products']['channel']['item'])->pluck('guid')->toArray();
                   // start fetch products
                    $this->loop_fetch_products_rounds();
                endif;
            }

            // return results
            return $this->results;
        }
    }

    public function loop_fetch_products_rounds(){
        // foreach all products
        foreach($this->products as $product){
            // set endpont to fetch api
            $this->endpoint = "https://www.panemirates.com/uae/en{$product}";

            // start fetch data in single product
            $this->resolve_api($json=false,$params = "single product");

            // start fetch some data from crawler documents
            $crawler = new Crawler($this->results['single product'] ?? []);

            // get on product info by details from crawler
            $fetched_product = json_decode($crawler->filter('amp-state#product')->text(null),true);

            // start filter data fetched from single product
            $product_filtered = new FilterFetchedProduct($fetched_product);


            // try to insert filtered data
            $insert_new_item  = new InsertParentProduct($product_filtered);
            $this->results['single product'] = null;

            $this->results[$product] = $product_filtered; // $insert_new_item->result['variate'][1]->attributes;
            // *break;
        }
    }

}

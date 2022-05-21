<?php
namespace App\FurnitureFetchApi\HomeBoxApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\HomeBoxApi\FilterFetchedProduct;
use App\FurnitureFetchApi\HomeBoxApi\InsertParentProduct;
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
            for($round = 1; $round <= $paginate_count; $round++){
                // set endpont to fetch api
                $this->endpoint = $products->endpoint;

                // reset request body
                $body_json = '{"requests":[{"indexName":"prod_uae_homebox_Product_by_asc_BSR","params":"query=*&hitsPerPage=36&page='.$round.'&facets=*&facetFilters=%5B%22inStock%3A1%22%2C%22approvalStatus%3A1%22%2C%22allCategories%22%2C%22badge.title.en%3A-LASTCHANCE%22%5D&getRankingInfo=1&clickAnalytics=true&attributesToHighlight=null&analyticsTags=%5B%22hbxbedroom-bedsandbedsets%22%2C%22en%22%2C%22Desktop%22%5D&attributesToRetrieve=concept%2CmanufacturerName%2Curl%2C333WX493H%2C345WX345H%2C505WX316H%2C550WX550H%2C499WX739H%2Cbadge%2Cname%2Csummary%2CwasPrice%2Cprice%2CemployeePrice%2CshowMoreColor%2CproductType%2CchildDetail%2Csibiling%2CthumbnailImg%2CgallaryImages%2CisConceptDelivery%2CextProdType%2CreviewAvgRating%2CreferencesAvailable%2CitemType%2CbaseProductId&numericFilters=price%20%3E%201&query=*&maxValuesPerFacet=500&tagFilters=%5B%5B%22homebox%22%5D%5D"}]}';

                // start resolve products
                $this->resolve_api($json = true,$params = "products",$type = "post",$body = $body_json);

                // set products from resolve
                $this->products =  $this->results['products']['results'][0]['hits'];

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

            $this->results[$product['name']['en']] = $product_filtered;
            // .break;
        }
    }

}

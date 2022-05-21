<?php
namespace App\FurnitureFetchApi\ACEApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\ACEApi\FilterFetchedProduct;
use App\FurnitureFetchApi\ACEApi\InsertParentProduct;
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
        if(method_exists($this,'FetchSingleDataProduct')){

            // start first 20 products
            $this->loop_fetch_products_rounds();

            // rest of api caling fetch
            $paginate_count = $products->get_page_info();

            // for loop on round paginate
            for($round = 1; $round <= $paginate_count; $round++){
                // set endpont to fetch api
                $this->endpoint = $products->endpoint;

                // reset request body
                $body_json = '{"requests":[{"indexName":"mozanta_ace_product_ae","params":"highlightPreTag=%3Cais-highlight-0000000000%3E&highlightPostTag=%3C%2Fais-highlight-0000000000%3E&clickAnalytics=true&analyticsTags=%5B%22desktop%22%2C%22anonymous%22%2C%22ae%22%2C%22en%22%5D&userToken=0&query=&page='.$round.'&maxValuesPerFacet=30&hitsPerPage=24&facets=%5B%22brand.name.en%22%2C%22pricing.sale_price%22%2C%22categories.en.lvl0%22%2C%22categories.en.lvl1%22%2C%22categories.en.lvl2%22%5D&tagFilters=&facetFilters=%5B%5B%22categories.en.lvl1%3AHomeware%20%26%20Furniture%20%3E%20Home%20Furniture%22%5D%5D"},{"indexName":"mozanta_ace_product_ae","params":"highlightPreTag=%3Cais-highlight-0000000000%3E&highlightPostTag=%3C%2Fais-highlight-0000000000%3E&clickAnalytics=false&analyticsTags=%5B%22desktop%22%2C%22anonymous%22%2C%22ae%22%2C%22en%22%5D&userToken=0&query=&page=0&maxValuesPerFacet=30&hitsPerPage=1&attributesToRetrieve=%5B%5D&attributesToHighlight=%5B%5D&attributesToSnippet=%5B%5D&tagFilters=&analytics=false&facets=%5B%22categories.en.lvl0%22%2C%22categories.en.lvl1%22%5D"},{"indexName":"mozanta_ace_product_ae","params":"highlightPreTag=%3Cais-highlight-0000000000%3E&highlightPostTag=%3C%2Fais-highlight-0000000000%3E&clickAnalytics=false&analyticsTags=%5B%22desktop%22%2C%22anonymous%22%2C%22ae%22%2C%22en%22%5D&userToken=0&query=&page=0&maxValuesPerFacet=30&hitsPerPage=1&attributesToRetrieve=%5B%5D&attributesToHighlight=%5B%5D&attributesToSnippet=%5B%5D&tagFilters=&analytics=false&facets=%5B%22categories.en.lvl2%22%5D"}]}';
        
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
            //*break;
        }
    }

}

<?php
namespace App\FurnitureFetchApi\MujjApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\MujjApi\FilterFetchedProduct;
use App\FurnitureFetchApi\MujjApi\InsertParentProduct;
use Log;
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
                $body_json = '{"requests":[{"indexName":"01live_muae_product_list","params":"query=&hitsPerPage=36&maxValuesPerFacet=NaN&page='.$round.'&highlightPreTag=%3Cais-highlight-0000000000%3E&highlightPostTag=%3C%2Fais-highlight-0000000000%3E&clickAnalytics=true&filters=(field_category_name.en.lvl0%3A%20%22Furniture%22)&ruleContexts=%5B%22furniture%22%5D&optionalFilters=null&facets=%5B%22final_price.en%22%2C%22attr_rkj_division.en%22%2C%22attr_sel_class.en%22%2C%22attr_sel_depa.en%22%2C%22attr_size.en%22%2C%22attr_line.en%22%2C%22attr_gender.en%22%2C%22attr_product_collection.en%22%2C%22attr_color_family.en.value%22%2C%22attr_product_brand.en%22%2C%22field_acq_promotion_label.en.web%22%2C%22field_category.en%22%5D&tagFilters="}]}';

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
            try{
                // start fetch data in single product
                $this->FetchSingleDataProduct();

                // start filter data fetched from single product
                $product_filtered = new FilterFetchedProduct($product);

                // try to insert filtered data
                $insert_new_item  = new InsertParentProduct($product_filtered);

                $this->results[$product['title']['en']] = $product_filtered;
                // break;
            }catch(\Exception $e){ Log::error("Error In fetch product :".json_encode($product)); }
        }
    }

    public function FetchSingleDataProduct(){
        return null;
    }

}

<?php

namespace App\FurnitureFetchApi\MujjApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://iba79mx5ck-dsn.algolia.net/1/indexes/*/queries?x-algolia-agent=Algolia%20for%20JavaScript%20(3.35.1)%3B%20Browser%20(lite)%3B%20react%20(16.9.0)%3B%20react-instantsearch%20(5.7.0)%3B%20JS%20Helper%20(2.28.0)&x-algolia-application-id=IBA79MX5CK&x-algolia-api-key=ef32e72341e03c30f9d18405f046f8ee";
    public function __construct(){
        $body_json = '{"requests":[{"indexName":"01live_muae_product_list","params":"query=&hitsPerPage=36&maxValuesPerFacet=NaN&page=0&highlightPreTag=%3Cais-highlight-0000000000%3E&highlightPostTag=%3C%2Fais-highlight-0000000000%3E&clickAnalytics=true&filters=(field_category_name.en.lvl0%3A%20%22Furniture%22)&ruleContexts=%5B%22furniture%22%5D&optionalFilters=null&facets=%5B%22final_price.en%22%2C%22attr_rkj_division.en%22%2C%22attr_sel_class.en%22%2C%22attr_sel_depa.en%22%2C%22attr_size.en%22%2C%22attr_line.en%22%2C%22attr_gender.en%22%2C%22attr_product_collection.en%22%2C%22attr_color_family.en.value%22%2C%22attr_product_brand.en%22%2C%22field_acq_promotion_label.en.web%22%2C%22field_category.en%22%5D&tagFilters="}]}';
        $this->resolve_api($json = true,$params = "products",$type = "post",$body = $body_json);
    }

    public function get_page_info(){
        return $this->results ? $this->results['products']['results'][0]['nbPages'] : [];
    }

    public function get_attributes(){
        return $this->results ? $this->results['products']['data']['products']['attributes'] : [];
    }

    public function get_slugs(){
        $slugs = $this->results ? $this->results['products']['results'][0]['hits'] : [];
        return $slugs;
    }
}

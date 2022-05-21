<?php

namespace App\FurnitureFetchApi\WestElmApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://vi9szej7v1-dsn.algolia.net/1/indexes/*/queries?x-algolia-agent=Algolia%20for%20JavaScript%20(3.35.1)%3B%20Browser%20(lite)%3B%20react%20(16.9.0)%3B%20react-instantsearch%20(5.7.0)%3B%20JS%20Helper%20(2.28.0)&x-algolia-application-id=VI9SZEJ7V1&x-algolia-api-key=4101596c0652e11ea78e310e06b44d5b";
    public function __construct(){
        $body_json = '{"requests":[{"indexName":"01live_weae_product_list","params":"query=&hitsPerPage=36&maxValuesPerFacet=NaN&page=0&highlightPreTag=%3Cais-highlight-0000000000%3E&highlightPostTag=%3C%2Fais-highlight-0000000000%3E&clickAnalytics=true&filters=(stock%20%3E%200)&optionalFilters=null&facets=%5B%22final_price.en%22%2C%22field_acq_promotion_label.en.web%22%2C%22attr_product_brand.en%22%2C%22attr_product_collection.en%22%2C%22attr_color_lhn.en.value%22%2C%22attr_feature_lhn.en%22%2C%22attr_finish_lhn.en%22%2C%22attr_furniturefinish_lhn.en%22%2C%22attr_material_lhn.en%22%2C%22attr_product_type_lhn.en%22%2C%22attr_size_lhn.en%22%2C%22attr_style_lhn.en%22%2C%22field_category.en%22%5D&tagFilters="}]}';
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

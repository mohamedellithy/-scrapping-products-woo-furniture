<?php

namespace App\FurnitureFetchApi\HomeBoxApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://3hwowx4270-1.algolianet.com/1/indexes/*/queries?X-Algolia-API-Key=4c4f62629d66d4e9463ddb94b9217afb&X-Algolia-Application-Id=3HWOWX4270&X-Algolia-Agent=Algolia%20for%20vanilla%20JavaScript%202.9.7";
    public function __construct(){
        $body_json = '{"requests":[{"indexName":"prod_uae_homebox_Product_by_asc_BSR","params":"query=*&hitsPerPage=36&page=0&facets=*&facetFilters=%5B%22inStock%3A1%22%2C%22approvalStatus%3A1%22%2C%22allCategories%22%2C%22badge.title.en%3A-LASTCHANCE%22%5D&getRankingInfo=1&clickAnalytics=true&attributesToHighlight=null&analyticsTags=%5B%22hbxbedroom-bedsandbedsets%22%2C%22en%22%2C%22Desktop%22%5D&attributesToRetrieve=concept%2CmanufacturerName%2Curl%2C333WX493H%2C345WX345H%2C505WX316H%2C550WX550H%2C499WX739H%2Cbadge%2Cname%2Csummary%2CwasPrice%2Cprice%2CemployeePrice%2CshowMoreColor%2CproductType%2CchildDetail%2Csibiling%2CthumbnailImg%2CgallaryImages%2CisConceptDelivery%2CextProdType%2CreviewAvgRating%2CreferencesAvailable%2CitemType%2CbaseProductId&numericFilters=price%20%3E%201&query=*&maxValuesPerFacet=500&tagFilters=%5B%5B%22homebox%22%5D%5D"}]}';
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

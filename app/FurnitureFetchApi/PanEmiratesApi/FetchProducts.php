<?php

namespace App\FurnitureFetchApi\PanEmiratesApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://www.panemirates.com/uae/en/rss/1/productlistings?page=1";
    public function __construct(){
        $this->resolve_api($json = false);
        $this->results['products'] =  simplexml_load_string($this->results['products']);
        $this->results['products'] =  json_encode($this->results['products']);
        $this->results['products'] =  json_decode($this->results['products'],true);
    }

    public function get_page_info(){
       $last_page_url = "https://www.panemirates.com".$this->results['products']['link'][3]['@attributes']['href'];
       parse_str(parse_url($last_page_url,PHP_URL_QUERY),$query);
       return $query['page'] ?? 0;
    }

    public function get_attributes(){
        return [];
    }

    public function get_slugs(){
        if(!empty($this->results['products']['channel']['item'])){
            return collect($this->results['products']['channel']['item'])->pluck('guid')->toArray();
        }

    }
}

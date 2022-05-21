<?php

namespace App\FurnitureFetchApi\DanubeHomeApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://danubehome.com/ae/en/graphql?hash=621218113&_sort_0={%22custom_sort%22:[{%22attribute%22:%22position%22,%22direction%22:%22ASC%22}]}&_filter_0={price_range:{eq:null},custom_filter:[]}&_pageSize_0=40&_currentPage_0=1";
    public function __construct(){
        $this->resolve_api();
    }

    public function get_page_info(){
        return $this->results ? $this->results['products']['data']['products']['page_info']['total_pages'] : [];
    }

    public function get_attributes(){
        return $this->results ? $this->results['products']['data']['products']['attributes'] : [];
    }

    public function get_slugs(){
        $slugs = $this->results ? $this->results['products']['data']['products']['items'] : [];
        if(!empty($slugs)){
            return collect($slugs)->pluck('url_key')->toArray();
        }
    }
}

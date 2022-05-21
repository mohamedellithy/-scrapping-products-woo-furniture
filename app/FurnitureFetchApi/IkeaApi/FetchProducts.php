<?php

namespace App\FurnitureFetchApi\IkeaApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://sik.search.blue.cdtapps.com/ae/en/special?special=all&size=20&subcategories-style=tree-navigation&c=lf&sort=RELEVANCE";
    public function __construct(){
        $this->resolve_api();
    }

    public function get_page_info(){
        return $this->results ? $this->results['products']['specialPage']['productCount'] : [];
    }

    public function get_attributes(){
        return $this->results ? $this->results['products']['data']['products']['attributes'] : [];
    }

    public function get_slugs(){
        $slugs = $this->results ? $this->results['products']['specialPage']['productWindow'] : [];
        return $slugs;
    }
}

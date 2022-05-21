<?php

namespace App\FurnitureFetchApi\TheHomeApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use App\FurnitureFetchApi\InterfaceFurnitureApi\FurnitureProducts as FurnitureProducts;
class FetchProducts extends HandleFurnitureRequest implements FurnitureProducts{
    public $endpoint = "https://thehome.ae/wp-json/wp/v2/product?per_page=30&page=1";
    public function __construct(){
        $this->resolve_api();
    }

    public function get_page_info(){
        return $this->results ? $this->results['headers']['X-WP-TotalPages'] : [];
    }

    public function get_attributes(){
        return $this->results ? $this->results['headers'] : [];
    }

    public function get_slugs(){
        $slugs = $this->results ? $this->results['products'] : [];
        return $slugs;
    }
}

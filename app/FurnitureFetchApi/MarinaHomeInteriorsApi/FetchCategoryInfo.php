<?php 

namespace App\FurnitureFetchApi\MarinaHomeInteriorsApi;
use App\FurnitureFetchApi\HandleFurnitureRequest;
class FetchCategoryInfo extends HandleFurnitureRequest{
    public $endpoint = "https://www.marinahomeinteriors.com/rest/V1/categories";
    protected static $cat_id = null;
    public function __construct(){
        if(self::$cat_id != null)
            $this->endpoint = $this->endpoint.'/'.self::$cat_id;

        $this->resolve_api($json = true,$params = 'category');
    }

    public function get_categories(){
        $slugs = $this->results ? $this->results['category'] : [];
        return $slugs;
    }

    public static function fetch_category_by_id($id){
        self::$cat_id = $id;
        $category = new FetchCategoryInfo();
        return $category->results['category'] ?? null;
    }
}

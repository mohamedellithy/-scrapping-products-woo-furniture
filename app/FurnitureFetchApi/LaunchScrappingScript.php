<?php
namespace App\FurnitureFetchApi;
use App\FurniturePlatform;
use App\Option;
use App\FurnitureFetchApi\DeleteFetechedProducts;
class LaunchScrappingScript{
    public $platforms = [];
    public $results   = [];
    public function __construct(){
        $this->fetched_first_time_products();
    }

    public function fetched_first_time_products(){
        $this->platforms = FurniturePlatform::where(['fetched_status' => 0])->pluck('name')->toArray();
        $this->launch_process_job($process = 'fetched');
    }

    public function launch_process_job($type){
        // loop platforms to check who need update and need to fetched
        foreach($this->platforms as $platform):

            // here class to fetch product
            $fetch_products  = '\App\FurnitureFetchApi\\'.$platform.'Api\FetchProducts';
            $products        = new $fetch_products();

            // here class to fetch single product
            $single_product  = '\App\FurnitureFetchApi\\'.$platform.'Api\FetchSingleProduct';
            $fetched         = new $single_product($products);

            $this->results[] = $fetched;

            // updated and fetched products
            $this->{$type."_completed"}($platform);

            // removed products
            $this->process_removed_products($platform);

        endforeach;
    }

    public function fetched_completed($platform){
        // approve status of fetched products
        FurniturePlatform::where(['name' => $platform,'fetched_status' => 0])->update([
           'fetched_status' => 1
        ]);
    }

    public function process_removed_products($platform){
        // removed products
        $removed_products = null;

        // updated or create options
        $new_products =  Option::where(['option_name' => $platform."_new"])->value('option_value');

        // updated or create options
        $old_products =  Option::where(['option_name' => $platform."_old"])->value('option_value');

        // convert value from new to old
        Option::updateOrCreate(
            ['option_name'  => $platform."_old"],
            ['option_value' => $new_products]
        );

        // convert new to null
        Option::updateOrCreate(
            ['option_name'   => $platform."_new"],
            ['option_value'  => serialize([])]
        );

        // new product is null nad old products is null
        if(($new_products != null ) && ($old_products != null)):
            $old_products     = unserialize($old_products);
            $new_products     = unserialize($new_products);
            $removed_products = array_diff($old_products,$new_products);
        endif;


        if($removed_products != null):
            // delete fetched products
            new DeleteFetechedProducts($removed_products);
        endif;
    }
}

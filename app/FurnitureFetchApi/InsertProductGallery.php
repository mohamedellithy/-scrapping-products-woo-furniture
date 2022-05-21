<?php
namespace App\FurnitureFetchApi;

use App\Product;
use App\ProductAttachment;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use Storage;
class InsertProductGallery extends HandleFurnitureRequest{
    private static $gallery_label;
    private static $attachment_IDs;
    public static $guid_url = "http://localhost/wordpress/wordpress/wp-content/uploads/";
    public static $dir_container = "furniture/";
    public function __construct(Product $product,$filter) {

        self::$attachment_IDs = [];

        // forloop on all gallery images
        foreach($filter->product_gallery as $gallery){

            // pass url of image thumbnail
            $this->endpoint = strtok($gallery['thumbnail']['url'],"?");

            self::$gallery_label = $gallery['label'];

            // check if image move or
            if($this->move_image_to_dir()){

                // inserted attachments
                $this->create_gallery($product,$filter);
            }

        }

        // inserted meta for gallery
        if(self::$attachment_IDs):
            $this->results = $this->insert_to_product_meta($product,self::$attachment_IDs) ? true : false;
        else:
            $this->results =  false;
        endif;
    }

    public function move_image_to_dir(){
        // fetch image from url
        $this->resolve_api($json = false,'image');

        // storage image in folder place
        return Storage::put(self::$dir_container.$this->attachment_name(),$this->results['image']);
    }

    public function attachment_name(){
        // get attachment name
        $attachment_name      = pathinfo($this->endpoint,PATHINFO_FILENAME);

        // get attachment extension
        $attachment_extension = pathinfo($this->endpoint,PATHINFO_EXTENSION);

        // return full name for image
        return $attachment_name.".".$attachment_extension;
    }

    public function mime_type(){
        // get attachment type
        return 'image/jpeg';
    }

    public function create_gallery(Product $product,$filter){
        $inserted_attachment = $product->attachments()->updateOrCreate(
        ['post_name'             => pathinfo($this->endpoint,PATHINFO_FILENAME) ?? null],
        [
            'post_content'          => $filter->product_description ?? '',
            'post_excerpt'          => $filter->product_short_description ?? '',
            'post_title'            => self::$gallery_label ?? $filter->product_thumbnail['label'],
            'post_status'           => 'inherit',
            'post_date_gmt'         => date('Y-m-d h:i:s'),
            'post_modified_gmt'     => date('Y-m-d h:i:s'),
            'guid'                  => self::$guid_url.$this->attachment_name(),
            'post_mime_type'        => $this->mime_type()
        ]);

        if($inserted_attachment){
            self::$attachment_IDs[] = $inserted_attachment->ID;

            $inserted_attachment->meta()->updateOrCreate(
                ['meta_key'   => "_wp_attached_file"],
                ['meta_value' => self::$dir_container.$this->attachment_name()]
            );

            $meta_attachment = array (
                'width'      => "100%",
                'height'     => "100%",
                'file'       => self::$dir_container.$this->attachment_name(),
                'sizes'      => array(
                    'medium' => [
                        'width'      => "50%",
                        'height'     => "50%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'thumbnail' => [
                        'width'      => "30%",
                        'height'     => "30%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'medium_large' => [
                        'width'      => "30%",
                        'height'     => "30%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'woocommerce_thumbnail' => [
                        'width'      => "30%",
                        'height'     => "30%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'woocommerce_single' => [
                        'width'      => "100%",
                        'height'     => "100%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'woocommerce_gallery_thumbnail' => [
                        'width'      => "100%",
                        'height'     => "100%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'shop_catalog' => [
                        'width'      => "100%",
                        'height'     => "100%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'shop_single' => [
                        'width'      => "100%",
                        'height'     => "100%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ],
                    'shop_thumbnail' => [
                        'width'      => "100%",
                        'height'     => "100%",
                        'file'       => $this->attachment_name(),
                        'mime-type'  => $this->mime_type()
                    ]
                ),         // thumbnails etc.
                'image_meta' => array(),    // EXIF data
            );

            $inserted_attachment->meta()->updateOrCreate(
                [ 'meta_key'   => "_wp_attachment_metadata"],
                [ 'meta_value' => serialize($meta_attachment)]
            );

            $inserted_attachment->meta()->updateOrCreate(
                [ 'meta_key'   => "rtwpvg_images"],
                [ 'meta_value' => serialize($meta_attachment)]
            );


            return true;
        }

        return false;
    }

    public function insert_to_product_meta(Product $product,$IDS){
        // Item plugins Variation Images Gallery for WooCommerce
        // https://radiustheme.com/demo/wordpress/woopluginspro/product/woocommerce-variation-images-gallery/
        $prod_ids = serialize($IDS);
        if($product->post_type == 'product_variation'):
            $product->meta()->updateOrCreate(
                ['meta_key' => 'rtwpvg_images'],
                ['meta_value' => $prod_ids]
            );
        endif;

        $ID_items = implode(',',$IDS);
        return $product->meta()->updateOrCreate(
            ['meta_key' => '_product_image_gallery'],
            ['meta_value' => $ID_items]
        ) ? true : null;
    }

}

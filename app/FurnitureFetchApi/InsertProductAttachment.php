<?php
namespace App\FurnitureFetchApi;

use App\Product;
use App\ProductAttachment;
use App\FurnitureFetchApi\HandleFurnitureRequest;
use Storage;
class InsertProductAttachment extends HandleFurnitureRequest{
    public static $guid_url = "http://localhost/wordpress/wordpress/wp-content/uploads/";
    public static $dir_container = "furniture/";
    public function __construct(Product $product,$filter) {
        // pass url of image thumbnail
        $this->endpoint = strtok($filter->product_thumbnail['url'],"?");
        if(!filter_var($this->endpoint,FILTER_VALIDATE_URL)) {
            $this->results = 'Attachment url is not valid';
            return;
        }

        // check if image move or
        if($this->move_image_to_dir()){

            // inserted attachments
            $this->results =  $this->create_attachment($product,$filter);
        }
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

    public function create_attachment(Product $product,$filter){
        $inserted_attachment = $product->attachments()->updateOrCreate(
        ['post_name'             => pathinfo($this->endpoint,PATHINFO_FILENAME) ?? null],
        [
            'post_content'          => $filter->product_description ?? '',
            'post_excerpt'          => $filter->product_short_description ?? '',
            'post_title'            => $filter->product_thumbnail['label'] ?? '',
            'post_status'           => 'inherit',
            'post_date_gmt'         => date('Y-m-d h:i:s'),
            'post_modified_gmt'     => date('Y-m-d h:i:s'),
            'guid'                  => self::$guid_url.self::$dir_container.$this->attachment_name(),
            'post_mime_type'        => $this->mime_type()
        ]);

        if($inserted_attachment){
            $this->insert_to_product_meta($product,$inserted_attachment);

            $inserted_attachment->meta()->updateOrCreate(
                ['meta_key'   => "_thumbnail_id"],
                ['meta_value' => $inserted_attachment->ID]
            );

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

            return true;
        }

        return false;
    }

    public function insert_to_product_meta(Product $product,ProductAttachment $atttachment) {
        return $product->meta()->updateOrCreate(
            ['meta_key' => '_thumbnail_id'],
            ['meta_value' => $atttachment->ID]
        );
    }


}

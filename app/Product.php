<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\ProductScope;
use App\ProductAttachment;
use App\ProductAttribute;
use App\ProductMeta;
use APP\TermTaxonomy;
use App\TermTaxonomyProduct;
use App\ProductMetaLookUp;
class Product extends Model
{
    // protected fillable
    protected $fillable =['post_date','post_content','post_excerpt','post_title','post_status','post_parent','post_name','post_type','post_date_gmt','post_modified_gmt'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'post_date_gmt','post_modified_gmt'
    ];

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    protected $attributes = [
        'post_excerpt'          => '',
        'to_ping'               => '',
        'pinged'                => '',
        'post_content_filtered' => '',
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d h:i:s';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_posts';

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'WoCommerce';

      /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ProductScope);
    }

    public function meta(){
        return $this->hasMany(ProductMeta::class,'post_id','ID');
    }

    public function attachments(){
        return $this->hasMany(ProductAttachment::class,'post_parent','ID');
    }

    public function get_current_date(){
        return date('Y-m-d H:i:s');
    }

    public function term_taxonomy()
    {
        return $this->belongsToMany(TermTaxonomy::class,TermTaxonomyProduct::class,'term_taxonomy_id','object_id');
    }

    public function attribute(){
        return $this->hasMany(ProductAttribute::class,'product_id','ID');
    }

    public function product_meta_look_up(){
        return $this->hasMany(ProductMetaLookUp::class,'product_id','ID');
    }

    // public function setPostModifiedGmtAttribute($value){
    //     $this->attributes['post_modified_gmt'] = date('Y-m-d H:i:s');
    // }

    // public function setPostTypeAttribute(){
    //     $this->attributes['post_type'] = 'product';
    // }
}

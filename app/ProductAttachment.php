<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\Scopes\AttachmentScope;
class ProductAttachment extends Model
{
    // protected fillable
    protected $fillable =['post_date','post_content','post_excerpt','guid','post_title','post_status','post_name','post_type','post_date_gmt','post_modified_gmt','post_mime_type'];
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
        'post_type'             => 'attachment'
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

        static::addGlobalScope(new AttachmentScope);
    }

    public function parent_product(){
        return $this->belongsTo(Product::class,'post_parent','ID');
    }

    public function meta(){
        return $this->hasMany(ProductMeta::class,'post_id','ID');
    }
}

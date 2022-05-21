<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
class ProductMeta extends Model
{
    //
    protected $fillable = ['meta_key','meta_value'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_postmeta';

     /**
     * The primary key associated with the table.
     *
     * @var string
     */

    protected $primaryKey = 'meta_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'WoCommerce';

    public function product(){
       return $this->belongsTo(Product::class,'post_id','ID');
    }
}

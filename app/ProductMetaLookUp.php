<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
class ProductMetaLookUp extends Model
{


    // protected fillable
    protected $fillable =['product_id','sku','virtual','downloadable','min_price','max_price','onsale',
    'stock_quantity','stock_status','rating_count','average_rating','total_sales','tax_status','tax_class'];

    /**
     * primaryKey
     *
     * @var integer
     * @access protected
     */
    protected $primaryKey = null;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    protected $attributes = [
        'downloadable'          => 0,
        'virtual'               => 0,
        'tax_status'            => 'taxable',
        'rating_count'          => 0,
        'average_rating'        => 0.00,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_wc_product_meta_lookup';

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
        return $this->belongsTo(Product::class,'product_id','ID');
    }

}

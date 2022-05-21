<?php

namespace App;
use App\Product;
use App\Term;
use Illuminate\Database\Eloquent\Model;
class ProductAttribute extends Model
{
    // protected fillable
    protected $fillable = ['product_id','product_or_parent_id','taxonomy','term_id','is_variation_attribute','in_stock'];

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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_wc_product_attributes_lookup';

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

      /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id','ID');
    }

    public function term(){
        return $this->belongsTo(Term::class,'term_id','term_id');
    }

}

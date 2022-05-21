<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TermTaxonomy;
class AttributeTaxonomy extends Model
{
    // protected fillable
    protected $fillable =['attribute_name','attribute_label','attribute_type','attribute_orderby','attribute_public'];

    protected $attributes = [
        'attribute_orderby' => 'menu_order'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_woocommerce_attribute_taxonomies';

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'attribute_id';

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

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class TermTaxonomyProduct extends Model
{
    // protected fillable
    protected $fillable = ['object_id','term_taxonomy_id','term_order'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_term_relationships';

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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TermTaxonomy;
use App\ProductAttribute;
class Term extends Model
{
    // protected fillable
    protected $fillable =['name','slug','term_group'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_terms';

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'term_id';

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

    public function term_taxonomy(){
        return $this->hasOne(TermTaxonomy::class,'term_id','term_id');
    }

    public function meta(){
        return $this->hasMany(TermMeta::class,'term_id','term_id');
    }

    public function attribute(){
        return $this->hasMany(ProductAttribute::class,'term_id','term_id');
    }

}

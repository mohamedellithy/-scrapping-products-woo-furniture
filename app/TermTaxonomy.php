<?php

namespace App;
use App\Product;
use Illuminate\Database\Eloquent\Model;
use App\Term;
use App\TermTaxonomyProduct;
class TermTaxonomy extends Model
{
    // protected fillable
    protected $fillable =['taxonomy','description','parent','count'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_term_taxonomy';

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

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
        return $this->belongsToMany(Product::class,TermTaxonomyProduct::class,'term_taxonomy_id','object_id');
    }

    public function terms(){
        return $this->belongsToMany(Term::class,'term_id','term_id');
    }

}

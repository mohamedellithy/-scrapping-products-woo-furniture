<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Term;
class TermMeta extends Model
{
    //
    protected $fillable = ['meta_key','meta_value'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_termmeta';

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

    public function term(){
       return $this->belongsTo(Term::class,'term_id','term_id');
    }
}

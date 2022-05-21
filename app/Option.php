<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    // protected fillable
    protected $fillable =['option_name','option_value','autoload'];

    protected $attributes = [
        'autoload'  => 'yes'
    ];

     /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_options';

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'option_id';
}

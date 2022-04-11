<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'properties';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = '_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'importance',           // = priority by user, float -3..5; default =0; -3="very low" ... 3="very high", 4="must", 5="now"
        'urgency',              // NOT USED; currently indirectly computed based on deadlines etc
        'deadline',             // date in minutes 
        'deadline_range',       // NOT USED; in munutes, means deadline + range is still acceptable 
        'attention_average',    // TOFIX - use in Action. in minutes - expected remaining average attention
        'attention_next',       // in minutes - expected next attention 0=undefined
        'duration_average',     // in minutes - expected remaining average attention, including attention and wait without attention; 0=undefined
        'duration_min',         // NOT USED; in minutes - expaected min average duration
        'created_at',           
        'updated_at', 
    ];

    public function task()
    {
        return $this->belongsTo('App\Models\Task', 'task_id', 'id');
    }

}

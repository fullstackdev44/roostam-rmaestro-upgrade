<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

const EXEC_DATA_PATH_NONE = -10;

class PrecedenceData extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'precedencedatas';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = '_id';

    /**
     * State interpretation: -1=farEnough; 0=normal, 1=approaching; 2=tooClose; 3=passed; 4-5=passedTooFar; EXEC_DATA_PATH_NONE=irrelevant
     * states: deadline; attention; duration; pathAttention; pathDuration;
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'exec_prio',            // = priority adjusted by urgency, the main ordering value
        'urgency',
        'exec_prio_message',    // = message that explains to the user how the exec_prio is calculated
        'exec_recommendation',  // = what the system recommends re the current settings - prio, duration etc
        'deadline_state',
        'attention_state',
        'duration_state',
        'path_attention_state',
        'path_duration_state',
        'attention_path_to_deadline',     // string, with '-' separator, 'this' is always first
        'duration_path_to_deadline',     // string, with '-' separator, 'this' is always first
        'suggested_delay',      // = the recommended default delay in minutes the system suggests if user doesn't choose otherwise; 0 = no recommendation
        'created_at',           
        'updated_at', 
    ];

    public function task()
    {
        return $this->belongsTo('App\Models\Task', 'task_id', 'id');
    }

}

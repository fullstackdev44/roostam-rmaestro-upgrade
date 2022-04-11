<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  Managed by ExecStateMachineSerice only
 *  
 *  can be read by anyone  
 */

class ExecStatus extends Model
{
    protected $table = 'execstatuses';

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
        'exec_state',           // the state the task in to wait for the event
        'is_active_action',     // boolean 
    ];

    
    public static function getTaskExecState($taskId)
    {
        $execStatus = ExecStatus::where('task_id', $taskId)
                ->first();
        
        return $execStatus->exec_state;
    }

    
    public static function setTaskExecState($taskId, $execState)
    {
        $execStatus = ExecStatus::where('task_id', $taskId)
                ->first();
        
        if (!$execStatus) {
            ExecStatus::create([
                'task_id' => $taskId,
                'exec_state' => $execState,
                'is_active_action' => false,
            ]);
        }
        else {
            $execStatus->update(['exec_state' => $execState]);
        }
    }
    

}

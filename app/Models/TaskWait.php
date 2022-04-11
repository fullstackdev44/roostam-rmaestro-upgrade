<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TaskWait extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'taskwaits';

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
        'task_id',              // the waiting task id
        'task_exec_state',      // the state the task in to wait for the event
        'for_event',            // event waiting for 
        'for_id'                // id - task/other, based on the event
    ];

    
    public static function addTaskWait($taskId, $taskExecState, $forEvent, $forId)
    {
        TaskWait::create([
            'task_id' => $taskId,
            'task_exec_state' => $taskExecState,
            'for_event' => $forEvent,
            'for_id' => $forId,        
        ]);
    }

    
    public static function taskWaitsAll($taskId, $execState)
    {
        return TaskWait::where('task_id', $taskId)
                ->where('task_exec_state', $execState)
                ->get();
    }
    
    public static function taskHasWaits($taskId, $execState)
    {
        return TaskWait::where('task_id', $taskId)
                ->where('task_exec_state', $execState)
                ->first() 
                    != NULL;
    }
    
    
    public static function taskWaitingAll($forEvent, $forId)
    {
        return TaskWait::where('for_id', $forId)
                ->where('for_event', $forEvent)                
                ->get();
    }
    

}

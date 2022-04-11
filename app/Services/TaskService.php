<?php

namespace App\Services;

use DB;
use Validator;

use App\Models\Task;
use App\Models\Property;


class TaskService
{
    const DEFAULT_ORDER = 'desc';


    public static function validate($input, $id = null)
    {
        $validationRules = [
            'name' => 'required',
            'deadline' => 'required|date_format:"Y-m-d H:i:s"',
            'importance' => 'required',
            'duration_average' => 'required',
        ];

        if ($input['parent_id']) {
            $validationRules['parent_id'] =  'exists:tasks,id';
        }

        if ($id) {
            $validationRules['id'] =  'exists:tasks,id';
        }

        return Validator::make($input, $validationRules)->setAttributeNames(trans('tasks'));
    }
    
    
    public static function validateUpdate($input, $id = null)
    {
        $validationRules = [];
        switch ($input['attr']) {
            case 'name':
                $validationRules['name'] =  'required';
                break;
            case 'deadline':
                $validationRules['deadline'] =  'required|date_format:"Y-m-d H:i:s"';
                break;
            case 'duration_average':
                $validationRules['duration_average'] =  'required';
                break;
            case 'parent_id':
                $validationRules['parent_id'] =  'exists:tasks,id';
                break;
            
            default:
                break;
        }

        $validationRules['id'] =  'exists:tasks,id';

        return Validator::make([$input['attr'] => $input['val']], $validationRules)->setAttributeNames(trans('tasks'));
    }

    
    public static function createAndStoreTask($parentId)
    {
        $input = [
                'name' => strval(rand(1,100)),
                'parent_id' => $parentId,
                'is_executing' => false,
                'subtask_batch' => 0,
                'importance' => 0,
                'urgency' => 0, 
                'deadline' => '1970-01-01 00:00:00',
                'deadline_range' => 0,
                'attention_average' => 30,
                'attention_next' => 5,
                'duration_average' => 4320,
                'duration_min' => 120,
            ];
        
        $errors = self::validate($input);
        if ($errors->fails()) {
            return [
                'errors' => $errors->messages()->toArray(),
            ];
        }

        $task = Task::create([
            'name' => $input['name'],
            'user_id' => auth()->id(),
            'subtask_batch' => $input['subtask_batch'],
            'parent_id' => $input['parent_id'],
            'is_executing' => $input['is_executing'],
        ]);
        
        Property::create([
            'task_id' => $task->id,
            'importance' => $input['importance'],
            'urgency' => $input['urgency'],
            'deadline' => $input['deadline'],
            'deadline_range' => $input['deadline_range'],
            'attention_average' => $input['attention_average'],
            'attention_next' => $input['attention_next'],
            'duration_average' => $input['duration_average'],
            'duration_min' => $input['duration_min'],
        ]);

        return [
            'errors' => null,
            'task' => $task,
        ];
    }

    
    public static function copyAndStoreTask($task, $newParentId, $copyTaskShallowCallback)
    {
        DB::beginTransaction();
        try {
            $newTask = self::copyTaskShallow($task, $newParentId);
            $newTaskId = $newTask->id;
            
            self::copyAndStoreProperty($task->property, $newTaskId);
            
            call_user_func_array($copyTaskShallowCallback, array($task->id, $newTaskId));
            
            if ($task->children) {
                foreach ($task->children as $child) {
                    self::copyAndStoreTask($child, $newTaskId, $copyTaskShallowCallback);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'errors' => [trans('tasks.update_error')],
            ];
        }
        
        return $newTaskId;

    }
    
    
    private static function copyTaskShallow($baseTask, $newParentId)
    {
        return Task::create([
            'name' => $baseTask->name,
            'user_id' => $baseTask->user_id,
            'parent_id' => $newParentId,
            'subtask_batch' => $baseTask->subtask_batch,
            'is_executing' => false,
        ]);
    }
    
    private static function copyAndStoreProperty($baseProperty, $newTaskId)
    {
        return Property::create([
            'task_id' => $newTaskId,  
            'importance' => $baseProperty->importance,
            'urgency' => $baseProperty->urgency,
            'deadline' => $baseProperty->deadline,
            'deadline_range' => $baseProperty->deadline_range,
            'attention_average' => $baseProperty->attention_average,
            'attention_next' => $baseProperty->attention_next,
            'duration_average' => $baseProperty->duration_average,
            'duration_min' => $baseProperty->duration_min,
        ]);
    }
    
    
    public static function updateTask($input, $task)
    {
        $errors = self::validateUpdate($input, $task->id);        
        if ($errors->fails()) {
            return [
                'errors' => $errors->messages()->toArray(),
            ];
        }

        DB::beginTransaction();
        try {
            if (in_array($input['attr'], ['name', 'subtask_batch', 'parent_id', 'is_executing'])) {
                $task->update([$input['attr'] => $input['val']]);
            }

            if (in_array($input['attr'], ['importance', 'urgency', 'deadline', 'deadline_range', 'attention_average',
                                            'attention_next', 'duration_average', 'duration_min', ])) {
                $task->property()->update([ $input['attr'] => $input['val'], ]);
            }

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();

            return [
                'errors' => [trans('tasks.update_error')],
            ];
        }
    }

    public static function delete($task)
    {
        DB::beginTransaction();
        try {
            // firts delete all children
            foreach ($task->children as $child) {
                self::delete($child);
            }
 
            $task->delete();
            DB::commit();

            return [
                'errors' => null,
            ];
        } catch(\Exception $e) {
            dd($e);
            DB::rollback();

            return [
                'errors' => [trans('tasks.delete_error')],
            ];
        }
    }
    
   
    public static function getExecStatesArray()
    {
        /*$result = [];
        foreach (config('task.exec_state') as $key => $value) {
            $result[$value] = $key;
        }*/

        return config('task.exec_state');
    }
    
    
    public static function getRootTaskId($taskId)
    {
        $task = Task::find($taskId);
        
        return $task->getRootId();
        
    }
    
    
}
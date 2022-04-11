<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Desk\Services\DeskService;
use App\Services\PrecedenceService;
use App\Services\ExecStateMachineService;
use App\Models\Task;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    
    public function actionLists(Request $request)
    {
        $input = $request->only(
            'user_id',
            'subtask_batch',
            'offset',
            'limit',
            'keyword'
        );

        $actionsList = Task::has('user')
                        ->where('user_id', $input['user_id'] ?: auth()->id())
                        ->has('property')
                        ->join('execstatuses', 'tasks.id', '=', 'execstatuses.task_id')
                        ->where('is_active_action', true)
                        ->join('precedencedatas', 'tasks.id', '=', 'precedencedatas.task_id')
                        ->join('properties', 'tasks.id', '=', 'properties.task_id')                         
                        ->get();

        PrecedenceService::updatePrecedenceDataList($actionsList, false);
        
        $updatedActions = Task::has('user')
                        ->where('user_id', $input['user_id'] ?: auth()->id())
                        ->has('property')
                        ->join('execstatuses', 'tasks.id', '=', 'execstatuses.task_id')
                        ->where('is_active_action', true)
                        ->join('properties', 'tasks.id', '=', 'properties.task_id')
                        ->join('precedencedatas', 'tasks.id', '=', 'precedencedatas.task_id')
                        ->orderBy('exec_prio', 'desc')
                        ->get();       

        /* deprecated - is_active_action should handle
        foreach ($updatedActions as $key => $action) {
            if ($action->has_children) {
                unset($updatedActions[$key]);
            }
        }*/

        return $updatedActions;
        
    }
    
    
    public function projectLists(Request $request)
    {
        $input = $request->only(
            'user_id',
            'subtask_batch',
            'offset',
            'limit',
            'keyword'
        );

        $keyword = $input['keyword'];
        $tasks = Task::has('user')
                    ->where('user_id', $input['user_id'] ?: auth()->id())
                    ->join('properties', 'tasks.id', '=', 'properties.task_id')
                    ->where('parent_id', null)
                    ->where('is_executing', true)
                    ->orderBy('name', 'asc')
                    ->where('name', 'like', "%$keyword%")
                    ->join('execstatuses', function ($join) { 
                            $join->on('tasks.id', '=', 'execstatuses.task_id') 
                                    ->where('execstatuses.exec_state', '=', 'exec_active'); })
                    ->join('precedencedatas', 'tasks.id', '=', 'precedencedatas.task_id')
                    ->skip($input['offset'] ?: 0)
                    ->take($input['limit'] ?: 40) 
                    ->get();
        
        return $tasks;
    }


    public function templateLists(Request $request)
    {
        $input = $request->only(
            'user_id',
            'subtask_batch',
            'offset',
            'limit',
            'keyword'
        );

        $keyword = $input['keyword'];
        return Task::has('user')
                    ->where('user_id', $input['user_id'] ?: auth()->id())
                    ->join('properties', 'tasks.id', '=', 'properties.task_id')
                    ->where('parent_id', null)
                    ->where('is_executing', false)
                    ->orderBy('name', 'asc')
                    ->where('name', 'like', "%$keyword%")
                    ->skip($input['offset'] ?: 0)
                    ->take($input['limit'] ?: 100)
                    ->get();

    }


    public function createAndStoreTask(Request $request)
    {        
        $parentId = $request->get('id') ?: null;
        
        DB::beginTransaction();
        
        try {
            $task = TaskService::createAndStoreTask($parentId);

            $newTaskId = $task['task']->id;  
            
            DeskService::createAndStoreTask($newTaskId);
            
            PrecedenceService::createAndStoreTask($newTaskId);  
            
            DB::commit();
            
        } catch(\Exception $e) {
            dd($e);
            DB::rollback();

            return [
                'errors' => [trans('tasks.create_error')],
            ];
        }
        
        $newTask = Task::has('user')
            ->where('tasks.id', $newTaskId)
            ->join('properties', 'tasks.id', '=', 'properties.task_id')
            ->first();

        return $newTask;

    }

    
    public function copyAndStoreTask($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return [
                'errors' => [trans('tasks.delete_error')],
            ];
        }
        
        $copyAndStoreTaskCallback = static function ($baseTaskId, $newTaskId)
        {
            DeskService::copyAndStoreTask($baseTaskId, $newTaskId); 
            PrecedenceService::copyAndStoreTask($baseTaskId, $newTaskId);
        };

        $newTaskId = TaskService::copyAndStoreTask($task, NULL, $copyAndStoreTaskCallback);
        
        $newTaskFull = Task::has('user')
                ->where('tasks.id', $newTaskId)
                ->join('properties', 'tasks.id', '=', 'properties.task_id')
                ->first();
        
        return [
            'task' => $newTaskFull,
            'errors' => false,                
        ];

    }
    
    
    public function update(Request $request, $id)
    {
        
        $task = Task::find($id);
        if (!$task) {
            return [
                'errors' => [trans('tasks.create_error')],
            ];
        }

        $input = $request->all();
        TaskService::updateTask($input, $task);
        
        return [
            'errors' => null,
            'task' => Task::join('properties', 'tasks.id', '=', 'properties.task_id')
                ->find($task->id),
        ];

        
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return [
                'errors' => [trans('tasks.delete_error')],
            ];
        }

        return TaskService::delete($task);
    }

    
    public function getChildren($id)
    {
        if (Task::find($id)->is_executing) {
            return Task::has('user')
                        ->where('parent_id', $id)
                        ->orderBy('subtask_batch', 'asc')
                        ->orderBy('tasks.created_at', 'asc')
                        ->join('properties', 'tasks.id', '=', 'properties.task_id')
                        ->join('execstatuses', 'tasks.id', '=', 'execstatuses.task_id')
                        ->join('precedencedatas', 'tasks.id', '=', 'precedencedatas.task_id')
                        ->get();
        }
        else {
            return Task::has('user')
                        ->where('parent_id', $id)
                        ->orderBy('subtask_batch', 'asc')
                        ->orderBy('tasks.created_at', 'asc')
                        ->join('properties', 'tasks.id', '=', 'properties.task_id')
                        ->get();
        }
        
    }
    
    
    public function getRootTask($taskId)
    {
        $rootId = TaskService::getRootTaskId($taskId);
        
        return Task::has('user')
            ->where('tasks.id', $rootId)
            ->join('properties', 'tasks.id', '=', 'properties.task_id')
            ->join('execstatuses', 'tasks.id', '=', 'execstatuses.task_id')
            ->join('precedencedatas', 'tasks.id', '=', 'precedencedatas.task_id')
            ->first();
        
    }
    
    
    /* 
     * Desk API 
     */
    
    public static function getDesks($taskId)
    {
        return DeskService::getDesks($taskId);
    }
    
    public static function newWindow(Request $request)
    {
        return DeskService::newWindow($request);
    }

    public static function updateWindow(Request $request, $windowId)
    {
        return DeskService::updateWindow($request, $windowId);
    }
    
    public static function deleteWindow($windowId)
    {
        return DeskService::deleteWindow($windowId);
    }

        
    public static function executeTask($rootTaskId)
    {
        // build the hierarchy of waits; NOTE - assuming no such building event prior to execution
        APIController::buildStateMachineTree($rootTaskId, 0);  // whole tree recursively
        
        // now the whole tree is in waiting state; root waiting for 'user_start'
        
        // now trigger the activation of the root => then the ExecStateMachineService will handle the rest             
        $result = ExecStateMachineService::event('user_start', $rootTaskId, 0); 
        
        if (!$result['errors']) {
            // mark it recursively executing 
            APIController::updateExecuting($rootTaskId);
            
            PrecedenceService::updatePrecedenceDataList(Task::find($rootTaskId), true);
            
            $task = Task::where('tasks.id', $rootTaskId)
                    ->join('properties', 'tasks.id', '=', 'properties.task_id')
                    ->join('execstatuses', 'tasks.id', '=', 'execstatuses.task_id')
                    ->join('precedencedatas', 'tasks.id', '=', 'precedencedatas.task_id')
                    ->first();
            
            return [
                'errors' => NULL,
                'task' =>$task,            
            ];
        }
        else { 
            return $result;
        }
    }

    
    public static function updateExecuting($id)
    {
        $task = Task::find($id);
        $task->update(['is_executing' => true]);
        
        foreach ($task->children as $child) {
            self::updateExecuting($child->id);
        }
    }

    
    public static function buildStateMachineTree($taskId, $parentId)
    {
        ExecStateMachineService::event('add_task', $taskId, null);
        ExecStateMachineService::event('parent_child_add', $parentId, $taskId);

        $task = Task::find($taskId);
        
        $batches = array(0=>array()); // [subtask_batch] = set of ids of the same batch
        for($i=0; $i<20; $i++) {
            $batches[$i] = array();
        }
        
        if ($task->children->first()) {
            foreach ($task->children as $child) {
                APIController::buildStateMachineTree($child->id, $taskId);
                
                // build batches array
                array_push($batches[$child->subtask_batch], $child->id);
            }
        }
        
        // now add batch depencies 
        $previousBatch = $batches[1];  // batch 0 are independents
        foreach ($batches as $batchOrder => $batchSet) {
            $nextBatch = $batches[$batchOrder];
            
            if ($batchOrder>1 && count($nextBatch)>0) {
                foreach ($nextBatch as $nextId) {
                    foreach ($previousBatch as $previousId) {
                        ExecStateMachineService::event('add_dependency', $previousId, $nextId);
                    }
                }
                
                $previousBatch = $nextBatch;
            }
        }
        
    }
    
    
    public function taskDone($taskId)
    {
        $result = ExecStateMachineService::event('user_done', $taskId, 0);
        
        PrecedenceService::updatePrecedenceDataList(Task::find( Task::find($taskId)->getRootId() ), true);
        
        return $result;
    }


}

<?php

namespace App\Services;

use App\Models\ExecStatus;
use App\Models\TaskWait;
use App\Models\Longestpath;

// eliminate via callback
// use App\Services\PrecedenceService;

use DB;
use Validator;


/*
 *
 *  NOTE: 'exec_notexecuting' (==0) isn't recognized by ExecStateMachineService; if a task is in this state it's not managed by ExecStateMachineService
 */


class ExecStateMachineService
{

    /* external events:
     *  // user = task=0
     *  'add_task', newTask
     *  'parent_child_add', parent, child
     *  'parent_child_remove', parent, child
     *  'add_dependency', taskFirst, taskNext
     * 
     *  'user_start', task - assuming in waiting state
     *  'user_done', task - assuming in active state
     */     
    public static function event($event, $taskIdPrimary, $taskIdSecondary)
    {
        switch ($event) {
            case 'add_task':
                ExecStatus::setTaskExecState($taskIdPrimary, 'exec_waiting');
                TaskWait::addTaskWait($taskIdPrimary, 'exec_active', 'user_done', $taskIdPrimary);         
                return [ 'errors' => null, ];
                break;
                
            case 'parent_child_add':
                if ($taskIdPrimary && $taskIdSecondary) {
                    TaskWait::addTaskWait($taskIdSecondary, 'exec_waiting', 'exec_active', $taskIdPrimary);
                    TaskWait::addTaskWait($taskIdPrimary, 'exec_active', 'exec_done', $taskIdSecondary);
                }
                elseif (!$taskIdPrimary) {
                    TaskWait::addTaskWait($taskIdSecondary, 'exec_waiting', 'user_start', $taskIdSecondary);
                }
                return [ 'errors' => null, ];
                break;
            
            case 'add_dependency':
                TaskWait::addTaskWait($taskIdSecondary, 'exec_waiting', 'exec_done', $taskIdPrimary);         
                return [ 'errors' => null, ];
                break;
                
        // execution events        
                
            case 'user_start':
                return ExecStateMachineService::stateMachineEvent('user_start', $taskIdPrimary);
                break;
            
            case 'user_done':
                return ExecStateMachineService::stateMachineEvent('user_done', $taskIdPrimary);
                break;
            
        }
    }
    
    public static function isActiveAction($taskId)
    {
        $execStatus = ExecStatus::where('task_id', $taskId)->first();
        if ($execStatus) {
            return $execStatus->is_active_action;
        }
        else {
            return false;
        }
    }
    
    public static function longestPaths($fromId, $toId)
    {
        if ($fromId == $toId) {
            return ExecStateMachineService::longestPathsToTaskDone($fromId);
        }
        
        $cachedPaths = Longestpath::longestPathsCached($fromId, $toId);
        if ($cachedPaths) { // if previously cached
            return $cachedPaths;
        }
        // here => not chached
        
        // calculate recursively with initial empty path         
        $longestPaths = ExecStateMachineService::longestPathsRec($fromId, $toId, array(), array());
        
        Longestpath::cacheLongestPaths($fromId, $toId, $longestPaths);
        
        return $longestPaths;
    }            
    
             
    private static function longestPathsRec($fromId, $toId, $pathDoneTillNow, $pathAllStarted)
    {
        // $pathDoneTillNow only includes those which were passed done; started are not stored
        
        // TOFIX - not sure caching is correct here
        $cachedPaths = Longestpath::longestPathsCached($fromId, $toId);
        if ($cachedPaths) { // if previously cached
            return $cachedPaths;
        }
        
        $thisId = $fromId;
        $thisVisitedFirstTime = !in_array($thisId, $pathAllStarted);
        $pathAllStartedUpdated = $pathAllStarted;
        array_push($pathAllStartedUpdated, $thisId);
        
        $thisToDone = ExecStateMachineService::longestPathsToTaskDone($thisId);
        $thisEmpty = ['longestAttention' => 0, 'longestDuration' => 0, 'longestAttentionPath' => array(), 'longestDurationPath' => array(), ];
        
        // now check all tasks which wait for this to be done and append the longest path from them
        
        $allWaitingForDone = TaskWait::taskWaitingAll('exec_done', $thisId);
        $allWaiting = $allWaitingForDone->merge(TaskWait::taskWaitingAll('exec_active', $thisId));  
            
        $longestAttentionNext = 0;
        $longestDurationNext = 0;
        $longestAttentionPathNext = array();
        $longestDurationPathNext = array();
        
        $pathDoneTillNowWithThis = $pathDoneTillNow;
        array_push($pathDoneTillNowWithThis, $thisId);

        foreach ($allWaiting as $waiting) {
            $waitingTaskId = $waiting->task_id;
            
            if (!in_array($waitingTaskId, $pathDoneTillNow)) {  // if not already visited 
                
                if ($waiting->for_event == 'exec_done') { 
                    // counting this as done 
                    $longestPathsNext = ExecStateMachineService::longestPathsOptionRec($waitingTaskId, $toId, $pathDoneTillNowWithThis, $pathAllStartedUpdated, $thisToDone);
                }
                else {
                    // $waitingTaskId is waiting to this be activated; it was already "activated"
                    if ($thisVisitedFirstTime) {
                        $longestPathsNext = ExecStateMachineService::longestPathsOptionRec($waitingTaskId, $toId, $pathDoneTillNow, $pathAllStartedUpdated, $thisEmpty);
                    }
                    else 
                        $longestPathsNext = null;
                }

                if ($longestPathsNext) {
                    if ($longestPathsNext['longestAttention'] > $longestAttentionNext) {
                        $longestAttentionNext = $longestPathsNext['longestAttention'];
                        $longestAttentionPathNext = $longestPathsNext['longestAttentionPath'];
                    }

                    if ($longestPathsNext['longestDuration'] > $longestDurationNext) {
                        $longestDurationNext = $longestPathsNext['longestDuration'];
                        $longestDurationPathNext = $longestPathsNext['longestDurationPath'];
                    }
                }
            }
        }
        
        $longestPaths = [
            'longestAttention' => $longestAttentionNext,
            'longestDuration' => $longestDurationNext,
            'longestAttentionPath' => $longestAttentionPathNext,
            'longestDurationPath' => $longestDurationPathNext,
        ];
        
        Longestpath::cacheLongestPaths($fromId, $toId, $longestPaths);
        
        return $longestPaths;

    }
    
    
    private static function longestPathsOptionRec($waitingTaskId, $toId, $pathDoneTillNow, $pathAllStarted, $thisToDone)
    {
        $longestPathsRec = ExecStateMachineService::longestPathsRec($waitingTaskId, $toId, $pathDoneTillNow, $pathAllStarted);
        
        return [
            'longestAttention' => $longestPathsRec['longestAttention'] + $thisToDone['longestAttention'],
            'longestDuration' => $longestPathsRec['longestDuration'] + $thisToDone['longestDuration'],
            'longestAttentionPath' => array_merge($longestPathsRec['longestAttentionPath'], $thisToDone['longestAttentionPath']),
            'longestDurationPath' => array_merge($longestPathsRec['longestDurationPath'], $thisToDone['longestDurationPath']),
        ];

    }
    
    
    private static function longestPathsToTaskDone($taskId)
    {
        $thisToDone = PrecedenceService::getTaskPathData($taskId);
        return $thisToDone;
    }
     
    
    /*
     *  'user_start', task
     *  'user_done', task
     *  'exec_waiting', task - state transition to 
     *  'exec_active', task - state transition to 
     *  'exec_done', task - state transition to      * 
     */
    private static function stateMachineEvent($event, $id) 
    {
        $allWaiting = TaskWait::taskWaitingAll($event, $id);
        
        foreach ($allWaiting as $waiting) {
            $waitingTaskId = $waiting->task_id;
            $waitingState = $waiting->task_exec_state;
            $waitingTaskExecState = ExecStatus::getTaskExecState($waitingTaskId);

            if ($waitingTaskExecState == $waitingState) {
                // waiting task in its state was waiting for this event
                $waiting->delete();
                
                // if now no waits for this task for its state
                if (!TaskWait::taskHasWaits($waitingTaskId, $waitingState)) {
                    // change the task state
                    $waitingToState = $waitingState=='exec_waiting' ? 'exec_active' : 'exec_done';
                    
                    $result = ExecStateMachineService::stateChange($waitingTaskId, $waitingState, $waitingToState);
                    if ($result['errors']) {
                        return $result;
                    }                        
                }
                else {
                    if ($waitingState == 'exec_active') {
                        $isActiveAction = ExecStateMachineService::updateTaskIsActiveAction($waitingTaskId);
                            // NOTE - when called to update event in PrecedenceService::eventNewAction from within change-state, caused bug in incorrect longestPath calculation
                    }
                }
            }
        }
        
        return [ 'errors' => null, ];
    }
    
    
    private static function stateChange($taskId, $fromState, $toState)
    {
        if ($fromState=='exec_waiting' && $toState=='exec_active') {
            // ok - nothing to do, continue below
            // NOTE - waits may exist
        }
        elseif ($fromState=='exec_active' && $toState=='exec_waiting') {
            // ok - nothing to do, continue below
        }
        elseif ($fromState=='exec_active' && $toState=='exec_done') {
            // ok - nothing to do, continue below
            // NOTE - waits may exist
        }
        else { 
            // illegal transition
            return [ 'errors' => "Error: illegal state transition - from " . $fromState . " to " . $toState . "for " . $taskId ];
        }
        // below - legal state transition
            
        $taskExecState = ExecStatus::getTaskExecState($taskId);

        if ($taskExecState != $fromState) {
            return [ 'errors' => "Error: task " . $taskId . " is not in the state " . $fromState ];
        }
        
        if ($fromState=='exec_active' && $toState=='exec_waiting') {
            // ok - nothing to do, continue below
        }
        else {
            // may be waits - need to check 
            if (TaskWait::taskHasWaits($taskId, $fromState)) {
                // can't activize - task waits 
                return [ 'errors' => "Error: task " . $taskId . " has non-empty wait list from " . $fromState, ];
            }
        }
       
        // actual transition
        ExecStatus::setTaskExecState($taskId, $toState);
        
        $isActiveAction = ExecStateMachineService::updateTaskIsActiveAction($taskId);
        // NOTE - when called to update event in PrecedenceService::eventNewAction from within change-state, caused bug in incorrect longestPath calculation
        
        // emit event of the transition
        $status = ExecStateMachineService::stateMachineEvent($toState, $taskId);
        
        return $status;
    }

    
    private static function updateTaskIsActiveAction($taskId)
    {
        $execStatus = ExecStatus::where('task_id', $taskId)
                ->first();
        
        if ($execStatus) {
            if ($execStatus->exec_state != 'exec_active') {
                $isActiveAction = false;
            }
            else {
                // Action === if active and the only wait is for user_done
                $taskWaits = TaskWait::taskWaitsAll($taskId, 'exec_active');
                
                if ($taskWaits->count() != 1) {
                    $isActiveAction = false;
                }
                else {
                    $taskWait = $taskWaits->first();
                    if ($taskWait->for_event=='user_done' && $taskWait->for_id==$taskId) {
                        $isActiveAction = true;
                    }
                    else {
                        $isActiveAction = false;
                    }
                }
            }
        }
        else {
            // shouldn't be here
        }
        
        $execStatus->update(['is_active_action' => $isActiveAction]);
        return $isActiveAction;
    }

    
}
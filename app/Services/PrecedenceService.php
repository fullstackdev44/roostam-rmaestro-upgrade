<?php

namespace App\Services;

use DB;
use Validator;

use App\Models\Task;
use App\Models\Property;
use App\Models\PrecedenceData;
use App\Services\ExecStateMachineService;


class PrecedenceService
{

    public static function isActiveAction($task)
    {
        return ExecStateMachineService::isActiveAction($task->id);
    }
    
    public static function updatePrecedenceDataList($taskList, $isRecursively)
    {
        // TOFIX: ONLY UPDATE if needed
            
        if ($taskList instanceof Task) { 
            // single task
            $task = $taskList;
            self::_updatePrecedenceDataList($task, $isRecursively);
        }
        else {
            // array or Collection
            foreach ($taskList as $key => $task) {
                self::_updatePrecedenceDataList($task, $isRecursively);
            }
        }
        // no return value; DB is updated 
    }
    
    
    private static function _updatePrecedenceDataList($task, $isRecursively)
    {
        if (self::isActiveAction($task)) {
            // update scheduling data of the action.
            // NOTE - now assuming it's a local per-action calculation
            self::updatePrecedenceData($task);     
        }
        
        if ($isRecursively) {
            $children = $task->children;
            foreach ($children as $child) { 
                self::_updatePrecedenceDataList($child, true);
            }
        }

        // no return value; DB is updated 
    }
            
    
    public static function updatePrecedenceData($action)            
    {
        $precedenceData = array();
        
        // NOTE - priority and deadline are used only of the root
        $rootTaskId = $action->getRootId();
        $rootTask = Task::find($rootTaskId);
        
        $priority = $rootTask->property->importance;
        $deadline = strtotime($rootTask->property->deadline);

        if ($deadline < 10000000) { // deadline is undefined
            $precedenceData['deadline_state'] = 0;
            $precedenceData['attention_state'] = 0;
            $precedenceData['duration_state'] = 0;
            $precedenceData['path_attention_state'] = 0;
            $precedenceData['path_duration_state'] = 0;
            $precedenceData['urgency'] = 0;
            $precedenceData['exec_prio'] = $priority;
            $precedenceData['attention_path_to_deadline'] = "";
            $precedenceData['duration_path_to_deadline'] = "";
            $precedenceData['suggested_delay'] = 2*24*60;
            $precedenceData['exec_prio_message'] = "";
          /*
            $message = "Urgency is zero since no deadline defined.";
            $recommendation = "You may want to define deadline and estimated required attention and duration so they can be taken into account to adjust the priority.";
          */  
        }
        else {
            $now = time();

            $timeToDeadline = round(($deadline - $now) / 60); // in minutes
            $attentionRemaining = $action->property->attention_average;
            $durationRemaining = $action->property->duration_average;            
            $attentionRange = $attentionRemaining ? $attentionRemaining : 2*60;  // 2 hours
            $durationRange = $durationRemaining ? $durationRemaining : 2*24*60;  // 2 days
            
            $longestPaths = self::longestPaths($action->id, $rootTask->id);
            $precedenceData['attention_path_to_deadline'] = implode(',',$longestPaths['longestAttentionPath']);
            $precedenceData['duration_path_to_deadline'] = implode(',',$longestPaths['longestDurationPath']);
            
            $pathAttentionRemaining = $longestPaths['longestAttention'];
            $pathDurationRemaining = $longestPaths['longestDuration'];     
            $pathAttentionRange = $pathAttentionRemaining ? $pathAttentionRemaining : 2*60; // 2 hours
            $pathDurationRange = $pathDurationRemaining ? $pathDurationRemaining : 2*24*60;  // 2 days
            
            $message = "";
            
            // params: -1=farEnough; 0=normal, 1=approaching; 2=tooClose; 3=passed; 4=passedTooFar
            $deadlineParams = [ 'farEnough'=>-1, 'approaching'=>max(4*24*60, 3*$durationRemaining), 'tooClose'=>min(2*24*60, 2*$durationRemaining), 'passedTooFar'=>-2*24*60 ];
            $attentionParams = [ 'farEnough'=>-1, 'approaching'=>2*$attentionRange, 'tooClose'=>round($attentionRange/2), 'passedTooFar'=>-round($attentionRange/3), ]; 
            $durationParams = [ 'farEnough'=>4*$pathDurationRange, 'approaching'=>$durationRange, 'tooClose'=>round($durationRange/3), 'passedTooFar'=>-round($durationRange/3), ]; 
            $pathAttentionParams = [ 'farEnough'=>-1, 'approaching'=>2*$pathAttentionRange, 'tooClose'=>round($pathAttentionRange/2), 'passedTooFar'=>-round($pathAttentionRange/3), ]; 
            $pathDurationParams = [ 'farEnough'=>4*$pathDurationRange, 'approaching'=>$pathDurationRange, 'tooClose'=>round($pathDurationRange/3), 'passedTooFar'=>-round($pathDurationRange/3), ];
            
            $tmpData = self::calculateUrgencyState($timeToDeadline-$pathAttentionRemaining, $pathAttentionParams);
            $precedenceData['path_attention_state'] = $tmpData['urgencyState'];
            $message .= "Deadline vs reamining critical path attention is " . $tmpData['message'] . ". ";
            
            $tmpData = self::calculateUrgencyState($timeToDeadline-$pathDurationRemaining, $pathDurationParams);
            $precedenceData['path_duration_state'] = $tmpData['urgencyState'];
            $message .= "Deadline vs reamining critical path duration is " . $tmpData['message'] . ". ";

            $tmpData = self::calculateUrgencyState($timeToDeadline, $deadlineParams);
            $precedenceData['deadline_state'] = $tmpData['urgencyState'];
            $message .= "Deadline vs now is " . $tmpData['message'] . ". ";
                            
            $tmpData = self::calculateUrgencyState($timeToDeadline-$attentionRemaining, $attentionParams);
            $precedenceData['attention_state'] = $tmpData['urgencyState'];
            $message .= "Deadline vs remaining attention of this action is " . $tmpData['message'] . ". ";

            $tmpData = self::calculateUrgencyState($timeToDeadline-$durationRemaining, $durationParams);
            $precedenceData['duration_state'] = $tmpData['urgencyState'];
            $message .= "Deadline vs remaining duration of this action is " . $tmpData['message'] . ". ";
            
            // boost is between 1..2
            $pathComplexityBoost = self::calculatePathComplexityBoost($longestPaths);
            
            // overall urgency in -1..4
            $urgency = $precedenceData['path_attention_state'] / 3 * $pathComplexityBoost
                    + $precedenceData['path_duration_state'] / 4 * $pathComplexityBoost
                    + $precedenceData['deadline_state'] / 4 
                    + $precedenceData['attention_state'] / 8
                    + $precedenceData['duration_state'] / 8;
            $urgency = $urgency<0 ? $urgency*12 : $urgency;
            
            $precedenceData['urgency'] = $urgency;
            $precedenceData['exec_prio'] = $priority + $urgency;
            
            $precedenceData['suggested_delay'] = self::calculateSuggestedDelay($priority, $timeToDeadline, $attentionRemaining, $durationRemaining, $pathAttentionRemaining, $pathDurationRemaining, $precedenceData);
            
            $message = "Action's precedence = " . round($precedenceData['exec_prio'],1) . " = " . round($priority,1) . " project's priority + " 
                    . round($precedenceData['urgency'],1) . " action's urgency. Urgency calculation: " . $message; 
            $message .= "path-attn: " . $precedenceData['path_attention_state'] . "; path-dur: " . $precedenceData['path_duration_state'] 
                    . "; ddl: " . $precedenceData['deadline_state'] . "; attn: " . $precedenceData['attention_state'] . "; dur: " . $precedenceData['duration_state'];
            $message .= " Longest attention path (" . $longestPaths['longestAttention'] . ") - " . implode(',',$longestPaths['longestAttentionPath']) 
            . ". Longest duration path: " . $longestPaths['longestDuration'] . ") - " . implode(',',$longestPaths['longestDurationPath']);
            $message .= " Path complexity boost: " . round($pathComplexityBoost,2);
            $precedenceData['exec_prio_message'] = $message;
        }
        
        $DBPrecedenceData = PrecedenceData::where('task_id', $action->id)
                        ->first();
        
        $DBPrecedenceData->update([ 
                'exec_prio' => $precedenceData['exec_prio'],
                'urgency' => $precedenceData['urgency'],
                'exec_prio_message' => $precedenceData['exec_prio_message'],
                'deadline_state' => $precedenceData['deadline_state'],
                'attention_state' => $precedenceData['attention_state'],
                'duration_state' => $precedenceData['duration_state'],
                'path_attention_state' => $precedenceData['path_attention_state'],
                'path_duration_state' => $precedenceData['path_duration_state'],
                'attention_path_to_deadline' => $precedenceData['attention_path_to_deadline'],
                'duration_path_to_deadline' => $precedenceData['duration_path_to_deadline'],
                'suggested_delay' => $precedenceData['suggested_delay'],
            ]);
    }
            

    public static function getTaskPathData($taskId)
    {
        $task = Task::where('tasks.id', $taskId)
                    ->join('properties', 'tasks.id', '=', 'properties.task_id')
                    ->first();
        
        /*
        if ($task->children->first()) { 
            // NOTE - has children -> ignore attention/duration setting, 
            $attention = 0;
            $duration = 0;
        }
        else { */
            $attention = $task->attention_average;
            $duration = $task->duration_average;
        //}
        
        return [
            'longestAttention' => $attention,
            'longestDuration' => $duration,
            'longestAttentionPath' => array( 0 => $taskId ),
            'longestDurationPath' => array( 0 => $taskId ),
        ];
    }
    
    
    private static function calculateUrgencyState($timeToDeadline, $params)
    {
        // [ $params= 'farEnough', 'approaching', 'tooClose', 'passedTooFar' ];
        if ($timeToDeadline <= $params['passedTooFar']) {
            $urgencyState = 4 + atan(($timeToDeadline-$params['passedTooFar']) / $params['passedTooFar'])/1.57;
            $message = "passed too long ago";
        }
        elseif ($timeToDeadline <= 0) {
            $urgencyState =  3 + $timeToDeadline/$params['passedTooFar'];
            $message = "passed";
        }
        elseif ($timeToDeadline <= $params['tooClose']) {
            $urgencyState =  1 + (1-$timeToDeadline/$params['tooClose']);
            $message = "too close";
        }
        elseif ($timeToDeadline <= $params['approaching']) {
            $approachDelta = $params['approaching'] - $params['tooClose'];
            $timeDelta = $timeToDeadline - $params['tooClose'];
            $urgencyState =  1-$timeDelta/$approachDelta;
            $message = "approaching";
        }
        else { // $timeToDeadline >$params['approaching']
            if ($params['farEnough']<0) { // no far-enough -> always norm=zero
                $urgencyState =  0;
                $message = "in normal range";
            }
            elseif ($timeToDeadline <= $params['farEnough']) {
                $urgencyState =  0;
                $message = "in normal range";
            }
            else {
                $urgencyState =  -atan(($timeToDeadline-$params['farEnough']) / $params['farEnough'])/1.57;
                $message = "far enough to relax";
            }
        }
            
        return [ 
            'urgencyState' => $urgencyState, 
            'message' => $message, 
        ];
    }
    
    
    private static function calculatePathComplexityBoost($longestPaths)
    {
        $pathAverageLength = count($longestPaths['longestAttentionPath']) + count($longestPaths['longestDurationPath']);
        
        if ($pathAverageLength<=1) { // simple/no path
            return 1;  
        }
        else {
            return 1 + atan($pathAverageLength / 5) /1.57 *0.7;
        }
    }
    
    
    private static function calculateSuggestedDelay($priority, $timeToDeadline, $attentionRemaining, $durationRemaining, $pathAttentionRemaining, $pathDurationRemaining, $precedenceData)
    {
        if ($precedenceData['urgency'] < 1) {
            return min(3*24*60, $pathDurationRemaining / (($priority+3)/3) );
        }
        elseif ($precedenceData['urgency'] > 3) {
            return 0;
        }
        else { 
            return min(4*60, 2*$attentionRemaining / (($priority+3)/3) );
        }
    }

    
    private static function longestPaths($fromId, $toId)
    {
        $longestPaths = ExecStateMachineService::longestPaths($fromId, $toId);
        $task = Task::where('tasks.id', $fromId)
                    ->join('properties', 'tasks.id', '=', 'properties.task_id')
                    ->first();
        
        // remove $fromId
        /*
        array_pop($longestPaths['longestAttentionPath']); 
        array_pop($longestPaths['longestDurationPath']); 
        $longestPaths['longestAttention'] -= $task->attention_average;
        $longestPaths['longestDuration'] -= $task->duration_average;
        */
        
        $longestPaths['longestAttentionPath'] = array_reverse($longestPaths['longestAttentionPath']);
        $longestPaths['longestDurationPath'] = array_reverse($longestPaths['longestDurationPath']);

        // NOTE - TODO improve the path calculation - removenon-leafs, etc
        return $longestPaths;
    }
    
    
    public static function createAndStoreTask($newTaskId)
    {
        PrecedenceData::create([
            'task_id' => $newTaskId,
            'exec_prio' => 0,
            'exec_prio_message' => '',
            'exec_recommendation' => '',
            'suggested_delay' => 0,
            'attention_path_to_deadline' => '',     // string, with '-' separator, 'this' is always first
            'duration_path_to_deadline' => '',     // string, with '-' separator, 'this' is always first
        ]);
        
    }


    public static function copyAndStoreTask($baseTaskId, $newTaskId)
    {
        $basePrecedenceData = PrecedenceData::where('task_id', $baseTaskId)
                                ->first();

        PrecedenceData::create([
            'task_id' => $newTaskId,  
            'exec_prio' => $basePrecedenceData->exec_prio,
            'exec_prio_message' => $basePrecedenceData->exec_prio_message,
            'exec_recommendation' => $basePrecedenceData->exec_recommendation,
            'suggested_delay' => $basePrecedenceData->suggested_delay,
            'attention_path_to_deadline' => $basePrecedenceData->attention_path_to_deadline,     
            'duration_path_to_deadline' => $basePrecedenceData->duration_path_to_deadline,     

        ]);
    }

}
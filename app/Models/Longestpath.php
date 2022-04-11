<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Longestpath extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'longestpaths';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = '_id';

    /**
     * the path is per (task_id_from, task_id_to, path_type)
     * the path is { index => index_id; }; index is from 1-..., path_type='a'/d' 
     * task_id_from isn't included; task_id_to is included
     * [index==0] => pathLength (hack to save calculation) 
     */
    protected $fillable = [
        'task_id_from',      
        'task_id_to',
        'path_type',
        'index',            
        'index_id',    
        'created_at',           
        'updated_at', 
    ];

    
    
    public function task()
    {
        return $this->belongsTo('App\Models\Task', 'task_id', 'id');
    }

    
    public static function cacheLongestPaths($fromId, $toId, $longestPaths)
    {
        // first erase previous if are
        $longestPathsAll = Longestpath::where('task_id_from', $fromId)
                                        ->where('task_id_to', $toId)
                                        ->get();
        
        foreach ($longestPathsAll as $item) {
            $item->delete();
        }
        
        // now cache new
        
        Longestpath::cachePath($fromId, $toId, $longestPaths['longestAttention'], $longestPaths['longestAttentionPath'], 'a'); 
        
        Longestpath::cachePath($fromId, $toId, $longestPaths['longestDuration'], $longestPaths['longestDurationPath'], 'd'); 

    }

    
    public static function longestPathsCached($fromId, $toId)
    {
        $longestPathAttention = Longestpath::where('task_id_from', $fromId)
                                            ->where('task_id_to', $toId)
                                            ->where('path_type', 'a')
                                            ->orderBy('index', 'asc')
                                            ->get();
       
        if (!$longestPathAttention->first()) { // wasn't cahced => return NULL
            return NULL;
        }
        
        // from here => were cached, so also duration needs to be 
        
        $longestPathDuration = Longestpath::where('task_id_from', $fromId)
                                            ->where('task_id_to', $toId)
                                            ->where('path_type', 'd')
                                            ->orderBy('index', 'asc')
                                            ->get();
        
        return [
            'longestAttention' => $longestPathAttention[0]->index_id,
            'longestDuration' => $longestPathDuration[0]->index_id,
            'longestAttentionPath' => Longestpath::restorePath($longestPathAttention),
            'longestDurationPath' => Longestpath::restorePath($longestPathDuration),
        ];
        
    }
    
    
    private static function cachePath($fromId, $toId, $length, $pathArray, $type)
    {
        Longestpath::create([
            'task_id_from' => $fromId,
            'task_id_to' => $toId,
            'path_type' => $type,
            'index' => 0,           
            'index_id' => $length,
        ]);
        
        foreach ($pathArray as $index => $taskId) {
            Longestpath::create([
                'task_id_from' => $fromId,
                'task_id_to' => $toId,
                'path_type' => $type,
                'index' => $index+1,           
                'index_id' => $taskId,
            ]);
        }
        
    }
                        
    
    private static function restorePath($longestPathCached)
    {
        $restoredPath = array();
        
        foreach ($longestPathCached as $index => $item) {
            if ($index != 0) { // skip 0, as stores length
                array_push($restoredPath, $item->index_id);                
            }
        }
        
        return $restoredPath;
    }
    
    
}

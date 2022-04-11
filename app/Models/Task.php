<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Task extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'user_id', 
        'parent_id', 
        'subtask_batch', 
        'is_executing'
    ];
    
    protected $appends = [
        'root_task', 
        'has_children'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function parent()
    {
        //return $this->belongsToOne(static::class, 'parent_id');
        return Task::where('tasks.id', $this->parent_id)
                    ->join('properties', 'tasks.id', '=', 'properties.task_id')
                    ->get()
                    ->first();
                    
    }

    public function getRootId()
    {
        if ( $this->parent_id == NULL ) {
            $rootId = $this->id; 
        }
        else {    
            $par = $this->parent();
            while ( $par->parent_id ) {
                $par = $par->parent();
            }
            
            $rootId = $par->id;
        }        
        
        return $rootId;
    }


    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')
                ->orderBy('subtask_batch');
    }
    

    public function property()
    {
        return $this->hasOne('App\Models\Property', 'task_id', 'id');
    }

    
    public function getRootTaskAttribute()
    {
        if ( $this->parent_id == NULL ) {
            return NULL;
        }
        else {    
            $par = $this->parent();
            while ( $par->parent_id ) {
                $par = $par->parent();
            }
                
            return $par;
        }
    }
    
    public function getHasChildrenAttribute()
    {
        return $this->children()->first() ? true : false;
    }

    
    protected static function boot() {
        parent::boot();

        static::deleting(function($task) {
             $task->children()->delete();
             $task->property()->delete();
        });
    }
}

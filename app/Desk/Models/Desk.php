<?php

namespace App\Desk\Models;

use App\Desk\Models;
use Illuminate\Database\Eloquent\Model;


class Desk extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'desks';

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
    protected $fillable = ['task_id', 'desk_image'];

    
    
    public function windows()
    {
        return $this->hasMany('App\Desk\Models\Window', 'desk_id', 'id') 
            ->orderBy('created_at', 'asc');
    }

    
    protected static function boot() {
        parent::boot();

        static::deleting(function($desk) {
            $desk->windows()->delete();
        });
    }

}

<?php

namespace App\Desk\Models;

use App\Desk\Models;
use Illuminate\Database\Eloquent\Model;


class Window extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'windows';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * type: 'project', 'note', 'browser', 'editor', 'chat', 
     * 'top', 'left', 'width', 'height' - percentage vs the desk available area - between 0..1
     * 
     */
    protected $fillable = ['desk_id', 'title', 'top', 'left', 'width', 'height', 'type', 'content', 'is_default'];

    
    
    public function desk()
    {
        return $this->belongsTo('App\Desk\Models\Desk', 'desk_id', 'id');
    }
}

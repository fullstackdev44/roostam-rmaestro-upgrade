<?php

namespace App\Desk\Services;

use App\Desk\Models\Desk;
use App\Desk\Models\Window;
use DB;
use Validator;


class DeskService
{

    public static function createAndStoreTask($newTaskId)
    {
        $desk = Desk::create([
            'task_id' => $newTaskId,
        ]);

        Window::create([
            'desk_id' => $desk->id,
            'title' => 'The Project',
            'top' => 0.01,
            'left' => 0.01,
            'width' => 0.28,
            'height' => 0.18,
            'type' => 'project',
            'content' => '',
            'is_default' => false,
        ]);

        Window::create([
            'desk_id' => $desk->id,
            'title' => 'Description',
            'top' => 0.01,
            'left' => 0.36,
            'width' => 0.28,
            'height' => 0.13,
            'type' => 'note',
            'content' => '',
            'is_default' => false,
        ]);

        Window::create([
            'desk_id' => $desk->id,
            'title' => 'Main chat',
            'top' => 0.21,
            'left' => 0.01,
            'width' => 0.23,
            'height' => 0.63,
            'type' => 'chat',
            'content' => '',
            'is_default' => false,
        ]);

        Window::create([
            'desk_id' => $desk->id,
            'title' => 'To-do',
            'top' => 0.21,
            'left' => 0.81,
            'width' => 0.18,
            'height' => 0.18,
            'type' => 'note',
            'content' => '',
            'is_default' => false,
        ]);

        Window::create([
            'desk_id' => $desk->id,
            'title' => 'Notes',
            'top' => 0.41,
            'left' => 0.81,
            'width' => 0.18,
            'height' => 0.33,
            'type' => 'note',
            'content' => '',
            'is_default' => true,
        ]);
        
    }
    
    
    public static function copyAndStoreTask($baseTaskId, $newTaskId)
    {
        $baseDesk = Desk::where('task_id', $baseTaskId)
                        ->first();
        
        $newDesk = Desk::create([
            'task_id' => $newTaskId,
            'desk_image' => $baseDesk->desk_image,
        ]);
                        
        foreach ($baseDesk->windows as $baseWindow)
        {
            Window::create([
                'desk_id' => $newDesk->id,
                'title' => $baseWindow->title,
                'top' => $baseWindow->top,
                'left' => $baseWindow->left,
                'width' => $baseWindow->width,
                'height' => $baseWindow->height,
                'type' => $baseWindow->type,
                'content' => $baseWindow->content,
                'is_default' => $baseWindow->is_default,
            ]);
        }
    }


    public static function getDesks($taskId)
    {
        $desk = Desk::where('task_id', $taskId)
                        ->first();
        
        $windows = $desk->windows;
        
        return [
            'desk' => $desk,
            'windows' => $windows,
            'errors' => null,
        ];
    }
    
    
    public static function newWindow($request)
    {
        $input = $request->only(
            'deskId', 'type'
        );
        
        if ($input['type'] == 'note') {
            $title = 'Notes';
            $content = '';
            $width = 0.38;
            $height = 0.28;
        }
        elseif ($input['type'] == 'browser') {
            $title = 'Browser';
            $content = 'http://www.google.com';
            $width = 0.48;
            $height = 0.38;
        }
        elseif ($input['type'] == 'project') {
            $title = 'The Project';
            $content = '';
            $width = 0.28;
            $height = 0.18;
        }
       
        $window = Window::create([
            'desk_id' => $input['deskId'],
            'title' => $title,
            'top' => 0.01,
            'left' => 0.01,
            'width' => $width,
            'height' => $height,
            'type' => $input['type'],
            'content' => $content,
            'is_default' => false,
        ]);
        
        return [
            'window' => $window,
            'errors' => null,
        ];
    }
    
    
    public static function updateWindow($request, $windowId)
    {
        $input = $request->only(
            'title', 'top', 'left', 'width', 'height', 'content', 'is_default'
        );
      
        $window = Window::find($windowId);
        
        $windowData = [
            'title' => $input['title'] ? $input['title'] : $window->title,
            'top' => $input['top'] ? $input['top'] : $window->top, 
            'left' => $input['left'] ? $input['left'] : $window->left, 
            'width' => $input['width'] ? $input['width'] : $window->width, 
            'height' => $input['height'] ? $input['height'] : $window->height, 
            'content' => $input['content'] ? $input['content'] : $window->content, 
            'is_default' => $input['is_default'] ? $input['is_default'] : $window->is_default,
        ];
        
        $window->update($windowData);
        
        return [
            'errors' => null,
        ];
    }
    
    public static function deleteWindow($windowId)
    {
        $window = Window::find($windowId);
        
        if ($window->type == 'chat') {
            // delete content for types which have content beyond embedded 'content'
        }
        
        $window->delete();
    }

    
}
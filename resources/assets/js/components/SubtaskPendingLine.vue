<template>
<div v-bind:class="['st-task-line', 
            !task.is_executing && hasChildren ? 'st-task-line-midtask' 
            : task.is_executing && task.exec_state=='exec_waiting' ? 'st-task-line-waiting'
            : task.is_executing && task.exec_state=='exec_done' ? 'st-task-line-done'
            : '' 
            ]" 
      style="display:flex; flex-direction:row; align-items:center;"
>
    <div style="display:flex; flex-direction:row; justify-content:space-between; align-items:center;" >

        <!-- icon, batch -->
        <div v-if="task.parent_id" style="flex: 0 0 6px; height:1px; background:grey;"></div>
        <div v-else style="flex: 0 0 6px; height:1px; color:grey;"></div>

        <task_batch style="flex: 0 0 10px;"
            v-bind:pBatch = "task.subtask_batch" 
            v-bind:task_id = "task.id"
            @refreshSubtasks="$emit('refreshSubtasks')"
        ></task_batch>

        <div v-if="task.parent_id" style="flex: 0 0 6px; height:1px; background:grey;"></div>
        <div v-else style="flex: 0 0 6px; height:1px; color:grey;"></div>

        <!--- exec_state icon --->
        <span style="flex: 0 0 28px; font-size:14px;">
            <i v-if="!task.is_executing" class="fa fa-square-o"></i>
            <i v-else-if="task.exec_state=='exec_active'" class="fa fa-caret-square-o-right" style="color: #a0a0a0"></i>
            <i v-else-if="task.exec_state=='exec_waiting'" class="fa fa-envelope-square"></i>
            <i v-else-if="task.exec_state=='exec_done'" class="fa fa-check-square"></i>
        </span>

        <span style="flex: 0 2 12px;"></span>

        <span style="flex: 0 0 80px;">
        </span>

        <span style="flex: 0 2 12px;"></span>

        <div style="flex: 0 1 180px; display:flex; flex-direction:row; align-items:center;">
            <task_name style="width:180px;"
                v-bind:pName = "task.name" 
                v-bind:task_id = "task.id"
            ></task_name>
        </div>

        <span style="flex: 0 2 12px;"></span>

        <div v-if="pSize!='short'"
            style="flex: 1 1 210px; display:flex; flex-direction:row; align-items:center;"
        >
            <span style="width: 15px;"></span> 
            <task_duration v-if="!hasChildren" style="display: flex; flex-direction: row; align-items: center;" 
                v-bind:pDuration = "task.duration_average" 
                v-bind:pAttention = "task.attention_average"
                v-bind:task_id = "task.id"
            ></task_duration>
        </div>

        <!--- controls --->
        <div v-if="pSize!='short'"
            style="flex: 1 0 140px;; display:flex; flex-direction:row; justify-content:flex-end; align-items:center;"
        >   

            <a v-if="!task.is_executing" href="javascript:void(0)" @click="$emit('copyTask')"> 
                <i class="fa fa-clone"></i> 
            </a> 

            <a v-if="!task.is_executing" href="javascript:void(0)" @click="$emit('deleteTask')"> 
                <i class="fa fa-trash-o"></i> 
            </a> 

            <a class="mbtn-do" @click="showDesk()" > 
                <span class="fa fa-desktop"></span> 
                <span>&nbsp Desk &nbsp</span>
            </a>

            <div v-if="showDeskModal" >
                <desk-modal
                    v-bind:pTask = "this.pTask"
                    @close="refreshView()"
                    @refresh="$emit('refresh')"
                ></desk-modal>
            </div>

        </div> 


    </div> 

</div> 
</template>



<script>
    export default {
        props: [
            'pTask',
            'pSize',
        ],

        data: function() { 
            return {
                task: this.pTask,
                editing: false,
                hasChildren: this.pTask.has_children,
                
                dom_id : "dom-id-task-view-" + this.pTask.id,
                tmp_new_task: {},
                constExecStates : constExecStates,

                showDeskModal: false,

            };
        },


        methods: {
            
            showDesk: function() {
                this.showDeskModal = true;
            },
            
            refreshView: function() {
                this.showDeskModal = false; 
            },

        }

    }
</script>



<style>


.st-task-children-line{
  font-family: arial, sans-serif; 
  font-size: 10px; 
  margin-top: 0px;
  margin-bottom: 3px;
  border-bottom: 1px solid lightgrey;
}


.st-task-line-waiting{
  background: #f5f5f5;
  color: grey;
}

.st-task-line-midtask{
  background: #f5f5f5;   
}

.st-task-line-done{
  background: #e0e0e0;
  color: grey;
  font-weight: normal;
  text-decoration: line-through;
}

.mbtn-control{
    height: 32px;
    display: flex; 
    flex-direction: column; 
    justify-content: center;    
    align-items: center; 
    padding: 1px 7px;  

    font-family: arial, sans-serif; 
    text-align: center;
    border: 1px solid #d2deee;
    border-radius: 4px;
    margin: 0px 5px 0px 0px;   

    font-size: 12px;
    color: #303030;
    font-weight: 500;
    cursor: pointer;
}

.mbtn-control:hover{
    background: #d2deee;
}

.mbtn-btn{
    display: flex; 
    flex-direction: column; 
    justify-content: center;    
    align-items: center;   

    font-family: arial, sans-serif; 
    text-align: center;
    border: 1px solid #d2deee;
    border-radius: 4px;
    padding: 2px 2px;   
    margin: 0px 3px 0px 0px;    

    font-size: 12px;
    color: #303030;
    font-weight: 500;
    cursor: pointer;
}
.mbtn-btn:hover{
    background: #d2deee;
}

</style>

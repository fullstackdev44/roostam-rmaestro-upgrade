<template>
<div v-bind:class="['st-task-line', hasChildren && !task.parent_id ? 'st-task-line-roottask' : '']" 
      style="display:flex; flex-direction:row; justify-content:space-between;"
>
    <div v-if="task.parent_id" style="width:16px; height:1px; background:grey;"></div>
    <div v-else style="width:16px; height:1px; color:grey;"></div>

    <div style="flex: 0 2 60px; display:flex; flex-direction:row; align-items:center;">

        <!--- exec_state icon --->
        <span style="font-size: 14px;">
            <i v-if="!task.is_executing" class="fa fa-square-o"></i>

            <span v-else-if="task.exec_state=='exec_active'" style="font-size: 14px;">
                <a class="mbtn-done" > 
                    <span class="fa fa-caret-square-o-right"></span>
                </a>
            </span>

            <i v-else-if="task.exec_state=='exec_waiting'" class="fa fa-envelope-square"></i>
            <i v-else-if="task.exec_state=='exec_done'" class="fa fa-check-square"></i>

        </span>

    </div> 

    <div style="flex: 1 3 200px; display:flex; flex-direction:row; align-items:center;">
        <task_name 
            v-bind:pName = "task.name" 
            v-bind:task_id = "task.id"
        ></task_name>
    </div>


    <div style="flex: 1 0 100px; display:flex; flex-direction:row; align-items:center; height:28px; background:#eef3f9;">
        <task_importance 
            v-bind:pImportance = "task.root_task ? task.root_task.importance : task.importance" 
            v-bind:task_id = "task.root_task ? task.root_task.id : task.id"
            v-bind:pShortView = 'false'
            @refresh="$emit('refresh')"
        ></task_importance>

        <task_deadline 
            v-if="pSize!='short'"
            v-bind:pDeadline = "task.root_task ? task.root_task.deadline : task.deadline" 
            v-bind:task_id = "task.root_task ? task.root_task.id : task.id"
            v-bind:pShortView = 'false'
            @refresh="$emit('refresh')"
        ></task_deadline>

    </div>

    <div 
        v-if="pSize!='short'"
        style="width:220px; display:flex; flex-direction:row; align-items:center;"
    >
        <span style="width: 15px;"></span> 
        <task_duration 
            v-if="pSize!='short' && !hasChildren" 
            style="display: flex; flex-direction: row; align-items: center;" 
            v-bind:pDuration = "task.duration_average" 
            v-bind:pAttention = "task.attention_average"
            v-bind:task_id = "task.id"
        ></task_duration>
    </div>

    <!--- controls --->

    <div v-if="pSize!='short'" style="width:180px; display:flex; flex-direction:row; justify-content:flex-end; align-items:center;">    
        
        <a href="javascript:void(0)" @click="$emit('copyTask')"> 
            <i class="fa fa-clone"></i> 
        </a> 
        &nbsp
        <a href="javascript:void(0)" @click="$emit('deleteTask')"> 
            <i class="fa fa-trash-o"></i> 
        </a> 
        &nbsp

        <a class="mbtn-do" @click="showDesk()" > 
            <span class="fa fa-desktop"></span> 
            <span>&nbsp Desk &nbsp</span>
        </a>

        <div v-if="showDeskModal" >
            <desk-modal
                v-bind:pTask = "this.pTask"
                @close="refreshView()"
                @refresh="emit('refresh')"
            ></desk-modal>
        </div>


        <!-- Do the task -->
        <span v-if="!task.is_executing && !task.parent_id">
            <do-task 
                v-bind:taskId = "task.id"
            ></do-task>
        </span>

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
                this.$emit('refresh');
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

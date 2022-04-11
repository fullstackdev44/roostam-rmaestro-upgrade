<template>
<div v-bind:class="['st-task-line', hasChildren && !task.parent_id ? 'st-task-line-roottask' : '']" 
      style="display:flex; flex-direction:column;"
>
    <div style="display:flex; flex-direction:row; justify-content:space-between; align-items:center;" >

        <!-- icon, batch -->
            <div v-if="task.parent_id" style="width:8px; height:1px; background:grey;"></div>
            <div v-else style="width:8px; height:1px; color:grey;"></div>

            <task_batch v-if="task.parent_id" style="width:12px;"
                v-bind:pBatch = "task.subtask_batch" 
                v-bind:task_id = "task.id"
            ></task_batch>
            <div v-else style="width:12px; height:1px; color:grey;"></div>


            <div v-if="task.parent_id" style="width:8px; height:1px; background:grey;"></div>
            <div v-else style="width:8px; height:1px; color:grey;"></div>

        <!--- exec_state icon --->
            <span style="width:28px; font-size:14px;">
                <a class="mbtn-done" @click="doneTask()"> 
                    <span class="fa fa-caret-square-o-right"></span>
                </a>
            </span>

            <span style="flex: 0 2 12px;"></span>

        <!--- status and precedence --->

            <span style="width:30px;">
                <action-status
                    v-bind:pTask = "task"
                ></action-status>
            </span>


            <span style="width:60px;">
                <precedence 
                    v-bind:pPrecedence = "task.exec_prio"
                    v-bind:pPriority = "task.root_task ? task.root_task.importance : task.importance"
                ></precedence>
                <q-tooltip> {{ task.exec_prio_message }} </q-tooltip>
            </span>

            <span style="flex: 0 2 12px;"></span>

        <!--- name and project --->
            <div v-bind:style="{ width: pSize=='short' ? '120px' : '360px', display:'flex', flexDirection:'row', alignItems:'center', }" >
                <div style="max-width:180px; display:flex; flex-direction:row;">
                    <task_name 
                        v-bind:pName = "task.name" 
                        v-bind:task_id = "task.id"
                    ></task_name>
                </div>

                <div v-if="pSize!='short'" style="flex: 1 2 20px; "></div>
                <div v-if="pSize!='short'" style="width:6px; "></div>

                <div v-if="pSize!='short'" style="display:flex; flex-direction:row; align-items:center;">
                    <span style="font-size:80%; color:lightgrey;">for</span>
                </div> 

                <div v-if="pSize!='short'" style="flex: 0.2 4 10px; "></div>
                <div v-if="pSize!='short'" style="width:6px;"></div>

                <div 
                    v-if="pSize!='short'"
                    style="max-width:160px; display:flex; flex-direction:row; height:28px; background:#eef3f9; border-bottom:1px solid #c0c0c0;"
                >
                    <div style="display:flex; flex-direction:row;">
                        <root-project style="display: flex; flex-direction: row;"
                            v-bind:pTask = "task"
                            @refresh="$emit('refresh')"
                        ></root-project>
                    </div>
                </div>
            </div>

    <!---
                <div style="width:220px; display:flex; flex-direction:row; align-items:center;">
                    <task_importance 
                        v-if="pSize!='short'"
                        v-bind:pImportance = "task.root_task ? task.root_task.importance : task.importance" 
                        v-bind:task_id = "task.root_task ? task.root_task.id : task.id"
                        v-bind:pShortView = 'true'
                        @refresh="$emit('refresh')"
                    ></task_importance>

                    <span style="width:7px;"></span> 

                    <task_deadline 
                        v-if="pSize!='short'"
                        v-bind:pDeadline = "task.root_task ? task.root_task.deadline : task.deadline" 
                        v-bind:task_id = "task.root_task ? task.root_task.id : task.id"
                        v-bind:pShortView = 'true'
                        @refresh="doRefresh()"
                    ></task_deadline>
                </div>
    -->
                
    
            <div 
                v-if="pSize!='short'"
                style="width:220px; display:flex; flex-direction:row; align-items:center;"
            >
                <span style="width: 15px;"></span> 
                <task_duration style="display: flex; flex-direction: row; align-items: center;" 
                    v-bind:pDuration = "task.duration_average" 
                    v-bind:pAttention = "task.attention_average"
                    v-bind:task_id = "task.id"
                ></task_duration>
            </div>
    

            <span style="flex: 3 1 20px;"></span>

        <!--- controls --->
            <div style="display:flex; flex-direction:row; justify-content:flex-end;">     

                <a v-if="pSize!='short'" style="flex-grow:1; flex-shrink:0;" class="mbtn-control" >
                    <span class="fa fa-share-square-o"></span>
                    <span> Delegate </span>
                </a>

        <!--
                <a v-if="pSize!='short'" style="flex-grow:1; flex-shrink:0;" class="mbtn-control" >
                    <span class="fa fa-calendar"></span> Calendar
                </a>
                <a v-if="pSize!='short'" style="flex-grow:1; flex-shrink:0;" class="mbtn-control" >
                    <span class="fa fa-clock-o"></span> Wait 
                </a>
        -->

                <a v-if="pSize!='short'" style="flex-grow:1; flex-shrink:0;" class="mbtn-control" >
                    <span class="fa fa-repeat"></span>
                    <span> Get Back </span>
                    <q-tooltip> {{ task.suggested_delay }} </q-tooltip>
                </a>

                <a class="mbtn-do" @click="showDesk()"> 
                    <span class="fa fa-desktop"></span> 
                    <span>&nbsp Do! &nbsp</span>
                </a>

                <div v-if="showDeskModal" >
                    <desk-modal
                        v-bind:pTask = "this.pTask"
                        @close="refreshView()"
                        @refresh="emit('refresh')"
                    ></desk-modal>
                </div>


            </div>

        </div>
 
    </div> 

    <!--
    <div style="display:flex; flex-direction:row; justify-content:space-between; align-items:center;" >
        <span style="font-size:11px; color:grey;">
            {{ task.exec_recommendation.substr(0,50) }}
        </span>

        <q-tooltip> {{ task.exec_recommendation }} </q-tooltip>
    </div>
    -->

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
            
            doRefresh: function () {
                this.$emit('refresh');
            },
            
            doneTask: function() {
                this.$http.get('/api/task-done/' + this.task.id).then(function (response) {
                });
                this.$emit('refresh');
            },

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

.mbtn-do{
    height: 32px;
    display: flex; 
    flex-direction: column; 
    justify-content: center;    
    align-items: center;   
    padding: 1px 12px;

    font-family: arial, sans-serif; 
    text-align: center;
    border: 1px solid #57cbbb;
    border-radius: 4px;
    margin: 0px 5px 0px 5px;   

    font-size: 12px;
    color: #05574c;
    font-weight: 600;
    cursor: pointer;
    background: #edf6fe;
}
.mbtn-do:hover{
    background: #98dad1;
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

.mbtn-done{
    display: flex; 
    flex-direction: column; 
    justify-content: center;    
    align-items: center;   

    font-family: arial, sans-serif; 
    text-align: center;
    border: 1px solid #57cbbb;
    border-radius: 4px;
    padding: 6px 6px;   
    margin: 0px 3px 0px 0px;    

    font-size: 12px;
    color: DodgerBlue;

    font-weight: 500;
    cursor: pointer;
    background: #edf6fe;
}
.mbtn-done:hover{
    background: #98dad1;
}

</style>

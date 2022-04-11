<template>
<div 
    v-if="pTask" 
    :id="dom_id" 
    style="display: flex; flex-direction: column; user-select: none;"
>
    <div >  

        
        <action-line 
            v-if="pTask.is_active_action"
            v-bind:pTask="this.pTask"
            v-bind:pSize="this.pSize"
            @refresh="$emit('refresh')"
        ></action-line>
        
        <project-line 
            v-else-if="!pTask.parent_id"
            v-bind:pTask="this.pTask"
            v-bind:pSize="this.pSize"
            @copyTask="copyTask()"
            @deleteTask="deleteTask()"
            @refresh="$emit('refresh')"
        ></project-line>

        <subtask-pending-line
            v-else
            v-bind:pTask="this.pTask"
            v-bind:pSize="this.pSize"
            @copyTask="copyTask()"
            @deleteTask="deleteTask()"
            @refreshSubtasks="$emit('refreshSubtasks')"
        ></subtask-pending-line>


        <!--- task children line -->
        <div style="display:flex; align-items:center; line-height:1.0; margin-top:0px; margin-bottom:0px; padding-top:0px; padding-bottom:0px;" >
            <div style="width: 2%"></div>

            <!--- children show/hide icon --->
            <show-task-children-control
                v-bind:pHasChildren = "hasChildren"
                v-bind:pIsShowChildren = "is_show_children"
                @showChildren="updateChildren(); is_show_children = true"
                @hideChildren="is_show_children = false"
            ></show-task-children-control>

            &nbsp

            <!--- create sub-task --->
            <span v-if="!task.is_executing"> 
                <a href="#" @click="createSubTask()">+</a>
            </span>

            &nbsp
            
            <span v-if="children && hasChildren && !is_show_children" style="font-size:80%; color:grey; ">
                {{ children.length }} sub-task(s)
            </span>

        </div>

        <!--- task children ---> 
        <div v-if="hasChildren" 
            style="display:flex; justify-content:flex-end;"
        >
            <div v-if="is_show_children" style="width: 97%;">
                <task 
                    v-for="(child, index) in children" 
                    v-bind:pTask="child"
                    v-bind:key="child.id"
                    v-bind:pSize="pSize"
                    @deleted="children.splice(index,1)"
                    @refreshSubtasks="updateChildren()"
                    @refresh="$emit('refresh')"
                ></task>
                <div class="clearfix"></div>
            </div>
            <div v-else="is_show_children" style="width: 97%; box-shadow: 0 1px 5px rgba(0,0,0,.2),0 2px 2px rgba(0,0,0,.14);">
                <div style="width:100%; height:1px;"></div>
            </div>
        </div>

    </div>
</div>
</template>



<script>
    export default {
        props: {
            pTask: Object,
            pSize: { type: String, default: 'normal' },
        },

        data: function() { 
            return {
                task: this.pTask,
                editing: false,
                hasChildren: this.pTask.has_children,
                children: null,
                is_show_children: this.pTask.parent_id ? true : false,
                
                dom_id : "dom-id-task-view-" + this.pTask.id,
                tmp_new_task: {},
                constExecStates : constExecStates,
            };
        },

        created: function() {
            if (this.pTask.has_children) {
                this.updateChildren();
            }
        },

        methods: {
            
            updateChildren: function() {
                this.$http.get('/api/task-children/' + this.task.id).then(function (response) {
                    this.children = response.data;
                    this.hasChildren = this.children.length ? true : false;                        
                });
            },


            createSubTask: function() {
                this.$http.get('/api/task-create', {params: {id: this.task.id}}).then(function (response) {
                    this.updateChildren();
                    this.is_show_children = true;
                });
            },

            copyTask: function() {
                this.$http.get('/api/tasks/copy-task/' + this.task.id).then(function (response) {
                    this.tmp_new_task = response.data.task;
                    this.$emit('refresh');
                });
            },

            deleteTask: function() {
                this.$http.delete('/api/tasks/' + this.task.id).then(function (response) {
                    //$('#' + this.dom_id).slideUp(300);
                    //$('#' + this.dom_id).remove();      
                    this.$emit('deleted');
                    this.$destroy();
                });
            },

        }

    }
</script>



<style>

.st-task-line{
    font-family: arial, sans-serif; 
    line-height: 1.2;
    margin-bottom: 3px;
    padding: 6px 12px 6px 0px;
    border-radius:2px;
    box-shadow: 0 1px 5px rgba(0,0,0,.2),0 2px 2px rgba(0,0,0,.14);
}

.st-task-children-line{
  font-family: arial, sans-serif; 
  font-size: 10px; 
  margin-top: 0px;
  margin-bottom: 3px;
  border-bottom: 1px solid lightgrey;
}

.st-task-line-subtask{
  background: #fff; 
}

.st-task-line-roottask{
  background: #eef3f9; 
}

.m-tcol-p1-start{
    width: 8%;
}
.m-tcol-p2{
    width: 70%;
}
.m-tcol-p5-controls{
    width: 22%;
}


.m-icol-name{
    width: 35%;
}
.m-icol-project{
    width: 20%;
}
.m-icol-p3{
    width: 220px;
}
.m-icol-p4{
    width: 160px;
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

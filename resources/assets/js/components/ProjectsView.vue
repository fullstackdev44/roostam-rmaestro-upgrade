<template>
<span>
    <div>
        <p class="create-task-btn">
            <a href="#" @click="createTask()">+ New Project</a>
        </p>
    </div>

    <task 
        v-for="item in list" 
        v-bind:pTask="item"
        v-bind:key="item.id"
        v-bind:pSize="pSize"
        @refresh="refresh()"
        style="margin-top:10px;"
    ></task>

</span>
</template>

<script>
    export default {
        props: {
            pSize: { type: String, default: 'normal' },
        },

        data: function() {
            return {
                list: [],
            };
        },

        created: function () {
            this.fetchTaskList();
        },


        methods: {
            fetchTaskList: function() {
                this.$http.get('/api/tasks-projects-view').then(function (response) {
                    this.list = response.data
                });
            },

            createTask: function() {
                this.$http.get('/api/task-create').then(function (response) {
                    this.list.unshift(response.data);
                });
            },

            refresh: function() {
                this.list = [];
                this.fetchTaskList(); 
            }

        }
    }
</script>


<style>

.create-task-btn{
  font-family: arial, sans-serif; 
  font: black bold;
  font-weight: 700;

  margin-top: 2px;
  padding-left: 10px;
  padding-top: 6px;
  padding-bottom: 6px;
}
.create-task-btn a{
  color: #9cb8da;
  font-weight: 600;
}
.create-task-btn:hover{
  background: #e6edf5; 
}
.create-task-btn:hover a{
  color: black;
}

</style>

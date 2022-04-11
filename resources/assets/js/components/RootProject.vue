<template>
<div style="display:flex; flex-direction:row; align-items:center;">    

    <div v-if="root" style="display:flex; flex-direction:row; align-items:center;">

        <span style="width:12px;"></span>

        <div class="st-task-name" 
            style="max-width:140px; cursor:pointer; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"
            @click="showProject()"
        >
            {{ root.name }}
        </div> 

        <span style="width:12px;"></span>

    </div>

    <div v-if="showModal" >
        <view-project-modal          
            v-bind:actionTaskId = "this.pTask.id"
            @close="refreshView()"
            @refresh="emit('refresh')"
        ></view-project-modal>
    </div>

</div>
</template>



<script>
    export default {
        props: ['pTask'],

        data: function() { 
            return {
                showModal: false,
                root: null,
            };
        },

        created: function() {
            this.root = this.pTask.root_task ? this.pTask.root_task : this.pTask;
        },

        methods: {     

            showProject: function() {
                this.showModal = true;
            },
       

            refreshView: function() {
                this.showModal = false; 
                this.$emit('refresh');
            },
        }

    }
</script>


<style>


</style>
<template>
<span>
    
    <div v-if="showModal" >
        <do-task-modal          
            v-bind:baseTaskId = "this.taskId"
            @close="showModal = false"
        ></do-task-modal>
    </div>

    <span class="st-do-task">
        <a href="javascript:void(0)" @click="doTask()">Do!</a>      
    </span>


</span>
</template>



<script>
    export default {
        props: ['taskId'],

        data: function() { 
            return {
                showModal: false,
            };
        },

        methods: {            

            doTask: function() {
                this.showModal = true;
            },
        }

    }
</script>


<style>

.st-do-task{
    text-align: center;
    text-transform: uppercase;
    padding: 0px 20px;
    border: 1px solid #57cbbb;
    display: inline-block;
    float: right;
    font-size: 12px;
    color: #000;
    font-weight: 600;
}

.st-do-task:hover{
    background: #00ff97;
}

</style>
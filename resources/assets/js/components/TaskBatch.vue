<template>
    <span class="st-task-name">
        <span 
            v-if="!isEditing" 
            @click="isEditing=true" 
            style="padding-left:2px; user-select: text;" 
        >
            <span v-if="batch=='*'" style="font-size:10px; color:grey;" class="fa fa-asterisk"></span>
            <span v-else style="font-weight:300; color:grey;"> {{ batch }} </span>
        </span>
        <input 
            v-else 
            v-model="batch" 
            @change="update()" @blur="update()" @keyup.enter="update()" @keyup.esc="update()"
            type="text" 
            style="width:12px; height:18px; padding-left:0px;" 
            v-autofocus
        />
    </span>
</template>

<script>
    export default {
        props: [
            'pBatch',
            'task_id',
        ],

        data: function() { 
            return {
                batch: this.pBatch==0 ? '*' : this.pBatch,
                isEditing: false,
            };
        },

 
        methods: {
            update: function() {
                this.batch = this.batch==0 ? '*' : this.batch;
                this.isEditing = false; 
                updateTaskAttr(this.task_id, 'subtask_batch', this.batch=='*'? 0 : this.batch );  
                this.$emit('refreshSubtasks');
            },

        }

    }
</script>

<style>

</style>


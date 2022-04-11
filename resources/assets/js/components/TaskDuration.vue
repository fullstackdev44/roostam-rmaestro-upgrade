<template>
<div class="st-task-normal" style="display: flex; flex-direction: row; align-items: baseline; padding-left: 5px; padding-right: 2px; border-left: 1px solid #c0c0c0;">

    <!--- attention --->
    <div class="st-task-normal" style="display: flex; flex-direction: row; align-items: baseline;">

        <span class="fa fa-hourglass-o" ></span>    
        <span 
            v-if="!isEditingAttention" 
            @click="isEditingAttention=true"      
            style="width:40px;"
        >
            {{ attention }} 
        </span>
        <input 
            v-else 
            v-model="attention" 
            @change="updateAttention()" @blur="updateAttention()" @keyup.enter="updateAttention()" @keyup.esc="updateAttention()"
            type="text" 
            style="width:40px; height:22px; padding-left:1px;" 
            v-autofocus
        />
    </div>

    <!--- duration --->
    <div class="st-task-normal" style="display: flex; flex-direction: row; align-items: baseline;">

        <span class="fa fa-hourglass" ></span>    
        <span 
            v-if="!isEditingDuration" 
            @click="isEditingDuration=true"      
            style="width:50px;"
        >
            {{ duration }} 
        </span>
        <input 
            v-else 
            v-model="duration" 
            @change="updateDuration()" @blur="updateDuration()" @keyup.enter="updateDuration()" @keyup.esc="updateDuration()"
            type="text" 
            style="width:50px; height:22px; padding-left:1px;" 
            v-autofocus
        />
    </div>
        

</div>
</template>

<script>

    export default {
        props: [
            'task_id',
            'pDuration',
            'pAttention',
        ],

        data: function() { 
            return {
                duration: this.pDuration,
                isEditingDuration: false,
                attention: this.pAttention,
                isEditingAttention: false,
            };
        },

 
        methods: {
            updateDuration: function() {
                this.isEditingDuration = false; 
                updateTaskAttr(this.task_id, 'duration_average', this.duration );  
            },

            updateAttention: function() {
                this.isEditingAttention = false; 
                updateTaskAttr(this.task_id, 'attention_average', this.attention );  
            },

        }

    }
</script>

<style>

</style>


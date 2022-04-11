<template>
    <span class="st-task-name" style="display:flex; flex-direction:row;">
       <span style="width:25px; color:lightgrey; font-weight:200; text-overflow:clip; overflow:hidden; white-space: nowrap;">
            {{ task_id }}
        </span>
        &nbsp

        <span 
            v-if="!isEditing" 
            @click="isEditing=true" 
            style="max-width:180px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; user-select:text;" 
        >
            {{ name }}
        </span>
        <input 
            v-else 
            v-model="name" 
            @change="update()" @blur="update()" @keyup.enter="update()" @keyup.esc="update()"
            type="text" 
            style="max-width:180px; height:18px; margin-left:-2px;" 
            v-autofocus
        />
    </span>
</template>

<script>
    export default {
        props: [
            'pName',
            'task_id',
        ],

        data: function() { 
            return {
                name: this.pName,
                isEditing: false,
            };
        },

 
        methods: {
            update: function() {
                this.isEditing = false; 
                updateTaskAttr(this.task_id, 'name', this.name );  
            },

        }

    }
</script>

<style>

</style>


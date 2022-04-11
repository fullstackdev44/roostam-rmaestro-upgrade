<template>
<div class="container" style="width:90%;">
    
    <div style="display:flex; flex-direction:row; justify-content:center;">
        <q-btn color="primary" @click="view_type='main'" big> Main View </q-btn>
        &nbsp &nbsp &nbsp
        <q-btn color="primary" @click="view_type='actions'" big> Actions </q-btn>
        &nbsp &nbsp &nbsp
        <q-btn color="primary" @click="view_type='projects'" big> Active Projects </q-btn>
        &nbsp &nbsp &nbsp
        <q-btn color="primary" @click="view_type='completed'" big> Completed Projects </q-btn>    
        &nbsp &nbsp &nbsp
        <q-btn color="primary" @click="view_type='templates'" big> Templates </q-btn>    
        &nbsp &nbsp &nbsp
        <q-btn @click="view_type='tasks'" big> All Tasks </q-btn>    
 
    </div>

    <div style="height:50px; width:100px;"></div>

    <main_view
        v-if="view_type==='main'"
    ></main_view>

    <actions_view
        v-if="view_type==='actions'"
    ></actions_view>

    <projects_view
        v-if="view_type==='projects'"
    ></projects_view>

    <templates_view
        v-if="view_type==='templates'"
    ></templates_view>

    <task_view
        v-if="view_type==='tasks'"
    ></task_view>

</div>                  
</template>



<script>

    export default {

        data: function() { 
            return {
                view_type: 'main',
            };
        },

    }

</script>


<style>

</style>


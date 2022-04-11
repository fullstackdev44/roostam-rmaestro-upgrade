<template>
<div >
    
    <div style="display:flex; flex-direction:row;" >
    
        <div style="display:flex; flex-direction:column; width:30%;" >
            <projects_view
                pSize="short"
            ></projects_view>
        </div>

        <div style="width:3%;"></div>

        <div style="display:flex; flex-direction:column; width:67%;" >
            <actions_view></actions_view>
        </div>

    </div>                  

</div>                  
</template>



<script>

    export default {

        data: function() { 
            return {
                view_type: 'actions',
            };
        },

    }

</script>


<style>

</style>


<template>
<div>

    <textarea  
        v-model="textContent" 
        v-bind:style="{ width:pContentWidth+'px', height:pContentHeight+'px', padding:'6px', resize:'none', fontSize:'14px', border:'none', }"  
        @change="updateContent()" @blur="updateContent()"
    > 
        {{ textContent }} 
    </textarea> 


</div>
</template>



<script>
    export default {
        props: [
            'pWindow',
            'pContentHeight',
            'pContentWidth',
        ],

        data: function() { 
            return {
                window: this.pWindow,
                textContent: this.pWindow.content,
            };
        },

        methods: {      

            updateContent: function() {
                this.$http.post('/api/desk-window-update/' + this.pWindow.id, {content:this.textContent}).then(function (response) {
                    //response.data;
                });

            },

        }

    }
</script>


<style>


</style>
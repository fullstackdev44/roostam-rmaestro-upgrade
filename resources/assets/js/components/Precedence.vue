<template>
<div style="display:flex; flex-direction:column;" >

    <div 
        v-bind:style="{ background:precedenceColor, width:precedenceWidth+'px', height:'5px', }"
    ></div>
    <div style="height:1px;"></div>

    <div 
        v-bind:style="{ background:priorityColor, width:priorityWidth+'px', height:'2px', }"
    ></div>
    <div style="height:1px;"></div>

    <div 
        v-bind:style="{ background:precedenceColor, width:precedenceWidth+'px', height:'5px', }"
    ></div>

</div>
</template>

<script>
    export default {
        props: [
            'pPrecedence',
            'pPriority',
        ],

        data: function() { 
            return {
                precedenceWidth: null,
                precedenceColor: null,
                priorityWidth: null,
                priorityColor: null,
            };
        },

        created: function() {
            this.precedenceWidth = Math.round((Math.max(this.pPrecedence,-2) +2) * 10);
            this.priorityWidth = Math.round((Math.max(this.pPriority,-2) +2) * 10);
            this.precedenceColor = this.pPrecedence<0 ? "gainsboro" : this.pPrecedence<2.4 ? "LightSlateGray" : this.pPrecedence<3.4 ? "sandybrown" : "salmon";
            this.priorityColor = this.pPriority<0 ? "gainsboro" : this.pPriority<2.4 ? "LightSlateGray" : this.pPriority<3.4 ? "sandybrown" : "salmon";
        },
 
        methods: {

        }

    }
</script>

<style>

</style>


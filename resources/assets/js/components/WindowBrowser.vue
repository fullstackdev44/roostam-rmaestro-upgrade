<template>
<div style="display:flex; flex-direction:column;">

    <input style="height:20px; background:#eee; color:grey;"
        v-model=link
        @change="updateContent()" @blur="updateContent()"
    />
 
    <iframe 
        v-bind:src = "link"
        v-bind:style="{ width:pContentWidth+'px', height:browserHeight+'px', background:'white', }" 
    >
        Can't load page: {{ link }}
        Try installing Chrome extension "iFrame Allow" https://chrome.google.com/webstore/detail/iframe-allow/gifgpciglhhpmeefjdmlpboipkibhbjg
    </iframe>

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
                link: this.pWindow.content,
            };
        },
        computed: {
            browserHeight: function () {
              // `this` points to the vm instance
              return this.pContentHeight - 20;
            }
        },

        methods: {      

            updateContent: function() {
                this.$http.post('/api/desk-window-update/' + this.pWindow.id, {content:this.link}).then(function (response) {
                    //response.data;
                });

            },

        }

    }
</script>


<style>


</style>
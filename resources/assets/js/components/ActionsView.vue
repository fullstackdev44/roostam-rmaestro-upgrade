<template>
<span>

    <action-line 
        v-for="item in list" 
        v-bind:pTask="item"
        v-bind:key="item.id"
        @refresh="refresh()"
        style="margin-bottom:6px;"
    ></action-line>

</span>
</template>


<script>
    export default {

        data: function() {
            return {
                list: [],
            };
        },

        created: function () {
            this.fetchActionsList();
        },

        beforeUpdate: function () {
            this;
        },
        
        updated: function () {
            this;
        },

        methods: {
            fetchActionsList: function() {
                this.$http.get('/api/tasks-action-view').then(function (response) {
                    this.list = response.data;
                });
            },

            refresh: function() {
                this.list = [];
                this.fetchActionsList(); 
            }
        }
    }
</script>


<style>

</style>

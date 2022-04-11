<template>
<span>
    <span style="font-size: 12px;">
        <span v-if="!pHasChildren">
            &nbsp 
        </span>
        <span v-else >
            <i 
                v-if="is_show_children" 
                href="javascript:void(0)"
                @click="hideChildren()"
                class="fa fa-minus-square-o"
            ></i>

            <i 
                v-else
                href="javascript:void(0)"
                @click="showChildren()"
                class="fa fa-plus-square-o"
            ></i>                     
        </span>
    </span>

</span>
</template>



<script>
    export default {
        props: [
                'pHasChildren',
                'pIsShowChildren',
            ],

        data: function() { 
            return {
            };
        },
        computed: {
            is_show_children: function () {
                return this.pIsShowChildren;
            }
        },

        methods: {            

            showChildren: function() {
                this.$emit('showChildren');
            },

            hideChildren: function() {
                this.$emit('hideChildren');
            },

        }

    }
</script>


<style>


</style>
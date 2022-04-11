<template>
<div class="st-task-normal" style="display: flex; flex-direction: row; align-items: baseline; padding-left: 5px; padding-right: 2px; border-left: 1px solid #c0c0c0;">
    <div class="st-task-normal" style="display: flex; flex-direction: row; align-items: center;">

        <span style="width:2px;"></span>

        <a class="mbtn-btn" href="#">
            <span @click="goDown()" class="fa fa-arrow-down" style="color:grey;"></span>
        </a>

        <vue-slider 
            ref="slider"
            v-model="label"
            width="50px"
            height="8"
            v-bind:dot-width="8"
            v-bind:dot-height="10"
            v-bind:min="-2"
            v-bind:max="5"
            v-bind:interval="1"
            tooltip="hover"
            v-bind:lazy="true"
            v-bind:data="['lowest','low','normal','high','very-high','top','NOW']"
            v-bind:processStyle="{backgroundColor:'sandybrown', }">
        ></vue-slider>

        <a class="mbtn-btn" href="#">
            <span @click="goUp()" class="fa fa-arrow-up" style="color:sandybrown;"></span>
        </a>

    </div>

</div>
</template>

<script>

    export default {
        props: [
            'pImportance',
            'task_id',
            'pShortView',
        ],

        data: function() { 
            return {    
                importance: this.pImportance,
                isEditing: false,
                label: null,
                color: null,
            };
        },

        created: function() {
            this.valueToLabel();
        },

        beforeUpdate: function() {
                this.update();
        },

        methods: {
            update: function() {
                this.isEditing = false; 
                this.labelToValue();
                updateTaskAttr(this.task_id, 'importance', this.importance );  
                this.$emit('refresh');
            },

            valueToLabel: function() {
                this.importance = this.pImportance;
                if (this.importance<-2) 
                    this.importance=-2;
                if (this.importance>5) 
                    this.importance=5;

                switch (Math.round(this.importance)) {
                    case -2: this.label='lowest'; this.color="lightgrey"; break;
                    case -1: this.label='low'; this.color="lightgrey"; break;
                    case 0: this.label='normal'; this.color="#404040"; break;
                    case 1: this.label='high'; this.color="sandybrown"; break;
                    case 2: this.label='very-high'; this.color="brown"; break;
                    case 3: this.label='top'; this.color="brown"; break;
                    case 4: this.label='NOW'; this.color="#b0b0b0b0"; break;
                }
            },

            labelToValue: function() {
                switch (this.label) {
                    case 'lowest': this.importance=-2; break;
                    case 'low': this.importance=-1; break;
                    case 'normal': this.importance=-0; break;
                    case 'high': this.importance=1; break;
                    case 'very-high': this.importance=2; break;
                    case 'top': this.importance=3; break;
                    case 'NOW': this.importance=4; break;
                 }
            },
            
            goUp: function() {
                switch (this.label) {
                    case 'lowest': this.label='low'; break;
                    case 'low': this.label='normal'; break;
                    case 'normal': this.label='high'; break;
                    case 'high': this.label='very-high'; break;
                    case 'very-high': this.label='top'; break;
                    case 'top': this.label='now'; break;
                }
                this.update();
            },

            goDown: function() {
                switch (this.label) {
                    case 'low': this.label='lowest'; break;
                    case 'normal': this.label='low'; break;
                    case 'high': this.label='normal'; break;
                    case 'very-high': this.label='high'; break;
                    case 'top': this.label='very-high'; break;
                    case 'now': this.label='top'; break;
                }
                this.update();      
            },

        }

    }
</script>

<style>

</style>


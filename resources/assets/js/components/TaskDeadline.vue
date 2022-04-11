<template>
<div class="st-task-normal" style="display: flex; flex-direction: row; align-items: baseline; padding-left: 5px; padding-right: 2px; border-left: 1px solid #c0c0c0;">

    <div class="st-task-normal" style="display: flex; flex-direction: row; align-items: center;">
        <span class="fa fa-calendar-check-o" ></span>

        <span style="width:76px;">
            {{ message }} 
        </span>
    </div>

    <div class="st-task-normal" style="display: flex; flex-direction: row; align-items: center;">

        <a v-if="!pShortView" class="mbtn-btn" href="#">
            <span @click="ahead()" class="fa fa-arrow-left"></span>
        </a>

        <a v-if="!message==''" class="mbtn-btn" href="#">
            <span @click="postpone()" class="fa fa-arrow-right"></span>
        </a>

        <span style="width:36px;"></span>

        <q-datetime 
            v-if="!pShortView"
            v-model="tmpDeadlineJSNumber" 
            type="date" 
            format="D/M YYYY"
            v-bind:minimal="true"
            v-bind:hide-underline="true"   
            placeholder="Click to set"
            @change="pickerChanged()"
        />
    </div>

</div>
</template>

<script>

    export default {
        props: [
            'pDeadline',
            'task_id',
            'pShortView',
        ],

        data: function() { 
            return {
                deadline: this.pDeadline,
                message: "",
                showDate: "",
                isEditing: false,
                deadlineJSNumber: null,
            };
        },

        created: function() {
            this.deadlineJSNumber = (new Date(this.pDeadline)).getTime();
            if (this.deadlineJSNumber < 31536000000) {
                this.deadlineJSNumber= null;
            }
            this.tmpDeadlineJSNumber = this.deadlineJSNumber;
            this.calculateMessage();
        },

        updated: function() {
            //this.deadlineJSNumber = this.tmpDeadlineJSNumber;
            //this.update();
        },

        methods: {

            calculateMessage: function() {
                var deadlineJS = this.deadlineJSNumber;
                if (this.deadlineJSNumber < 31536000000) { //=invalid date
                    this.message = "";
                    this.showDate = "Click to set";
                    this.deadlineJSNumber = null;
                }
                else {
                    var now = new Date();
                    var remainingTime = (deadlineJS - now)/60000; // in minutes

                    if (remainingTime <0) {
                        this.message = "Passed!";
                        this.showDate = this.deadline.substr(0, 10);
                    }
                    else if (remainingTime < 60) {
                        this.message = "in " +Math.round(remainingTime) + " min";
                        this.showDate = this.deadline.substr(11,5);
                    }
                    else if (remainingTime < 60*24) {
                        this.message = "in " +Math.round(remainingTime/60)+ " hours";
                        this.showDate = this.deadline.substr(11,5);
                    }
                    else if (remainingTime < 7*60*24) {
                        this.message = "in " +Math.round(remainingTime/60/24)+ " days";
                        this.showDate = this.deadline.substr(5, 11);
                    }
                    else if (remainingTime < 30*60*24) {
                        this.message = "in " +Math.round(remainingTime/60/24/7)+ " weeks";
                        this.showDate = this.deadline.substr(5, 5);
                    }
                     else {
                        this.message = "in " +Math.round(remainingTime/60/24/30)+ " months";
                        this.showDate = this.deadline.substr(0, 10);
                    }
                }
            },


            postpone: function() {
                var deadlineJS = this.deadlineJSNumber;
                var now = new Date();
                var remainingTime = (deadlineJS - now)/60000; // in minutes

                if (remainingTime <0) {
                    // TOFIX - suggest 
                }
                else if (remainingTime < 60) {
                    this.deadlineJSNumber = now.getTime() + (remainingTime + 10)*60000;
                }
                else if (remainingTime < 60*24) {
                    this.deadlineJSNumber = now.getTime() + (remainingTime + 60)*60000;
                }
                else if (remainingTime < 7*60*24) {
                    this.deadlineJSNumber = now.getTime() + (remainingTime + 1*60*24)*60000;
                }
                else if (remainingTime < 30*60*24) {
                    this.deadlineJSNumber = now.getTime() + (remainingTime + 7*60*24)*60000;
                }
                 else {
                    this.deadlineJSNumber = now.getTime() + (remainingTime + 30*60*24)*60000;
                }

                this.update();
            },

            pickerChanged: function() {
                this.deadlineJSNumber = this.tmpDeadlineJSNumber;
                this.update();
            },

            update: function() {
                var newDeadlineJS = new Date(this.deadlineJSNumber);
                this.deadline = newDeadlineJS.toISOString().substr(0,10) + ' ' + newDeadlineJS.toTimeString().substr(0,8);
                this.calculateMessage();
                this.tmpDeadlineJSNumber = this.deadlineJSNumber;

                this.isEditing = false; 
                updateTaskAttr(this.task_id, 'deadline', this.deadline ); 
                this.$emit('refresh');
                /*this.$http.put('/api/tasks/' + this.task_id, {params: {id: this.task_id, val: 'deadline', attr: this.deadline}}).then(function (response) {
                    this.$emit('refresh');
                }); */
            },

        }

    }
</script>

<style>

</style>


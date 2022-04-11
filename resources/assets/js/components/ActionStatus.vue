<template>
<div style="display:flex; flex-direction:row;" >
    <span style="width:8px;"></span>

    <span 
        class="fa fa-circle" 
        v-bind:style="{ color:color, fontSize:'70%', }"
    ></span>
    
    <q-tooltip> 
        {{ message  }} 
        <br/>
        {{ recommendation }} 
    </q-tooltip>
    
</div>
</template>



<script>
    export default {
        props: [
            'pTask',
        ],

        data: function() { 
            return {
                color: null,
                message: null,
                recommendation: null,
            };
        },

        created: function() {
            if (this.pTask.path_attention_state>3 || this.pTask.deadline_state>1.7) 
                this.color = "salmon";
            else if (this.pTask.path_attention_state>1 || this.pTask.path_duration_state>4 || this.pTask.deadline_state>1)
                this.color = "sandybrown";
            else 
                this.color = "DarkSeaGreen";

            // status message
            var deadlineStatuses = [ "", "Note: The deadline is approaching. ", "Warning: The deadline is TOO CLOSE. ", 
                                    "Warning: The deadline is DANGEROUSLY close! ", "Warning: YOU'RE BEHIND; the deadline HAS PASSED! ", ];
            var attentionStatuses = [ "", "Note: The deadline is approaching the total expected attention required for the critical path of this action. ", 
                                    "Warning: The deadline is VERY close to the total expected ATTENTION required for the critical path of this action. ",              
                                    "Warning: You start being behind; the deadline is CLOSER than the total expected ATTENTION required for the critical path of this action. ",  
                                    "Warning: YOU'RE BEHIND; the deadline is MUCH CLOSER than the total expected ATTENTION required for the critical path of this action. ", ];
            var durationStatuses = [ "", "Note: The deadline is approaching the total expected duration required for the critical path of this action. ", 
                                    "Warning: The deadline is VERY close to the total expected duration required for the critical path of this action. ",              
                                    "Warning: You start being behind; the deadline is CLOSER than the total expected duration required for the critical path of this action. ",  
                                    "Warning: YOU'RE BEHIND; the deadline is MUCH CLOSER than the total expected DURATION required for the critical path of this action. ", ];

            var deadlineStatusesIndex = Math.min(4, Math.max(0, Math.floor(this.pTask.deadline_state) ));
            var attentionStatusesIndex = Math.min(4, Math.max(0, Math.floor(this.pTask.path_attention_state) ));
            var durationStatusesIndex = Math.min(4, Math.max(0, Math.floor(this.pTask.path_duration_state) ));

            this.message = deadlineStatuses[deadlineStatusesIndex] + attentionStatuses[attentionStatusesIndex] + durationStatuses[durationStatusesIndex];
            if (this.message == "")
                this.message = "All good.";
            else 
                this.message = "Status: " + this.message;

            // recommendations 
            var doingRecoms = [ "", "", "You better start doing it right away. ", 
                                    "You should IMMEDIATELY start doing it. Or, reduce ATTENTION setting to reflect the fact you'll do fast and it will take less time.", 
                                    "You should IMMEDIATELY start doing it and MOVE FAST. Or, reduce ATTENTION setting to reflect the fact you'll do fast and it will take less time." ];
            var durationRecoms = [ "", "", "Consider trying to reduce duration by speeding up others that prolong duration. ", 
                                    "Try to REDUCE DURATION and make sure you speed up others that prolong duration. ", 
                                    "Try to REDUCE DURATION and make sure you work HARD to SPEED UP OTHERS that prolong duration. " ];
            var pathRecoms = [ "", "", "Consider paralleling things, i.e. start doing actions that currently are waiting for this action to finish. ", 
                                    "Try to PARALLEL things, i.e. start doing actions that currently are waiting for this action to finish. ", 
                                    "Try HARD to PARALLEL things, i.e. start doing actions that currently are waiting for this action to finish. ", ];
            var deadlineRecoms = [ "", "", "Consider postponing the deadline if possible. ",    
                                    "Postpone the deadline if possible. ", "Postpone the deadline if possible", ];
            
            var doingRecomsIndex = Math.min(4, Math.max(0, Math.floor(this.pTask.path_attention_state), Math.floor(this.pTask.deadline_state) ));
            var durationRecomsIndex = durationStatusesIndex;
            var pathRecomsIndex = Math.min(4, Math.max(0, Math.floor(this.pTask.path_attention_state), Math.floor(this.pTask.path_duration_state) ));
            var deadlineRecomsIndex = Math.min(4, Math.max(0, doingRecomsIndex, durationRecomsIndex) );

            this.recommendation = doingRecoms[doingRecomsIndex] + durationRecoms[durationRecomsIndex] + pathRecoms[pathRecomsIndex] + deadlineRecoms[deadlineRecomsIndex];
            if (this.recommendation != "") 
                this.recommendation = "How to fix it: " + this.recommendation;
        },

        methods: {            

        }

    }
</script>



<style>


</style>
<template>
  <transition name="modal">
    <div class="modal-mask" v-bind:style="{ zIndex:zIndex, }" >
      <div class="modal-wrapper">
        <div class="modal-container">

                <a  
                    style="float: right;" 
                    href="javascript:void(0)" 
                    @click="escape()"
                >&times;</a>

                <br>
                <br>

                <task 
                    v-if="newTask" 
                    v-bind:pTask = "newTask"
                ></task>

                <br>
                <br>

                <span class="st-do-task">
                    <a  
                        href="javascript:void(0)" 
                        @click="executeTask()"
                    >Do!</a>      
                </span>


          </div>
        </div>
      </div>
  </transition>
</template>


<script>
    export default {
        props: ['baseTaskId'],

        data: function() { 
            return {
                newTask: null,
                wasExecuted: false,
                zIndex: null,
            };
        },

        created: function() {
            this.zIndex = this.$getGlobalZIndexStack() + 1000;
            this.$setGlobalZIndexStack(this.zIndex);

            this.$http.get('/api/tasks/copy-task/' + this.baseTaskId).then(function (response) {
                this.newTask = response.data.task;
            });
        },

        destroyed: function() {
            this.$setGlobalZIndexStack(this.zIndex - 1000);
        },


        methods: {
            
            executeTask: function() {
                var newTaskId = this.newTask.id;
                this.newTask = null;
                this.$http.get('/api/tasks/execute-task/' + newTaskId).then(function (response) {
                    this.newTask = response.data.task;
                    this.wasExecuted = true;
                });
            },

            escape: function() {
                if (!this.wasExecuted) {
                    this.$http.delete('/api/tasks/' + this.newTask.id).then(function (response) {
                    });
                }
                this.$emit('close');
            },
            
       }

    }

</script>


<style>
.modal-mask {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 90%;
  min-height: 500px;
  margin: 0px auto;
  padding: 2px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-default-button {
  float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}


.st-do-task{
    text-align: center;
    text-transform: uppercase;
    padding: 0px 20px;
    border: 1px solid #57cbbb;
    display: inline-block;
    float: right;
    font-size: 12px;
    color: #000;
    font-weight: 600;
    float: right;
}

.st-do-task:hover{
    background: #00ff97;
}

</style>

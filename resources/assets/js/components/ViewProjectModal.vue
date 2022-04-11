<template>
  <transition name="modal">
    <div class="modal-mask" v-bind:style="{ zIndex:zIndex, }" >
      <div class="modal-wrapper">
        <div class="modal-container">

                <a  
                    style="float: right;" 
                    href="javascript:void(0)" 
                    @click="$emit('close')"
                >&times;</a>

                <br>
                <br>

                <task 
                    v-if="projectTask" 
                    v-bind:pTask = "projectTask"
                    @refresh="refresh()"
                ></task>

                <br>
                <br>



          </div>
        </div>
      </div>
  </transition>
</template>


<script>
    export default {
        props: ['actionTaskId'],

        data: function() { 
            return {
                projectTask: null,
                zIndex: null,
            };
        },

        created: function() {
            this.zIndex = this.$getGlobalZIndexStack() + 1000;
            this.$setGlobalZIndexStack(this.zIndex);

            this.refresh();
        },

        destroyed: function() {
            this.$setGlobalZIndexStack(this.zIndex - 1000);
        },


        methods: {
            refresh: function() {
                this.projectTask = null;
                this.$http.get('/api/tasks/get-root-task/' + this.actionTaskId).then(function (response) {
                    this.projectTask = response.data;
                });                
                //this.$emit('refresh');
            }
            
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



</style>

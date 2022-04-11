<template>
  <transition name="modal">
    <div class="desk-modal-mask" v-bind:style="{ zIndex:zIndex, }" >
      <div style="display:table-cell;  vertical-align:middle;">
        <div class="desk-modal-container" style="display:flex; flex-direction:column;">

            <div style="display:flex; flex-direction:row; heigth:52px;">
            
                <div style="display:flex; flex-direction:column; width:30%;" >
                    <project-line 
                        v-bind:pTask="this.project"
                        pSize="short"
                        @refresh="$emit('refresh')"
                    ></project-line>
                </div>

                <div style="display:flex; flex-direction:column; width:2%;" >
                </div>

                <div style="display:flex; flex-direction:column; width:66%;" >
                    <action-line 
                        v-if="task.is_active_action"
                        v-bind:pTask="this.task"
                        pSize="short"
                        @refresh="$emit('refresh')"
                    ></action-line>

                    <subtask-pending-line
                       v-else
                       v-bind:pTask="this.task"
                       pSize="short"
                   ></subtask-pending-line>

                </div>

                <div style="display:flex; flex-direction:column; width:2%;" >
                    <a  
                        href="javascript:void(0)" 
                        @click="$emit('close')"
                    >&times;</a>
                </div>

            </div>

            <div style="display:flex; flex-direction:row; height:40px; background:#ddd;">
                <span style="width:10px;"> </span>
                <a class="mbtn-control" @click="newWindow('note')" >
                    <span class="fa fa-sticky-note-o"></span>
                    <span> New notes </span>
                </a>       

                <span style="width:10px;"> </span>
                <a class="mbtn-control" @click="newWindow('browser')" >
                    <span class="fa fa-globe"></span>
                    <span> New browser </span>
                </a>                    

                <span style="width:10px;"> </span>
                <a class="mbtn-control" @click="newWindow('chat')" >
                    <span class="fa fa-comments-o"></span>
                    <span> New chat </span>
                </a>                    

                <span style="width:10px;"> </span>
                <a class="mbtn-control" @click="newWindow('project')" >
                    <span class="fa fa-tasks"></span>
                    <span> New project-window </span>
                </a>                    
            </div>

            <div v-if = "isReadyToRenderDesk"
                v-bind:id="domId"
                style="height:100%;"
            >
                <div v-if = "isReadyToRenderWindows"
                    v-bind:style="{ position:fixed, top:deskTop+'px', left:deskLeft+'px', width:deskWidth+'px', height:deskHeight+'px', }"
                >
                    <window
                        v-for="(item, index) in windowList" 
                        v-bind:pWindow="item"
                        v-bind:pTask="task"
                        v-bind:pProject="project"
                        v-bind:key="item.id"
                        v-bind:pDeskTop="deskTop"
                        v-bind:pDeskLeft="deskLeft"
                        v-bind:pDeskWidth="deskWidth"
                        v-bind:pDeskHeight="deskHeight" 
                        v-bind:pZIndex="zIndices[index]"
                        @active="activeWindow(index)"
                        @deleted="windowList.splice(index,1)"
                    ></window>
                </div>
            </div>

          </div>
        </div>
      </div>
  </transition>
</template>


<script>
    export default {
        props: [
            'pTask'
        ],

        data: function() { 
            return {
                task: this.pTask,
                project: null,
                desk: null,
                windowList: null,
                domId: null,
                isReadyToRenderDesk: false,
                isReadyToRenderWindows: false,
                deskTop: null,
                deskLeft: null,
                deskWidth: null,
                deskHeight: null, 
                zIndices: null,
                activeWindowIndex: -1,
                zIndex: null,
            };
        },

        created: function() {
            this.zIndex = this.$getGlobalZIndexStack() + 1000;
            this.$setGlobalZIndexStack(this.zIndex);

            this.project = this.pTask.root_task ? this.pTask.root_task : this.pTask;
        },

        mounted: function() {
            this.refresh();
        },

        updated: function() {
            if (this.isReadyToRenderDesk && !this.isReadyToRenderWindows) {
                var self = this;

                $(document).ready(function () {
                    var deskEl = $("#" + self.domId);
                    var position = deskEl.position();

                    self.deskTop = position.top;
                    self.deskLeft = position.left;
                    self.deskWidth = deskEl.width();
                    self.deskHeight = deskEl.height();
                    self.isReadyToRenderWindows = true;
                })
            }
        },

        destroyed: function() {
            this.$setGlobalZIndexStack(this.zIndex - 1000);
        },

        methods: {
            
            refresh: function() {
                this.isReadyToRenderDesk = false;
                this.isReadyToRenderWindows = false;
                
                this.$http.get('/api/get-desks/' + this.pTask.id).then(function (response) {
                    this.desk = response.data.desk;
                    this.windowList = response.data.windows;
                    this.domId = "desk-id-" + this.desk.id;

                    this.zIndices = new Array();
                    for (var i = 0; i < this.windowList.length; i++) { 
                        this.zIndices[i] = 'auto';
                    }

                    this.isReadyToRenderDesk = true;
                });                

            },
            

            newWindow: function(windowType) {
                this.$http.post('/api/desk-window-new', {deskId:this.desk.id, type:windowType}).then(function (response) {
                    this.windowList.push(response.data.window);
                });
            },

            activeWindow: function(index) {
                // this.zIndices[index]='999999';
                if (this.activeWindowIndex >= 0) { // when active window exists; -1 == doesn't
                    this.zIndices.splice(this.activeWindowIndex, 1, 'auto');
                }
                this.zIndices.splice(index, 1, '999999');
                this.activeWindowIndex = index;
            },
            
       }

    }

</script>


<style>
.desk-modal-mask {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.desk-modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.desk-modal-container {
  width: 98%;
  height: 98%;
  margin: 0px auto;
  padding: 6px 6px;
  background-color: #f0f0f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
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

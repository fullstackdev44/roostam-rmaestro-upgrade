<template>
<div class="draggable ui-resizable ui-draggable" 
    v-bind:style="{ position:'fixed', top:top+'px', left:left+'px', width:width+'px', height:height+'px', 
                    border:'1px solid Lavender', borderRadius:'2px', boxShadow:'0 1px 5px rgba(0,0,0,.2),0 2px 2px rgba(0,0,0,.14)',
                    zIndex:pZIndex, }"
    v-bind:id="domId"
    @mousedown="$emit('active')"
>
    <div class="draggable-handle" style="height:22px; background:Lavender; color:black; padding:2px 6px; display:flex; flexDirection:row; align-items:center;" >
        <span v-if = "pWindow.type == 'note'" class="fa fa-sticky-note-o"></span>
        <span v-if = "pWindow.type == 'browser'" class="fa fa-globe"></span>
        <span v-if = "pWindow.type == 'chat'" class="fa fa-comments-o"></span>

        <span style="width:10px;"></span>
        <span style="font-size:14px; font-weight:500;">{{ this.pWindow.title }} </span>

        <div style="display:flex; flexDirection:row; justify-content:flex-end;">
            <a href="javascript:void(0)" style="float:right;" @click="deleteWindow()">&times;</a>
        </div>
    </div>

    <div class="draggable-content" v-bind:style="{ height:contentHeight+'px', width:contentWidth+'px', background:'white', }">

        <div v-if = "pWindow.type == 'project'" v-bind:style="{ height:contentHeight+'px', width:contentWidth+'px', background:'white', 
                                                                overflow:'auto', display:'flex', flexDirection:'column', alignItems:'center', }">
            <div style="width:98%;"> 
                <div style="height:5px;">
                </div>
                <task
                    v-bind:pTask = "pProject"
                    pSize = "short"
                    @refresh = "refresh()"
                ></task>
            </div>
        </div>

        <window-note
            v-if = "pWindow.type == 'note'"
            v-bind:pWindow = "pWindow"
            v-bind:pContentHeight = "contentHeight"
            v-bind:pContentWidth = "contentWidth"
        ></window-note>

        <window-browser
            v-if = "pWindow.type == 'browser'"
            v-bind:pWindow = "pWindow"
            v-bind:pContentHeight = "contentHeight"
            v-bind:pContentWidth = "contentWidth"
        ></window-browser>

        <window-note
            v-if = "pWindow.type == 'chat'"
            v-bind:pWindow = "pWindow"
            v-bind:pContentHeight = "contentHeight"
            v-bind:pContentWidth = "contentWidth"
        ></window-note>

    </div>

</div>
</template>



<script>
    export default {
        props: [
            'pWindow',
            'pTask',
            'pProject',
            'pDeskTop',
            'pDeskLeft',
            'pDeskWidth',
            'pDeskHeight',
            'pZIndex',
        ],

        data: function() { 
            return {
                window: this.pWindow,
                domId: "window-id-" + this.pWindow.id, 
                top: null,
                left: null,
                width: null,
                height: null,
                contentHeight: null,
                contentWidth: null,
            };
        },

        created: function () {
            this.top = this.pDeskTop + this.window.top * this.pDeskHeight;
            this.height = this.window.height * this.pDeskHeight;
            this.left = this.pDeskLeft + this.window.left * this.pDeskWidth;
            this.width = this.window.width * this.pDeskWidth;
            this.contentHeight = this.height - 24;
            this.contentWidth = this.width -2;
        },

        mounted: function () {
            var self = this;
            $(document).ready(function () {
                var windowEl = $("#window-id-" + self.pWindow.id);

                windowEl.draggable({
                    handle: ".draggable-handle",
                    cancel: ".draggable-content",
                    containment: "parent", 

                    stop: function(event, ui) {
                        var stops = $(this).position();

                        self.window.top = (stops.top-self.pDeskTop) / self.pDeskHeight;
                        self.window.left = (stops.left-self.pDeskLeft) / self.pDeskWidth;

                        self.top = stops.top;
                        self.left = stops.left;

                        self.$http.post('/api/desk-window-update/' + self.pWindow.id, {top:self.window.top, left:self.window.left}).then(function (response) {
                            response.data;
                        });
                    }
                });

                windowEl.resizable({
                    resize: function( event, ui ) {
                        self.width = ui.size.width;
                        self.height = ui.size.height;
                        self.contentHeight = self.height - 24;
                        self.contentWidth = self.width -2;
                    },
                    stop: function(event, ui) {
                        self.width = ui.size.width;
                        self.height = ui.size.height;
                        self.contentHeight = self.height - 24;
                        self.contentWidth = self.width -2;

                        self.window.width = self.width / self.pDeskWidth;
                        self.window.height = self.height / self.pDeskHeight;

                        self.$http.post('/api/desk-window-update/' + self.pWindow.id, {width:self.window.width, height:self.window.height}).then(function (response) {
                            response.data;
                        });
                    }

                });

            })
        },

        methods: {            

            deleteWindow: function () {
                this.$http.get('/api/desk-window-delete/' + this.pWindow.id).then(function (response) {
                    this.$emit('deleted');
                });
            }

        }

    }
</script>


<style>


</style>
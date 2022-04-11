$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var app = new Vue({
      el: '#vue-app',
    })    
    
    var _globalZIndexStack = 0;
    
    Vue.prototype.$getGlobalZIndexStack = function() {
        return _globalZIndexStack;
    }
    
    Vue.prototype.$setGlobalZIndexStack = function(index) {
        _globalZIndexStack = index;
    }
    
    $('.switch-tasks-view').on('click', function() {
        $.ajax({
            type: "GET",
            data: {},
            url: '/api/tasks-tasks-view',
            beforeSend: function(msg){
            },
            success: function (data) {
                $('#main-view').html(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });


    $('.switch-action-view').on('click', function() {
        $.ajax({
            type: "GET",
            data: {},
            url: '/api/tasks-action-view',
            beforeSend: function(msg){
            },
            success: function (data) {
                /* TOFIX! */
                $('#main-view').html(data);
                /* code from maestro-last
                $('.create-task').remove();
                $('#main-app').html(templateActionView(data));
                setTimeout(function(){ 
                    sortUpdate();
                }, 1000); */
            },
            error: function (data) {
                console.log(data);
            }
        });
    });


});


function updateTaskAttr(id, attr, val) {
    $.ajax({  
        type: "PUT",
        url: '/api/tasks/' + id,
        data: {
            id: id,
            val: val,
            attr: attr,
        },
        success: function (data) {     
        },
        error: function (data) {  
        }
    });
}


// activate datepicker
function activateDatepicker() {
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true,
    });
}

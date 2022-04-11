
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

import Vue from 'vue';
//Vue.use(require('@hscmap/vue-window'))

Vue.component('vue-slider', require('./components/vue2-slider.vue')); 

Vue.component('main_view', require('./components/MainView.vue')); 
Vue.component('app_view', require('./components/AppView.vue')); 
Vue.component('task_view', require('./components/TaskView.vue')); 
Vue.component('projects_view', require('./components/ProjectsView.vue')); 
Vue.component('templates_view', require('./components/TemplatesView.vue')); 
Vue.component('actions_view', require('./components/ActionsView.vue')); 

Vue.component('task', require('./components/Task.vue')); 
Vue.component('action-line', require('./components/ActionLine.vue')); 
Vue.component('project-line', require('./components/ProjectLine.vue')); 
Vue.component('subtask-pending-line', require('./components/SubtaskPendingLine.vue')); 

Vue.component('action-status', require('./components/ActionStatus.vue')); 
Vue.component('precedence', require('./components/Precedence.vue')); 

Vue.component('task_name', require('./components/TaskName.vue')); 
Vue.component('task_batch', require('./components/TaskBatch.vue')); 
Vue.component('task_deadline', require('./components/TaskDeadline.vue')); 
Vue.component('task_importance', require('./components/TaskImportance.vue')); 
Vue.component('task_duration', require('./components/TaskDuration.vue')); 
Vue.component('do-task', require('./components/DoTask.vue')); 

Vue.component('show-task-children-control', require('./components/ShowTaskChildrenControl.vue')); 
Vue.component('do-task-modal', require('./components/DoTaskModal.vue')); 
Vue.component('view-project-modal', require('./components/ViewProjectModal.vue')); 
Vue.component('root-project', require('./components/RootProject.vue')); 

Vue.component('desk-modal', require('./components/DeskModal.vue')); 
Vue.component('window', require('./components/Window.vue')); 
Vue.component('window-note', require('./components/WindowNote.vue')); 
Vue.component('window-browser', require('./components/WindowBrowser.vue')); 


Vue.directive('autofocus', {
  // When the bound element is inserted into the DOM...
  inserted: function (el) {
    // Focus the element
    el.focus()
  }
})

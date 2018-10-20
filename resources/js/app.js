/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Notifications from 'vue-notification';

const axios = require('axios');

Vue.component('gantt', require('./components/gantt.vue'));
Vue.component('list', require('./components/list.vue'));
Vue.component('new-task', require('./components/new-task.vue'));
Vue.component('home', require('./components/home.vue'));

Vue.use(Notifications);

const app = new Vue({
    el: '#app'
});


import 'core-js/stable';
// eslint-disable-next-line import/no-extraneous-dependencies
import 'regenerator-runtime/runtime';
import Vue from 'vue';
import Vuex from 'vuex';
// store
import fm from './store';
// App
import App from './FileManager.vue';

window.Vue = require('vue');

Vue.use(Vuex);

import BootstrapVue from 'bootstrap-vue' //Importing
Vue.use(BootstrapVue);

import Form from "./utilities/Form";
window.Form = Form;

import VueSweetalert2 from 'vue-sweetalert2';

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.use(VueSweetalert2);




const store = new Vuex.Store({
    strict: process.env.NODE_ENV !== 'production',
    modules: { fm },
});



Vue.config.productionTip = process.env.NODE_ENV === 'production';

window.fm = new Vue({
    store,
    render: (h) => h(App),
}).$mount('#fm');


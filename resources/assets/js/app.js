import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import moment from 'moment';

import VueAxios from 'vue-axios'

import App from './views/App'

Vue.use(VueRouter,VueAxios, axios)
// Vue.use()

// import Hello from './views/Hello'
// import Home from './views/Home'
// import Transaction from './views/Transaction'

// import TransInfo from './components/TransInfo.vue'

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI)


import router from './router/index.js';

// const router = new VueRouter({
//     mode: 'history',
//     base: __dirname,
//     routes: [
//         { path: '/transfer/:stock_id/create', component: TransInfo , props: true }
//     ]
// })
// Vue.component('transfer-info',require('./components/TransInfo.vue'));
Vue.prototype.$moment = moment;


// new Vue(Vue.util.extend({ router }, App)).$mount('#app')
//
new Vue({
    el: '#app',
    router,
    render: h => h(App)
})
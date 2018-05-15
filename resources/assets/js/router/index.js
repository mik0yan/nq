import Vue from 'vue';
import VueRouter from 'vue-router';
// import TransInfo from '..components/TransInfo'
import Purchase from '../components/Purchase.vue';
import Ship from '../components/Ship.vue';
import Trans from '../components/Trans.vue';
import Lend from '../components/Lend.vue';
import Return from '../components/Return.vue';
import Loss from '../components/Loss.vue';
import Rework from '../components/Rework.vue';
import Repair from '../components/Repair.vue';

Vue.use(VueRouter);

export default new VueRouter({
    saveScrollPosition: true,
    mode: 'history',
    base: __dirname,
    routes: [
        { path: '/transfer/:stock_id/purchase', component: Purchase , props: true },
        { path: '/transfer/:stock_id/trans', component: Trans , props: true },
        { path: '/transfer/:stock_id/ship', component: Ship , props: true },
        { path: '/transfer/:stock_id/lend', component: Lend , props: true },
        { path: '/transfer/:stock_id/return', component: Return , props: true },
        { path: '/transfer/:stock_id/repair', component: Repair , props: true },
        { path: '/transfer/:stock_id/rework', component: Rework , props: true },
        { path: '/transfer/:stock_id/loss', component: Loss , props: true },
    ]
});


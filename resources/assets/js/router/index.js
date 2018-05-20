import Vue from 'vue';
import VueRouter from 'vue-router';
// import TransInfo from '..components/TransInfo'
import Purchase from '../components/Purchase.vue'; //采购
import Ship from '../components/Ship.vue';  //发货
import Trans from '../components/Trans.vue'; //调拨
import Lend from '../components/Lend.vue';  //借出
import Return from '../components/Return.vue';  //归还
import Loss from '../components/Loss.vue'; //损耗
import Rework from '../components/Rework.vue'; //返修
import Repair from '../components/Repair.vue'; //维修

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


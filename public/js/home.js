/**
 * Created by mikuan on 2018/4/6.
 */
// import Vue from 'vue'
// import Router from 'vue-router'
//
// Vue.use(Router)
// let router = new VueRouter({
//     routes: [
//         {path : '/transferview/:id',  name: 'transferview' },
//         {path : '/transferview/:id/edit', name: 'editTransfer'},
//     ]
// });
const app = new Vue({

    el: '#app',
    mounted:function () {
        this.getData(this.$el.baseURI.split('=')[1]);
        console.log(this.$el.baseURI.split('=')[1]);
    },
    data: {"v":{
        "title": "采购",
        "from": null,
        "to": {
            "id": 4,
            "name": "国药保税库",
            "address": "国药保税库",
            "user": "高晓茹"
        },
        "order": {
            "id": 10,
            "user": "杨鹏飞",
            "client": "温州市中心医院",
            "agent": "趋势传媒有限公司",
            "orderno": "12"
        },
        "user": "高晓茹",
        "track": {
            "invoice": "14DFA008400",
            "contract": "无",
            "track": null
        },
        "comment": null,
        "date": {
            "ship": "2014-12-22",
            "arrival": "2014-12-31"
        },
        "detail": [
            {
                "id": 17,
                "amount": 90,
                "product_id": 5,
                "product_name": "CARDIOVIT AT-102 心电图机标配+C",
                "core": true,
                "serials": [
                    {
                        "id": 40,
                        "serial": "070.12729",
                        "comment": null
                    },
                    {
                        "id": 41,
                        "serial": "070.12730",
                        "comment": null
                    },
                    {
                        "id": 42,
                        "serial": "070.12731",
                        "comment": null
                    },
                    {
                        "id": 43,
                        "serial": "070.12732",
                        "comment": null
                    },
                    {
                        "id": 1419,
                        "serial": "070.12815",
                        "comment": null
                    }
                ]
            }
        ]
    }},
    methods:{
        getData(id){
            axios.get('/quickline/'+id)
                .then((response)=>{
                    this.v = response.data;
                    console.log(response);

                })
                .catch(function (error) {
                    console.log(error);
                })
        }
    },
}).$mount('#app')

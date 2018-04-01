/**
 * Created by mikuan on 2018/3/18.
 */
/**
 * Created by mikuan on 2018/3/2.
 */


import VueRoute from 'vue-router'

let routes = [
    {
        path: '/',
        component: require('./components/pages/Home')
    },
    {
        path: '/about',
        component: require('./components/pages/About')
    },
    {
        path: '/posts/:id',
        name: 'posts',
        component: require('./components/posts/Post')
    }
]

export default new VueRoute({
    mode:'history',

    routes
})
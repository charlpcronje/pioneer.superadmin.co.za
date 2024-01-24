import VueRouter from 'vue-router';


let routes = [
    {
        path: '/',
        component: require('./views/dashboard').default
    },
    {
        path: '/dashboard',
        component: require('./views/dashboard').default
    },
    {
        path: '/login',
        component: require('./views/auth/login').default
    },
    {
        path: '/forgot-password',
        component: require('./views/auth/forgot_password').default
    },
    {
        path: '/recover-password',
        component: require('./views/auth/recovery_password').default,
        name: 'recovery_password'
    },
    {
        path: '/users/index',
        component: require('./views/users/index').default
    },
    {
        path: '/users/types',
        component: require('./views/users/types').default
    },
    {
        path: '/users/permissions',
        component: require('./views/users/permissions').default
    },
    {
        path: '/communications',
        component: require('./views/communications').default
    },
    {
        path: '/content-manager',
        component: require('./views/content').default,
    },
    {
        path: '/media-manager',
        component: require('./views/media').default,
    },
    {
        path: '/file-manager',
        component: require('./views/fm').default,
    },
    {
        path: '/file-links',
        component: require('./views/links').default,
    },

];


export default new VueRouter({
    base: '/',
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});

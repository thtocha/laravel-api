import './bootstrap';
import 'bootstrap/dist/css/bootstrap.css';
import '@popperjs/core/dist/cjs/popper';
import 'bootstrap/dist/js/bootstrap.bundle';


import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { useVuelidate } from '@vuelidate/core'

import App from "./components/App.vue";
import Home from "./components/Home.vue";
import Desks from "./components/desks/Desks.vue";
import ShowDesk from "./components/desks/ShowDesk.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/desks',
            name: 'desks',
            component: Desks
        },
        {
            path: '/desks/:deskId',
            name: 'showDesk',
            component: ShowDesk,
            props: true
        },
    ],
});

const app = createApp(App);
app.use(router);
app.use(useVuelidate);
app.component('app', App);
app.mount('#app');

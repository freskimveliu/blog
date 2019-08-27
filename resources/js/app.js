require('./bootstrap');
window.Vue = require('vue');

import App from './components/App';
import router from './router';
import axios from 'axios';
import store from './store';
import Swal from 'sweetalert2'
import mixins from './mixins';
import VueProgressBar from 'vue-progressbar'
import './filters'
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(fas)

Vue.component('font-awesome-icon', FontAwesomeIcon)


Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '2px'
});


axios.defaults.baseURL = 'http://localhost:8000/api';
axios.defaults.headers.post['Content-Type'] = 'application/json';

let newVue = new Vue({});

axios.interceptors.request.use(function (config) {
        newVue.$Progress.start();
        const token = localStorage.getItem('access_token');
        config.headers.Authorization =  token ? `Bearer ${token}` : '';
        return config
    },
    (error) => {
        return Promise.reject(error);
    }
);
axios.interceptors.response.use(function (response) {
    newVue.$Progress.finish();
    return response;
}, function (error) {
    newVue.$Progress.fail();
    switch (error.response.status) {
        case 401:{
            store.dispatch('logout');
            router.push('/login');
            break;
        }
        case 403:{
            Swal.fire({
                title: 'Not Allowed Page',
                text: "You aren't allowed to visit that page.",
                type: 'warning',
                confirmButtonText: "Okay",
            }).then((res) => {
                router.push('/');
            });
            break;
        }
        case 404: {
            router.push('/404')
        }
    }
    return Promise.reject(error.response);
});

const app = new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App),
    mixin: [mixins],
    created:function () {
        let access_token = store.state.access_token;
        if(access_token != null){
            store.dispatch('getUser');
        }

    }
});

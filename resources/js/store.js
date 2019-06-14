import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        access_token: localStorage.getItem('access_token') || null,
        is_logged: localStorage.getItem('access_token') != null,
        user: localStorage.getItem('user') || null,
        is_admin: false,
    },
    getters: {
        getAccessToken(state) {
            return state.access_token;
        },
        isLogged(state) {
            return state.access_token !== null;
        },
        isAdmin(state) {
            if (state.user == null) {
                return false;
            }
            return (JSON.parse(state.user).role) === 'admin'
        },
        user(state) {
            return JSON.parse(state.user);
        }
    },
    mutations: {
        storeAuthData(state, data) {
            state.access_token = data.access_token;
            state.is_logged = true;
            state.user = JSON.stringify(data.user);
            localStorage.setItem('access_token', data.access_token);
            if (state.user != null) {
                state.is_admin = (JSON.parse(state.user).role) === 'admin';
            }
            localStorage.setItem('user', JSON.stringify(data.user));
        },
        logout(state) {
            state.access_token = null;
            state.is_logged = false;
            state.is_admin = false;
            state.user = null;
            localStorage.clear();
        },
        updateUser(state, data) {
            state.user = JSON.stringify(data);
            localStorage.setItem('user', state.user);
        },
        storeAuthUser(state, data) {
            state.user = JSON.stringify(data);
            localStorage.setItem('user', state.user);
        }
    },
    actions: {
        login({commit}, data) {
            return new Promise((resolve, reject) => {
                axios.post('/login', data)
                    .then((res) => {
                        let response = res.data.data;
                        commit('storeAuthData', response);
                        resolve(response);
                    })
                    .catch((err) => {
                        reject(err);
                    })
            })

        },
        register({commit}, data) {
            return new Promise((resolve, reject) => {
                axios.post('/register', data)
                    .then((res) => {
                        let response = res.data.data;
                        commit('storeAuthData', response);
                        resolve(response);
                    })
                    .catch((err) => {
                        reject(err);
                    })
            })
        },
        logout(context) {
            return new Promise((resolve, reject) => {
                if (context.getters.isLogged) {
                    context.commit('logout')
                }
                resolve();
            })

        },
        updateUser({commit}, data) {
            return new Promise((resolve, reject) => {
                axios.post('/profile', data)
                    .then(res => {
                        let data = res.data.data;
                        commit('updateUser', data);
                        resolve(res)
                    })
                    .catch(err => {
                        reject(err);
                    })
            })
        },
        storeAuthUser({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/profile')
                    .then(res => {
                        commit('storeAuthUser', res.data.data);
                    })
                    .catch(err => {
                        reject(err);
                        console.log(err);
                    })
            })
        }
    }
});
export default store

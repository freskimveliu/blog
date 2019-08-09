import Vue from 'vue';
import Router from 'vue-router';
import not_found from './components/pages/errors/not_found';
import home from './components/pages/home/index';
import login from './components/pages/auth/login';
import logout from './components/pages/auth/logout';
import register from './components/pages/auth/register';
import profile from './components/pages/auth/profile';
import store from './store';
import posts from './components/pages/posts/index'
import postsShow from './components/pages/posts/show';
import usersShow from './components/pages/users/show';
import adminCategories from './components/pages/admin/categories/index'
import adminCreateCategories from './components/pages/admin/categories/create'
import adminEditCategories from './components/pages/admin/categories/edit'
import adminPosts from './components/pages/admin/posts/index';
import adminPostsCreate from './components/pages/admin/posts/create';
import adminPostsEdit from './components/pages/admin/posts/edit';
import adminPostsShow from './components/pages/admin/posts/show';
import adminUsers from './components/pages/admin/users/index';
import adminUsersEdit from './components/pages/admin/users/edit';
import adminUsersShow from './components/pages/admin/users/show';
import Swal from 'sweetalert2'
import authPosts from './components/pages/auth/posts/index';
import authPostsCreate from './components/pages/auth/posts/create';
import authPostsEdit from './components/pages/auth/posts/edit';
import authPostsShow from './components/pages/auth/posts/show';

Vue.use(Router);

const routes = [
    { path: '*',                         component: not_found,                name: 'not_found'  },
    { path: '/',                         component: home,                     name: 'home'       },
    { path: '/login',                    component: login,                    name: 'login',     },
    { path: '/register',                 component: register,                 name: 'register',  },
    { path: '/logout',                   component: logout,                   name: 'logout',                   meta: { requiresAuth: true }},
    { path: '/profile/edit',             component: profile,                  name: 'profile.edit',             meta: { requiresAuth: true }},
    { path: '/posts',                    component: posts,                    name: 'posts.index',              meta: { requiresAuth: true }},
    { path: '/posts/:id',                component: postsShow,                name: 'posts.show',               meta: { requiresAuth: true }},
    { path: '/users/:slug',              component: usersShow,                name: 'users.show' },
    { path: '/admin/categories',         component: adminCategories,          name: 'admin.categories.index',   meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/categories/create',  component: adminCreateCategories,    name: 'admin.categories.create',  meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/categories/:id/edit',component: adminEditCategories,      name: 'admin.categories.edit',    meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/posts',              component: adminPosts,               name: 'admin.posts.index',        meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/posts/create',       component: adminPostsCreate,         name: 'admin.posts.create',       meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/posts/:id',          component: adminPostsShow,           name: 'admin.posts.show',         meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/posts/:id/edit',     component: adminPostsEdit,           name: 'admin.posts.edit',         meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/users',              component: adminUsers,               name: 'admin.users.index',        meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/users/:id',          component: adminUsersShow,           name: 'admin.users.show',         meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/admin/users/:id/edit',     component: adminUsersEdit,           name: 'admin.users.edit',         meta: { requiresAuth: true, onlyForAdmins:true}},
    { path: '/my/posts',                 component: authPosts,                name: 'auth.posts.index',         meta: { requiresAuth: true }},
    { path: '/my/posts/create',          component: authPostsCreate,          name: 'auth.posts.create',        meta: { requiresAuth: true }},
    { path: '/my/posts/:id',             component: authPostsShow,            name: 'auth.posts.show',          meta: { requiresAuth: true }},
    { path: '/my/posts/:id/edit',        component: authPostsEdit,            name: 'auth.posts.edit',          meta: { requiresAuth: true }},


];

const router = new Router({
    mode: 'history',
    routes,
});

router.beforeEach((to, from, next) => {
    let auth_routes = ['login','register'];
    let is_logged   = store.getters.isLogged;
    let is_admin    = store.getters.isAdmin;

    if(is_logged && auth_routes.includes(to.name)){
       next('/');
       return;
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {

        //FOR ADMIN ROUTES
        if(to.matched.some(record => record.meta.onlyForAdmins) && is_logged){
            if(is_admin){
                next();
                return false;
            }else{
                Swal.fire({
                    title: 'Not Allowed Page',
                    text: "You aren't allowed to visit that page.",
                    type: 'warning',
                    confirmButtonText: "Okay",
                }).then((res) => {
                    next('/');
                });
                return false;
            }
        }

        if (is_logged) {
            next();
            return false;
        } else {
            Swal.fire({
                title: 'Not Allowed Page',
                text: "You aren't allowed to visit that page.",
                type: 'warning',
                confirmButtonText: "Okay",
            }).then((res) => {
                next('/login');
            });
            return false;
        }

    } else {
        next();
        return false;
    }
});

export default router;

<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01"
                    aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <router-link :class="'navbar-brand'" :to="'/'">BLOG</router-link>
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item px-2">
                        <router-link :class="'nav-link'" :to="'/'">Home</router-link>
                    </li>
                    <li class="nav-item px-2">
                        <router-link :class="'nav-link'" :to="'/posts'">Posts</router-link>
                    </li>
                    <li class="nav-item px-2">
                        <router-link :class="'nav-link'" :to="'/my/posts'" v-if="loggedIn">My Posts</router-link>
                    </li>
                </ul>

                <div class="my-2 my-lg-0">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item px-2" v-if="!loggedIn">
                            <router-link :class="'nav-link'" :to="'/login'">Login</router-link>
                        </li>
                        <li class="nav-item px-2" v-if="!loggedIn">
                            <router-link :class="'nav-link'" :to="'/register'">Register</router-link>
                        </li>
                        <li class="nav-item dropdown" v-if="loggedIn && isAdmin">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Administration
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown1">
                                <router-link :class="'dropdown-item'" :to="'/admin/users'">Users</router-link>
                                <div class="dropdown-divider"></div>
                                <router-link :class="'dropdown-item'" :to="'/admin/categories'">Categories</router-link>
                                <router-link :class="'dropdown-item'" :to="'/admin/posts'">Posts</router-link>
                            </div>
                        </li>
                        <li class="nav-item dropdown ml-3" v-if="loggedIn">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="div-image">
                                    <img :src="user.image_url || 'https://i.stack.imgur.com/l60Hf.png'"/>
                                </div>
                                {{ user.name}}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <router-link :class="'dropdown-item'" :to="'/users/'+$store.getters.user.username">Profile</router-link>
                                <div class="dropdown-divider"></div>
                                <router-link :class="'dropdown-item'" :to="'/logout'">Log Out</router-link>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "navbar",
        computed: {
            loggedIn() {
                return this.$store.getters.isLogged;
            },
            user() {
                return this.$store.getters.user;
            },
            isAdmin() {
                return this.$store.getters.isAdmin;
            }
        },
    }
</script>

<style scoped>
    .dropdown .div-image {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        margin-right: 5px;
        display: inline-block;
    }

    .dropdown img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
</style>

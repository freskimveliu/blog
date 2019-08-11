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
                        <router-link :class="'nav-link'" v-if="loggedIn" :to="'/posts'">Posts</router-link>
                    </li>
                    <li class="nav-item px-2">
                        <router-link :class="'nav-link'" :to="'/my/posts/create'" v-if="loggedIn">Create Post</router-link>
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

                        <li class="nav-item dropdown" v-if="loggedIn">
                            <a class="nav-link dropdown-toggle" @click="getFollowRequests(1)" href="#"
                               id="follow-requests" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Follow Requests
                            </a>
                            <div @scroll="onScroll" v-clickoutside="closeDropdownMenu"
                                 class="dropdown-menu dropdown-menu-right dropdown-menu-follow-requests"
                                 :class="dropdown_menu_state ? 'show' : ''" aria-labelledby="requests">
                                <div @click="clickedOnDropdownItem()" v-for="(follow_request, key) in follow_requests"
                                     v-if="follow_requests.length > 0" class="mb-2 px-2 dropdown-item">
                                    <div class="d-flex justify-content-between">
                                        <router-link :to="'/users/'+follow_request.user.username" class="user-details">
                                            <div class="div-image">
                                                <img :src="follow_request.user.image"/>
                                            </div>
                                            {{ follow_request.user.name }}
                                        </router-link>
                                        <div class="follow-request-buttons"
                                             v-show="follow_request.status === 'requested'">
                                            <button @click="confirmFollowRequest(follow_request.id,key)"
                                                    class="btn mr-1 btn-primary btn-confirm-request">Confirm
                                            </button>
                                            <button @click="cancelFollowRequest(follow_request.id,key)"
                                                    class="btn btn-default btn-cancel-request">Cancel
                                            </button>
                                        </div>
                                        <div class="text-capitalize request-status"
                                             :class="follow_request.status === 'cancelled' ? 'text-danger' : 'text-primary'"
                                             v-show="follow_request.status !== 'requested'">
                                            {{ follow_request.status }}
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-item" v-if="follow_requests.length === 0">
                                    You don't have any follow requests.
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown ml-3" v-if="loggedIn">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="div-image">
                                    <img :src="user.image || 'https://i.stack.imgur.com/l60Hf.png'"/>
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
    import {EventBus} from "../../event-bus";

    export default {
        name: "navbar",
        data: function () {
            return {
                follow_requests: [],
                follow_requests_xhr: false,
                dropdown_menu_state: false,
                current_page: 0,
                last_page: 1,
                next_page: 1,
            }
        },
        directives: {
            clickoutside: {
                bind: function (el, binding, vnode) {
                    el.eventSetDrag = function () {
                        el.setAttribute('data-dragging', 'yes');
                    }
                    el.eventClearDrag = function () {
                        el.removeAttribute('data-dragging');
                    }
                    el.eventOnClick = function (event) {
                        var dragging = el.getAttribute('data-dragging');
                        // Check that the click was outside the el and its children, and wasn't a drag
                        if (!(el == event.target || el.contains(event.target)) && !dragging) {
                            // call method provided in attribute value
                            vnode.context[binding.expression](event);
                        }
                    };
                    document.addEventListener('touchstart', el.eventClearDrag);
                    document.addEventListener('touchmove', el.eventSetDrag);
                    document.addEventListener('click', el.eventOnClick);
                    document.addEventListener('touchend', el.eventOnClick);
                }, unbind: function (el) {
                    document.removeEventListener('touchstart', el.eventClearDrag);
                    document.removeEventListener('touchmove', el.eventSetDrag);
                    document.removeEventListener('click', el.eventOnClick);
                    document.removeEventListener('touchend', el.eventOnClick);
                    el.removeAttribute('data-dragging');
                },
            }
        },
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
        methods: {
            getFollowRequests(page) {
                this.dropdown_menu_state = true;

                if (this.follow_requests_xhr) {
                    return false;
                }

                if (this.last_page <= this.current_page) {
                    return false;
                }

                if (page <= this.current_page) {
                    return false;
                }

                this.follow_requests_xhr = true;

                axios.get(`/my/follow-requests?page=${page}`)
                    .then(res => {
                        this.follow_requests = this.follow_requests.concat(res.data.data);

                        this.current_page = res.data.pagination.current_page;
                        this.next_page = res.data.pagination.current_page + 1;
                        this.last_page = res.data.pagination.last_page;
                        this.follow_requests_xhr = false;
                    })
                    .catch(err => {
                        this.follow_requests_xhr = false;
                        console.log(err)
                    })


            },
            confirmFollowRequest(follow_request_id, key) {
                axios.get(`/my/follow-requests/${follow_request_id}/confirm`)
                    .then(res => {
                        if (this.$route.name === 'users.show' && this.$route.params.slug === this.$store.getters.user.username) {
                            EventBus.$emit('new-follower-for-auth-user', 1);
                        }
                        this.follow_requests[key].status = 'confirmed'
                    })
                    .catch(err => {
                        console.log(err);
                    })
            },
            cancelFollowRequest(follow_request_id, key) {
                axios.get(`/my/follow-requests/${follow_request_id}/cancel`)
                    .then(res => {
                        this.follow_requests[key].status = 'cancelled'
                    })
                    .catch(err => {
                        console.log(err);
                    })
            },
            clickedOnDropdownItem() {
                this.dropdown_menu_state = true;
            },
            closeDropdownMenu() {
                this.dropdown_menu_state = false;

                this.current_page = 0;
                this.last_page = 1;
                this.next_page = 1;
                this.follow_requests = [];
            },
            onScroll({target: {scrollTop, clientHeight, scrollHeight}}) {
                if (scrollTop + clientHeight >= scrollHeight) {
                    this.getFollowRequests(this.next_page);
                }
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
        border: 1px solid gray;
    }

    .dropdown img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .dropdown-menu-follow-requests {
        width: 400px;
        max-height: 400px;
        overflow-y: scroll;
    }

    .btn-confirm-request, .btn-cancel-request {
        padding: 3px 15px;
    }

    .btn-cancel-request {
        background: #f4f4f4;
    }

    .dropdown-item:focus {
        background: transparent;
        color: #16181b;
    }

    .user-details {
        max-width: 205px;
        overflow: hidden;
        margin-right: 15px;
        margin-top: 3px;
    }

    .request-status {
        margin-top: 3px;
        font-weight: bold;
    }
</style>

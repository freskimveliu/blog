<template>
    <div>
        <div class="row pt-4">
            <div class="col-md-9 offset-md-3 user-notifications">
                <div v-if="object.is_following_me_and_im_not_following_him">
                    {{object.name }} is following you, follow back to see his/her posts.
                </div>
                <div v-if="object.has_requested_to_follow_me">
                    {{object.name }} has requested to follow you, confirm <a href="javascript:void(0)"
                                                                             @click="confirmFollowRequest(object.relationship_as_a_user_with_auth_friend.id)"
                                                                             class="text-primary"> here.</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="user-image-div">
                    <img :src="object.image_url ? object.image_url : default_user_image()">
                </div>
            </div>
            <div class="col-md-9">
                <div class="user-details pt-2">
                    <div class="d-flex">
                        <div class="user-name">
                            {{ object.name }}
                        </div>
                        <div class="follow-user ml-5 pl-3"
                             v-if="!object.is_my_profile">
                            <div class="not-following"
                                 v-show="(object.relationship_status_as_a_friend_with_auth_user === 'unfollowing') || (object.relationship_status_as_a_friend_with_auth_user == null)">
                                <button @click="relationshipAction" class="btn btn-sm btn-primary px-4">Follow</button>
                            </div>
                            <div class="following bg-white"
                                 v-show="(object.relationship_status_as_a_friend_with_auth_user === 'following')">
                                <button class="btn btn-sm px-4" @click="unFollowUser">Following</button>
                            </div>
                            <div class="following"
                                 v-show="(object.relationship_status_as_a_friend_with_auth_user === 'requested')">
                                <button class="btn btn-sm bg-white px-4" @click="relationshipAction">Requested</button>
                            </div>
                        </div>
                        <div class="follow-user ml-5 pl-3" v-if="object.is_my_profile">
                            <div class="following bg-white">
                                <router-link :to="'/profile/edit'" class="btn btn-sm px-4">Edit Profile</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex counts">
                        <div class="posts-count">
                            <strong>{{ object.posts_count }}</strong> Posts
                        </div>
                        <div class="followers-count" @click="showRelationships('followers')">
                            <strong>{{ object.followers_count }} </strong> Followers
                        </div>
                        <div class="followings-count" @click="showRelationships('followings')">
                            <strong>{{ object.followings_count }}</strong> Following
                        </div>
                    </div>
                    <div class="user-bio">
                        {{ object.bio }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5 posts-container">
            <div class="col-md-12 posts">
                <div class="row px-2" v-if="object.can_i_show_posts">
                    <div class="col-4 my-4" v-for="post in posts">
                        <router-link :to="'/posts/'+post.id" class="user-post">
                            <div class="user-post-image">
                                <img :src="post.image_url ? post.image_url : default_image()">
                            </div>
                            <div class="user-post-details">
                                <div class="user-post-title">
                                    {{ post.title }}
                                </div>
                                <div class="mt-2">
                                    <span class="mr-2">{{ post.favorites_count }} <font-awesome-icon icon="star"
                                                                                                     class="icon alt"/></span>
                                    <span class="ml-2">{{ post.comments_count}} <font-awesome-icon icon="comments"
                                                                                                   class="icon alt"/></span>
                                </div>
                            </div>
                        </router-link>
                    </div>
                    <div class="col-md-12 text-center py-5" v-if="posts.length === 0">
                        <h5>This Account doesn't have posts.</h5>
                    </div>
                </div>
                <div class="row py-5" v-else>
                    <div class="col-md-12 text-center">
                        <h5>This Account is Private.</h5>
                        <div class="mt-2">Follow to see their posts.</div>
                    </div>
                </div>
            </div>
        </div>

        <app-followers :show_followers="show_followers"
                       @relationshipStatuses="relationshipStatuses"
                       @hideFollowers="show_followers = false"></app-followers>
        <app-followings :show_followings="show_followings"
                        @relationshipStatuses="relationshipStatuses"
                        @hideFollowings="show_followings = false"></app-followings>
    </div>
</template>

<script>
    import {EventBus} from "../../../event-bus.js";
    import AppFollowers from '../../pages/users/partials/followers';
    import AppFollowings from '../../pages/users/partials/followings';

    export default {
        name: "users.show",
        components: {
            'app-followers': AppFollowers,
            'app-followings': AppFollowings,
        },
        data: function () {
            return {
                object: {
                    id: 0,
                    image_url: '',
                    name: '',
                    bio: '',
                    posts_count: 0,
                    im_following: false,
                    followers_count: 0,
                    followings_count: 0,
                    is_my_profile: false,
                    is_private_account: false,
                    has_requested_to_follow_me: false,
                    has_blocked_relationships: true,
                },
                posts: [],
                current_page: 1,
                last_page: 1,
                username: '',
                show_followers: false,
                show_followings: false,
            }
        },
        created() {
            this.getUserDetails();
        },
        methods: {
            getUserDetails() {
                this.username = this.$route.params.slug;
                axios.get(`/users/${this.username}`)
                    .then(res => {
                        this.object = res.data.data;
                        if (this.object.can_i_show_posts) {
                            this.getUsersPosts(1);
                        }
                        document.title = this.object.name || 'Blog';
                    })
                    .catch(err => {
                        console.log(err)
                    });
            },
            scroll() {
                window.onscroll = () => {
                    if (this.$route.name != 'users.show') {
                        return false;
                    }
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow && (this.current_page < this.last_page) && this.object.can_i_show_posts) {
                        this.getUsersPosts(this.current_page + 1);
                    }
                };
            },
            getUsersPosts(search_page) {
                let page = search_page || 1;
                axios.get(`/users/${this.$route.params.slug}/posts?page=${page}`, {
                    params: {
                        per_page: 6,
                    }
                })
                    .then(res => {
                        this.posts = this.posts.concat(res.data.data);
                        this.current_page = res.data.pagination.current_page;
                        this.last_page = res.data.pagination.last_page;
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            relationshipAction() {
                this.posts = [];

                let action = this.object.next_relationship_action_as_a_friend_with_auth_user;

                axios.put(`/my/relationships/${this.$route.params.slug}`, {
                    action: action
                })
                    .then(res => {
                        this.object = res.data.data;

                        if (this.object.im_following) {
                            this.getUsersPosts(1);
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            confirmFollowRequest(follow_request_id) {
                axios.get(`/my/follow-requests/${follow_request_id}/confirm`)
                    .then(res => {
                        this.object = res.data.data;
                    })
                    .catch(err => {
                        console.log(err);
                    })
            },
            unFollowUser(){
                let title = `Are you sure, you want to un follow ${this.object.name}?`;
                this.showConfirmAlert(title, this.relationshipAction);
            },
            showRelationships(status) {
                if (this.object.has_blocked_relationships) {
                    return false;
                }

                if (status === 'followers') {
                    this.show_followers = true;
                }

                if (status === 'followings') {
                    this.show_followings = true;
                }
            },
            relationshipStatuses(statuses) {
                if (!this.object.is_my_profile) {
                    return false;
                }

                let old_status = statuses.old_status;
                let new_status = statuses.new_status;

                if (old_status === 'following' && new_status === 'unfollowing') {
                    this.object.followings_count--;
                }

                if (new_status === 'following') {
                    this.object.followings_count++;
                }
            }
        },
        mounted() {
            this.scroll();
            if (this.$store.getters.isLogged && (this.object.id === this.$store.getters.user.id)) {
                EventBus.$on('new-follower-for-auth-user', () => {
                    this.object.followers_count++;
                })
            }
        },
        watch: {
            $route(to, from) {
                document.title = 'Blog';
                this.posts = [];
                this.username = this.$route.params.slug;
                this.getUserDetails();
            }
        },
        beforeRouteLeave(from,to,next){
            next();

        },
        beforeRouteUpdate(to, from, next) {
            next();

        },
    }
</script>

<style scoped>
    .posts img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 5px;
    }

    .user-image-div {
        width: 150px;
        height: 150px;
        border: 2px solid #f4f4f4;
        border-radius: 50%;
        padding: 5px;
    }

    .user-image-div img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .user-name {
        color: #262626;
        font-size: 18px;
        margin-top: 4px;
    }

    .counts div {
        margin-right: 30px;
        margin-top: 30px;
        margin-bottom: 20px;
        font-size: 15px;
    }

    .following {
        border: 1px solid #f4f4f4;
        border-radius: 5px;
    }

    .user-post-details {
        margin: 0;
        color: #fff;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -35%;
        transform: translate(-50%, -50%);
        font-size: 16px;
        text-align: center;
        display: none;
    }

    .user-post:hover .user-post-details {
        display: block;
    }

    .follow-user {
        height: 40px;
    }

    .posts-container {
        border: 1px solid #f4f4f4;
        box-shadow: 5px -5px 5px #f4f4f4;
        background-color: #fff;
    }

    .user-notifications{
        height: 20px;
        background: transparent;
    }

    .followers-count, .followings-count {
        cursor: pointer;
    }
</style>

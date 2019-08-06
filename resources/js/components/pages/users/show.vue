<template>
    <div>
        <div class="row pt-4">
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
                            <div class="not-following" v-show="(object.my_relationship_status === 'unfollowing') || (object.my_relationship_status == null)">
                                <button @click="relationshipAction" class="btn btn-sm btn-primary px-4">Follow</button>
                            </div>
                            <div class="following" v-show="(object.my_relationship_status === 'following')">
                                <button class="btn btn-sm px-4" @click="relationshipAction">Following</button>
                            </div>
                            <div class="following" v-show="(object.my_relationship_status === 'requested')">
                                <button class="btn btn-sm px-4" @click="relationshipAction">Requested</button>
                            </div>
                        </div>
                        <div class="follow-user ml-5 pl-3" v-if="object.is_my_profile">
                            <div class="following">
                                <router-link :to="'/profile/edit'" class="btn btn-sm px-4">Edit Profile</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex counts">
                        <div class="posts-count">
                            <strong>{{ object.posts_count }}</strong> Posts
                        </div>
                        <div class="followers-count">
                            <strong>{{ object.followers_count }} </strong> Followers
                        </div>
                        <div class="following-count">
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
                <div class="row pt-4 px-2" v-if="canShowUserPosts()">
                    <div class="col-4 mb-4" v-for="post in posts">
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
                    <div class="col-md-12 text-center pb-2" v-if="posts.length === 0">
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
    </div>
</template>

<script>
    import { EventBus } from "../../../event-bus.js";

    export default {
        name: "users.show",
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
                },
                posts: [],
                current_page: 1,
                last_page: 1,
                username: '',
            }
        },
        created() {
            if(this.$store.state.is_logged){
                this.getUserDetails();
            }
        },
        beforeRouteUpdate(to, from, next) {
            next();
            this.posts = [];
            this.username = this.$route.params.slug;
            this.getUserDetails();
        },
        methods: {
            getUserDetails() {
                this.username = this.$route.params.slug;
                axios.get(`/users/${this.username}`)
                    .then(res => {
                        this.object = res.data.data;
                        if (this.canShowUserPosts()) {
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

                    if (bottomOfWindow && (this.current_page < this.last_page) && this.canShowUserPosts()) {
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
                if (!this.$store.state.is_logged) {
                    this.$router.push('/login');
                    return;
                }

                this.posts = [];

                let status = this.object.my_relationship_status;
                let action = '';

                switch (status) {
                    case 'following':
                    case 'requested':
                        action = 'unfollow';
                        break;
                    case 'unfollowing':
                        action = 'follow';
                        break;
                    case null:
                        action = 'follow';
                        break;
                    default:
                        return false;
                }

                axios.put(`/my/relationships/${this.$route.params.slug}`, {
                    action: action
                })
                    .then(res => {
                        let response_status = res.data.data.status;

                        switch (response_status) {
                            case 'following':
                                this.object.im_following = true;
                                this.object.followers_count++;
                                break;
                            case 'requested':
                                break;
                            case 'unfollowing':
                                if (status === 'following') {
                                    this.object.followers_count--;
                                }
                                this.object.im_following = false;
                                break;
                        }

                        this.object.my_relationship_status = response_status;

                        if (this.object.im_following) {
                            this.getUsersPosts(1);
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            canShowUserPosts() {
                if (this.object.id == this.$store.getters.user.id) {
                    return true;
                }
                if (this.object.im_following) {
                    return true;
                }
                return false;
            }
        },
        mounted() {
            this.scroll();
            EventBus.$on('new-follower-for-auth-user',()=>{
                this.object.followers_count++;
            })
        },
        beforeRouteLeave(from,to,next){
            next();
            document.title = 'Blog';
        }
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
    }
</style>

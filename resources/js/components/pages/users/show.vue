<template>
    <div>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="user-image-div">
                    <img :src="object.image_url ? object.image_url : default_user_image()">
                </div>
            </div>
            <div class="col-md-9">
                <div class="user-details mt-2">
                    <div class="d-flex">
                        <div class="user-name">
                            {{ object.name }}
                        </div>
                        <div class="follow-user ml-5 pl-3"
                             v-if="!object.is_my_profile">
                            <div class="not-following" v-show="!object.im_following">
                                <button @click="relationshipAction" class="btn btn-sm btn-primary px-4">Follow</button>
                            </div>
                            <div class="following" v-show="object.im_following">
                                <button class="btn btn-sm px-4" @click="relationshipAction">Following</button>
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
        <div class="row mt-5">
            <div class="col-md-12 mb-2">
                <h4>Posts</h4>
            </div>
            <div class="col-md-12 posts">
                <div class="row">
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
                </div>
            </div>
        </div>
    </div>
</template>

<script>
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
                },
                posts: [],
                current_page: 1,
                last_page: 1,

            }
        },
        created() {
            let username = this.$route.params.slug;
            axios.get(`/users/${username}`)
                .then(res => {
                    this.object = res.data.data;
                })
                .catch(err => {
                    console.log(err)
                });
            this.getUsersPosts(1);
        },
        methods: {
            scroll() {
                window.onscroll = () => {
                    if (this.$route.name != 'users.show') {
                        return false;
                    }
                    let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                    if (bottomOfWindow && (this.current_page < this.last_page)) {
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

                axios.put(`/my/relationships/${this.$route.params.slug}`, {
                    action: this.object.im_following ? 'unfollow' : 'follow'
                })
                    .then(res => {
                        if (this.object.im_following === true) {
                            this.object.followers_count--;
                        } else {
                            this.object.followers_count++;
                        }
                        this.object.im_following = !this.object.im_following;
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
        },
        mounted() {
            this.scroll();
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

</style>

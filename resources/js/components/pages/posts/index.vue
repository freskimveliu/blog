<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Posts</h3>
            </div>
        </div>
        <div class="row post my-2 mb-4 pb-4" v-for="(post,index) in posts">
            <div class="col-md-8">
                <div class="mb-3">
                    <router-link :to="'/posts/'+post.id" class="">
                        <img class="post-image" :src="post.image_url">
                    </router-link>
                    <div class="post-title">
                        <router-link :to="'/posts/'+post.id" class="">{{ post.title}}</router-link>
                    </div>
                    <div class="post-body mt-3">
                        {{ post.short_description | str_limit(250)}}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-inline-flex">
                    <div class="post-options">
                        <div>
                            <font-awesome-icon icon="list" class="icon alt"/>
                            {{ post.category.name }}
                        </div>
                        <div>
                            <font-awesome-icon icon="user" class="icon alt"/>
                            {{ post.user ? post.user.name : 'Not Set' }}
                        </div>
                        <div>
                            <font-awesome-icon icon="calendar" class="icon alt"/>
                            {{ post.created_at | day}}
                        </div>
                        <div>
                            <font-awesome-icon icon="clock" class="icon alt"/>
                            {{ post.created_at | time }}
                        </div>
                    </div>
                </div>
                <div class="d-inline favorite float-right" v-if="$store.state.is_logged" @click="favoriteAction(post.id,index)">
                    <img :src="post.is_favorite ? star_filled : star">
                </div>
            </div>
        </div>
        <app-pagination @pagination="fetchItems" :pagination="this.pagination"></app-pagination>
    </div>
</template>

<script>
    import pagination from '../../partials/pagination'

    export default {
        name: "auth.posts.index",
        components: {
            'app-pagination': pagination,
        },
        data: function () {
            return {
                posts: [],
                pagination: {},
                star: 'https://image.flaticon.com/icons/svg/149/149220.svg',
                star_filled: 'https://image.flaticon.com/icons/svg/148/148839.svg',
            }
        },
        created: function () {
            this.fetchItems();
        },
        methods: {
            fetchItems(page) {
                let url = `/posts?page=${page || 1}`;
                axios.get(url)
                    .then(res => {
                        this.posts = res.data.data;
                        let pagination = res.data.pagination;
                        this.pagination = {
                            current_page: pagination.current_page,
                            total: pagination.total,
                            last_page: pagination.last_page,
                            item_per_page: pagination.item_per_page,
                        };
                    })
                    .catch(err => {
                        console.log(err)
                    });
            },
            favoriteAction(post_id, index) {
                let post = this.posts[index];
                axios.post('/posts/' + post_id + '/favorite', {'is_favorite': !post.is_favorite})
                    .then(res => {
                        post.is_favorite = res.data.data;
                        if (post.is_favorite) {
                            this.showNotification('Post added to your favorites.');
                            post.image_src = this.star_filled;
                        } else {
                            this.showNotification('Post removed from your favorites.');
                            post.image_src = this.star;
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
        }
    }
</script>

<style scoped>
    .post .post-image {
        width: 150px;
        display: block;
        float: left;
        margin-right: 15px;
        border-radius: 10px;
    }

    .post-options > div {
        margin-bottom: 10px;
    }

    .post-options {
        margin-top: 0;
    }
</style>

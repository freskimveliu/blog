<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Posts</h3>
            </div>
        </div>
        <div class="row post mt-3" v-for="post in posts">
            <div class="col-md-8">
                <div>
                    <img class="post-image" :src="post.image_url">
                    <div class="post-title">
                        <router-link :to="'/posts/'+post.id" class="">{{ post.title}}</router-link>
                    </div>
                    <div class="post-body mt-3">
                        {{ post.short_description | str_limit(250)}}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
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
            <div class="col-md-1">
                <div class="d-inline favorite" >
                    <img src="https://image.flaticon.com/icons/svg/149/149220.svg">
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
                pagination: {}
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
            }
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

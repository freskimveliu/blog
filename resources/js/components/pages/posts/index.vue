<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Posts</h3>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6" v-for="post in posts">
                <div class="post">
                    <div class="post-title">
                        <router-link :to="'/posts/'+post.id" class="">{{ post.title}}</router-link>
                    </div>
                    <div class="post-options">
                        <span><font-awesome-icon icon="clock" class="icon alt"/> {{ post.created_at | time }}</span>
                        <span><font-awesome-icon icon="calendar" class="icon alt"/> {{ post.created_at | day}}</span>
                        <span><font-awesome-icon icon="list" class="icon alt"/> {{ post.category.name }}</span>
                        <span><font-awesome-icon icon="user" class="icon alt"/> {{ post.user ? post.user.name : 'Not Set' }}</span>
                    </div>
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

</style>

<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Online News</h3>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4" v-for="post in posts">
                <div class="card mb-4">
                    <div class="card-body">
                        <router-link :to="'/posts/'+post.id" class="card-title">{{ post.title}}</router-link>
                        <p class="card-text">
                            {{ post.body}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!--<app-pagination @pagination="fetchItems" :pagination="this.pagination"></app-pagination>-->
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
                let url = 'https://jsonplaceholder.typicode.com/posts';
                axios.get(url)
                    .then(res => {
                        this.posts = res.data;
                    })
                    .catch(err => {
                        console.log(err)
                    });
            }
        }
    }
</script>

<style scoped>
    .card-subtitle {
        font-size: 13px;
        margin: 10px 0;
        color: gray;
    }

    .card-title {
        font-size: 17px;
        text-decoration: none;
    }
</style>

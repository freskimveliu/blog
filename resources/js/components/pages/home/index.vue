<template>
    <div>
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="jumbotron text-center"
                     style="background-image: url(http://www.anasfim.com/p/2018/04/background-of-old-vintage-newspapers-stock-photo-ronstik-13732950-inside-newspaper-background-image.jpg); background-size: 100%;">
                    <div class="jumbotron-title">The best website for blog posts.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="content row">
                    <div class="col-md-12">
                        <div class="content-title">Most popular posts</div>
                    </div>
                    <div class="col-md-6 content-body" v-for="(popular_post,index) in popular_posts">
                        <router-link :to="'/posts/'+popular_post.id">
                            <span>{{ index+1}}</span>
                            <strong>
                                {{ popular_post.title }}
                            </strong>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="content row">
                    <div class="col-md-12">
                        <div class="content-title">Recent posts</div>
                    </div>
                    <div class="col-md-6 content-body" v-for="(recent_post,index) in recent_posts">
                        <router-link :to="'/posts/'+recent_post.id">
                            <span>{{ index+1}}</span>
                            <strong>
                                {{ recent_post.title }}
                            </strong>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "home.index",
        data: function () {
            return {
                popular_posts: [],
                recent_posts: [],
            }
        },
        created: function () {
            axios.get('/posts?per_page=6&tag=most_popular')
                .then(res => {
                    this.popular_posts = res.data.data;
                })
                .catch(err => {
                    console.log(err)
                });
            axios.get('/posts?per_page=6')
                .then(res => {
                    this.recent_posts = res.data.data;
                })
                .catch(err => {
                    console.log(err)
                })
        }
    }
</script>

<style scoped>

</style>

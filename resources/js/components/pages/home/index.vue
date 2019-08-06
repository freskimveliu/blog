<template>
    <div>
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="jumbotron text-center"
                     style="background-image: url(http://www.anasfim.com/p/2018/04/background-of-old-vintage-newspapers-stock-photo-ronstik-13732950-inside-newspaper-background-image.jpg); background-size: 100%;">
                    <div class="jumbotron-title">The best website for posts.</div>
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

        }
    }
</script>

<style scoped>

</style>

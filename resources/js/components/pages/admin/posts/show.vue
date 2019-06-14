<template>
    <div class="row">
        <div class="col-md-12">
            <div class="my-5">
                <h3>{{ object.title }}</h3>
                <hr>
            </div>
            <div class="post-options">
                <div class="float-right">
                    <span><font-awesome-icon icon="clock" class="icon alt"/> {{ object.created_at | time }}</span>
                    <span><font-awesome-icon icon="calendar" class="icon alt"/> {{ object.created_at | day}}</span>
                    <span><font-awesome-icon icon="list" class="icon alt"/> {{ object.category.name }}</span>
                    <span><font-awesome-icon icon="user" class="icon alt"/> {{ object.user ? object.user.name : 'Not Set' }}</span>
                </div>
            </div>
            <img :src="object.image_url" class="cover-image mt-3" :class="{'d-none' : !object.image_url}">
        </div>
        <div class="col-md-12 mt-4" v-html="object.description"></div>
    </div>
</template>

<script>
    export default {
        name: "admin.posts.index",
        data: function () {
            return {
                object: {
                    title: '',
                    created_at: '',
                    category: {
                        name: ''
                    }
                },
            }
        },
        created: function () {
            axios.get(`/admin/posts/${this.$route.params.id}`)
                .then((res) => {
                    let data = res.data;
                    this.object = data.data;
                })
                .catch((err) => {
                    console.log(err)
                });
        },
    }
</script>

<style scoped>

</style>

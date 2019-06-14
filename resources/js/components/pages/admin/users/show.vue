<template>
    <div>
        <div class="row">
            <div class="col-md-12 mt-4">
                <router-link :to="'/admin/users'" class="btn btn-primary mb-3">Go Back</router-link>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <img v-if="object.image_url" :src="object.image_url">
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-2"><strong>Name</strong></div>
                    <div class="col-md-10">{{ object.name }} </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><strong>Email</strong></div>
                    <div class="col-md-10">{{ object.email }} </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><strong>Role</strong></div>
                    <div class="col-md-10 text-capitalize">{{ object.role }} </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><strong>Posts</strong></div>
                    <div class="col-md-10 text-capitalize">{{ object.posts_count }} </div>
                </div>
                <div class="row">
                    <div class="col-md-2"><strong>Bio</strong></div>
                    <div class="col-md-10">{{ object.bio || '--' }} </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "admin.users.index",
        data: function () {
            return {
                object: {},
            }
        },
        created: function () {
            axios.get(`/admin/users/${this.$route.params.id}`)
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
    img {
        width:100px;
        height:100px;
        object-fit: cover;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
    }
</style>

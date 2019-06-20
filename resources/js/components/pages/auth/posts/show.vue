<template>
    <div>
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
                    </div>
                </div>
                <div>
                    <img :src="object.image_url" class="cover-image mt-3" :class="{'d-none' : !object.image_url}">
                </div>
            </div>
            <div class="col-md-12 mt-4" v-html="object.description"></div>
        </div>
        <div class="row my-4">
            <div class="col-md-12 my-4">
                <h4>Comments</h4>
            </div>
            <div class="col-md-12">
                <div class="comments-section">
                    <div class="comment w-100" v-for="(comment,index) in object.comments">
                        <div class="comment-avatar">
                            <img :src="comment.user && comment.user.image_url ? comment.user.image_url : 'https://www.startupdelta.org/wp-content/uploads/2018/04/No-profile-LinkedIn.jpg'">
                        </div>
                        <div class="comment-body w-100">
                            <div class="comment-message">
                                {{ comment.message }}
                            </div>
                            <div class="comment-options">
                                <span><font-awesome-icon icon="clock" class="icon alt"/> {{ comment.created_at | time }}</span>
                                <span><font-awesome-icon icon="calendar" class="icon alt"/> {{ comment.created_at | day}}</span>
                                <span><font-awesome-icon icon="user" class="icon alt"/> {{ comment.user_id === loggedId ? 'Your Comment' : comment.user_name}}</span>
                                <span class="delete-comment text-danger text" @click="deleteComment(comment.id,index)">
                                    <font-awesome-icon icon="trash" class="icon alt"/> Delete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "my.posts.index",
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
        computed: {
            loggedId() {
                return this.$store.getters.user.id;
            }
        },
        created: function () {
            axios.get(`/my/posts/${this.$route.params.id}`)
                .then((res) => {
                    let data = res.data;
                    this.object = data.data;
                })
                .catch((err) => {
                    console.log(err)
                });
        },
        methods: {
            deleteComment(id, index) {
                this.$swal({
                    title: 'Do you want to delete this comment?',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!',
                    confirmButtonClass: 'btn btn-sm btn-primary',
                    buttonsStyling: true,
                }).then((result) => {
                    if (result.value) {
                        axios.delete('/my/posts/' + this.$route.params.id + '/comments/' + id + `?index=${index}`)
                            .then(res => {
                                this.object.comments.splice(index, 1);
                            })
                            .catch(err => {
                                console.log(err);
                            })
                    }
                })

            }
        },
        mounted: function () {
            window.Echo.channel(`post.${this.$route.params.id}`)
                .listen('new-comment', (res) => {
                    this.object.comments.unshift(res);
                    let user = this.$store.getters.user;
                    let user_id;
                    if (user) {
                        user_id = user.id;
                    } else {
                        user_id = 0;
                    }

                    if (res.user_id !== user_id) {
                        this.showNotification(res.user_name + ' wrote on this post!')
                    }
                }).listen('delete-comment', (res) => {
                this.object.comments.splice(res.index, 1);
                this.showNotification(res.user_name + ' deleted a comment.')
            })
        },
    }
</script>

<style scoped>

</style>

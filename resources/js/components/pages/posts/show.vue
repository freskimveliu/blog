<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="my-5">
                    <h3>{{ object.title }}</h3>
                    <div class="text-right" v-if="($store.getters.isLogged) && (object.user_id === $store.getters.user.id)">
                        <router-link :to="'/my/posts/'+object.id+'/edit'"><font-awesome-icon icon="pen" class="icon alt"/><span class="ml-1">Edit Post</span></router-link>
                    </div>
                    <hr>
                </div>
                <div class="post-options mb-3">
                    <div class="d-inline favorite" v-if="isLogged">
                        <img :src="image_src" @click="favoriteAction">
                    </div>
                    <div class="float-right">
                        <span><font-awesome-icon icon="clock" class="icon alt"/> {{ object.created_at | time }}</span>
                        <span><font-awesome-icon icon="calendar" class="icon alt"/> {{ object.created_at | day}}</span>
                        <span><font-awesome-icon icon="list" class="icon alt"/> {{ object.category.name }}</span>
                        <span><font-awesome-icon icon="user" class="icon alt"/>
                            <router-link v-if="object.user" :to="'/users/'+object.user.username">{{ object.user.name }}
                            </router-link>
                        </span>
                    </div>
                </div>
                <img :src="object.image_url" class="cover-image" :class="{'d-none' : !object.image_url}">
            </div>
            <div class="col-md-12 mt-4" v-html="object.description"></div>
        </div>
        <div class="row mt-5">
            <div class="col-md-12">
                <h4>Leave a comment</h4>
            </div>
            <div class="col-md-12">
                <form @submit.prevent="submitForm">
                    <div class="form-group" v-if="!isLogged">
                        <label for="first_name"><strong>Name</strong></label>
                        <input type="text" id="first_name" name="first_name" v-model="form.name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="message"><strong>Message</strong></label>
                        <textarea class="form-control" v-model="form.message" name="message" id="message" cols="30"
                                  rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row my-4">
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
                                <span><font-awesome-icon icon="user" class="icon alt"/> {{ comment.user_id == loggedId ? 'Your Comment' : comment.user_name}}</span>
                                <span class="delete-comment text-danger text" v-if="comment.user_id == loggedId"
                                      @click="deleteComment(comment.id,index)">
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
        name: "auth.posts.index",
        data: function () {
            return {
                object: {
                    title: '',
                    created_at: '',
                    category: {
                        name: ''
                    },
                    is_favorite: false,
                    comments: [],
                },
                image_src: '',
                star: 'https://image.flaticon.com/icons/svg/148/148839.svg',
                star_filled: 'https://image.flaticon.com/icons/svg/149/149220.svg',
                form: {
                    message: '',
                    name: '',
                },
            }
        },
        created: function () {
            axios.get(`/posts/${this.$route.params.id}`)
                .then((res) => {
                    let data = res.data;
                    this.object = data.data;

                    if (data.data.is_favorite) {
                        this.image_src = this.star;
                    } else {
                        this.image_src = this.star_filled;
                    }
                    this.object.comments = data.data.comments;
                })
                .catch((err) => {
                    console.log(err)
                });
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
        computed: {
            isLogged: function () {
                return this.$store.getters.isLogged;
            },
            loggedId: function () {
                let user = this.$store.getters.user;
                if (user) {
                    return user.id;
                }
                return 0;
            }
        },
        methods: {
            favoriteAction() {
                axios.post('/posts/' + this.$route.params.id + '/favorite', {'is_favorite': !this.object.is_favorite})
                    .then(res => {
                        this.object.is_favorite = res.data.data;
                        if (this.object.is_favorite) {
                            this.showNotification('Post added to your favorites.');
                            this.image_src = this.star;
                        } else {
                            this.showNotification('Post removed from your favorites.');
                            this.image_src = this.star_filled;
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },

            submitForm() {
                axios.post('/posts/' + this.$route.params.id + '/comments', this.form)
                    .then(res => {
                        this.form.message = '';
                        this.form.name = '';
                        this.object.comments.unshift(res.data.data);
                    })
                    .catch(err => {
                        console.log(err);
                    })
            },

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
                        axios.delete('/posts/' + this.$route.params.id + '/comments/' + id + `?index=${index}`)
                            .then(res => {
                                this.object.comments.splice(index, 1);
                            })
                            .catch(err => {
                                console.log(err);
                            })
                    }
                })

            }
        }
    }
</script>

<style scoped>
</style>

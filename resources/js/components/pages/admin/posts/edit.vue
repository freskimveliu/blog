<template>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center mt-3">Edit Post</h3>
            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" v-model="post.title">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" v-model="post.category_id" class="form-control">
                        <option v-for="(category,key) in categories" :value="category.id">{{ category.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <text-editor id="description" v-model="post.description"/>
                </div>
                <button class="btn btn-primary">Submit</button>
                <router-link class="btn btn-warning" :to="'/admin/posts'">Cancel</router-link>
            </form>
        </div>
    </div>
</template>

<script>
    import TextEditor from "../../../partials/text-editor";
    export default {
        name: "posts.create",
        components: {TextEditor},
        data: function () {
            return {
                post: {},
                categories: []
            }
        },
        methods: {
            submitForm() {
                axios.put(`/admin/posts/${this.$route.params.id}`, this.post)
                    .then(res => {
                        this.showNotification('Post updated successfully!')
                        this.$router.push('/admin/posts');
                    })
                    .catch(err => {
                        console.log(err.data.errors[0]);
                    })
            },
        },
        created: function () {
            axios.get(`/admin/categories`)
                .then((res) => {
                    let data = res.data;
                    this.categories = data.data;
                })
                .catch((err) => {
                    console.log(err)
                });

            axios.get(`/admin/posts/${this.$route.params.id}`)
                .then((res) => {
                    let data = res.data;
                    console.log(data);
                    this.post = data.data;
                })
                .catch((err) => {
                    console.log(err)
                })
        }
    }
</script>

<style scoped>

</style>

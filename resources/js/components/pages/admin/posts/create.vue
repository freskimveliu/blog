<template>
    <div class="row mb-5">
        <div class="col-md-12">
            <h3 class="text-center mt-3">Create Post</h3>
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
                    <text-editor v-model="post.description" id="description"></text-editor>
                </div>
                <button class="btn btn-primary">Submit</button>
                <router-link class="btn btn-warning" :to="'/admin/posts'">Cancel</router-link>
            </form>
        </div>
    </div>
</template>

<script>
    import text_editor from '../../../partials/text-editor';

    export default {
        components: {
            'text-editor': text_editor
        },
        name: "posts.create",
        data: function () {
            return {
                post: {
                    category_id:''
                },
                categories: []
            }
        },
        methods: {
            submitForm() {
                axios.post('/admin/posts', this.post)
                    .then(res => {
                        this.showNotification('Post created successfully!')
                        this.$router.push('/admin/posts');
                    })
                    .catch(err => {
                        console.log(err.data.errors[0]);
                    })
            },
        },
        created: function () {
            let url = `/admin/categories`;
            axios.get(url)
                .then((res) => {
                    let data = res.data;
                    this.categories = data.data;
                })
                .catch((err) => {
                    console.log(err)
                })
        }
    }
</script>

<style scoped>

</style>

<template>
    <div class="row mb-5">
        <div class="col-md-12">
            <h3 class="text-center mt-3">Create a Post</h3>
            <form @submit.prevent>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" v-model="object.title">
                    <span class="text text-danger" v-if="errors.title">{{ errors.title[0] }}</span>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" v-model="object.category_id" class="form-control">
                        <option v-for="(category,key) in categories" :value="category.id">{{ category.name }}
                        </option>
                    </select>
                    <span class="text text-danger" v-if="errors.category_id">{{ errors.category_id[0] }}</span>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" class="form-control" accept="image/*" id="image" @change="onFileSelected">
                    <span class="text text-danger" v-if="errors.image">{{ errors.image[0] }}</span>
                </div>
                <div class="form-group" :class="{'d-none' : !object.image_url}">
                    <img class="" :src="object.image_url">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <text-editor id="description" v-model="object.description"></text-editor>
                    <span class="text text-danger" v-if="errors.description">{{ errors.description[0] }}</span>
                </div>
                <button @click="submitForm" class="btn btn-primary">Submit</button>
                <router-link class="btn btn-warning" :to="'/posts'">Cancel</router-link>
            </form>
        </div>
    </div>
</template>

<script>
    import text_editor from '../../../partials/text-editor';

    export default {
        name: "auth.posts.create",
        components: {
            'text-editor': text_editor
        },
        data: function () {
            return {
                object: {
                    category_id: '',
                    description: '',
                    image: '',
                    image_url: null,
                },
                categories: [],
                errors: {}
            }
        },
        methods: {
            submitForm() {
                this.errors = {};

                const form_data = new FormData();
                let object      = this.object;

                Object.keys(object).forEach(function (key) {
                    form_data.append(key, object[key]);
                });

                axios.post('/my/posts', form_data)
                    .then(res => {
                        this.showNotification('Post created successfully!')
                        this.$router.push(`/posts/${res.data.data.id}`);
                    })
                    .catch(err => {
                        this.errors = err.data.errors;
                    })
            },
            onFileSelected(event) {
                this.object.image = event.target.files[0];
                let src = URL.createObjectURL(event.target.files[0]);
                this.object.image_url = src
            },
        },
        created: function () {
            let url = `/categories`;
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
    form img {
        width: 100px;
    }
</style>

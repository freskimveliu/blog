<template>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center mt-3">Create Category</h3>
            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label for="title">Name</label>
                    <input type="text" class="form-control" id="title" v-model="category.name">
                </div>
                <button class="btn btn-primary">Submit</button>
                <router-link class="btn btn-warning" :to="'/admin/categories'">Cancel</router-link>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "admin.categories.create",
        data: function () {
            return {
                category: {
                    name: '',
                }
            }
        },
        methods: {
            submitForm() {
                axios.post('/admin/categories', this.category)
                    .then(res => {
                        this.showNotification('Category created successfully!')
                        this.$router.push('/admin/categories');
                    })
                    .catch(err => {
                        console.log(err.data.errors[0]);
                    })
            }
        }
    }
</script>

<style scoped>

</style>

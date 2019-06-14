<template>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center mt-3">Edit Category</h3>
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
                axios.put(`/admin/categories/${this.$route.params.id}`, this.category)
                    .then(res => {
                        this.showNotification('Category updated successfully!');
                        this.$router.push('/admin/categories');
                    })
                    .catch(err => {
                        console.log(err.data.errors[0]);
                    })
            }
        },
        created: function () {
            axios.get(`/admin/categories/${this.$route.params.id}`)
                .then(res => {
                    let data = res.data.data;
                    this.category = data;
                })
                .catch(err => {
                    console.log(err);
                })
        }
    }
</script>

<style scoped>

</style>

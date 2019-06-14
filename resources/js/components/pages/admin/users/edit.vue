<template>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center mt-3">Edit user</h3>
            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label for="title">Name</label>
                    <input type="text" class="form-control" id="title" v-model="user.name">
                </div>
                <div class="form-group">
                    <label for="title">Email</label>
                    <input type="text" class="form-control" id="email" v-model="user.email" readonly>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" class="form-control" id="role" v-model="user.role">
                        <option :selected="user.role == 'admin'" value="admin">Admin</option>
                        <option :selected="user.role == 'user'" value="user">User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Bio</label>
                    <textarea rows="5" class="form-control" id="description"
                              v-model="user.bio"></textarea>
                </div>
                <button class="btn btn-primary">Submit</button>
                <router-link class="btn btn-warning" :to="'/admin/users'">Cancel</router-link>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "admin.users.create",
        data: function () {
            return {
                user: {},
            }
        },
        methods: {
            submitForm() {
                axios.put(`/admin/users/${this.$route.params.id}`, this.user)
                    .then(res => {
                        this.showNotification('User updated successfully!')
                        this.$router.push('/admin/users');
                    })
                    .catch(err => {
                        console.log(err.data.errors[0]);
                    })
            },
        },
        created: function () {
            axios.get(`/admin/users/${this.$route.params.id}`)
                .then((res) => {
                    this.user = res.data.data;
                })
                .catch((err) => {
                    console.log(err)
                })
        }
    }
</script>

<style scoped>

</style>

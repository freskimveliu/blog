<template>
    <div>
        <div class="row align-items-center h-100">
            <div class="col-d-4 my-auto mx-auto">
                <div class="auth-box mt-5">
                    <div class="auth-title text-center">
                        Register
                    </div>
                    <form @submit.prevent="submitForm" id="authForm">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" v-model="name" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" v-model="email" class="form-control" id="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" v-model="password" class="form-control" id="password">
                        </div>
                        <button class="btn btn-lg btn-primary btn-block" type="submit">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "register",
        data: function () {
            return {
                email: '',
                name: '',
                password: '',
            }
        },
        methods: {
            submitForm() {
                console.log(this.$data);
                this.$store.dispatch('register', this.$data)
                    .then(response => {
                        this.$router.push('/');
                    })
            }
        }
    }
</script>

<style scoped>

</style>

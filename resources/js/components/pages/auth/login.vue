<template>
    <div>
        <div class="row align-items-center h-100">
            <div class="col-d-4 my-auto mx-auto">
                <div class="auth-box mt-5">
                    <div class="auth-title text-center">
                        Login
                    </div>
                    <form @submit.prevent="submitForm" id="authForm">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" v-model="email" class="form-control" id="email">
                            <span v-if="allerrors.email" :class="['text text-danger']">{{ allerrors.email[0] }}</span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" v-model="password" class="form-control" id="password">
                            <span v-if="allerrors.password" :class="['text text-danger']">{{ allerrors.password[0] }}</span>
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
        name: "login",
        data: function () {
            return {
                email: '',
                name: '',
                password: '',
                allerrors: [],
            }
        },
        methods: {
            submitForm() {
                this.allerrors = [];
                this.$store.dispatch('login', this.$data)
                    .then(response => {
                        this.$router.push('/');
                    })
                    .catch(err => {
                        this.allerrors = err.data.errors;
                        console.log(this.allerrors);
                    })
            }
        }
    }
</script>

<style scoped>

</style>

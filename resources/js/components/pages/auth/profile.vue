<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Profile</h3>
            </div>
            <div class="col-md-3">
                <div class="div-image">
                    <img :src="user.image_url" class="w-100">
                </div>
            </div>
            <div class="col-md-9">
                <form @submit.prevent="submitForm">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" v-model="user.name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" readonly id="email" v-model="user.email">
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control" accept="image/*" id="image" @change="onFileSelected">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-container">Is Private Account
                            <input type="checkbox" v-model="user.is_private_account">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea name="bio" id="bio" v-model="user.bio" class="form-control" rows="3"></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "profile",
        data: function () {
            return {
                user: {
                    name: this.$store.getters.user.name,
                    email: this.$store.getters.user.email,
                    image_url: this.$store.getters.user.image_url || 'https://i.stack.imgur.com/l60Hf.png',
                    image: '',
                    bio: this.$store.getters.user.bio || '',
                    is_private_account: this.$store.getters.user.is_private_account,
                }
            }
        },
        methods: {
            onFileSelected(event) {
                this.user.image = event.target.files[0];
                let src = URL.createObjectURL(event.target.files[0]);
                this.user.image_url = src
            },
            submitForm() {
                const form_data = new FormData();
                let user = this.user;

                Object.keys(this.user).forEach(function (key) {
                    if (key == 'is_private_account' && user[key] == true) {
                        user[key] = 1;
                    } else if (key == 'is_private_account' && user[key] == false) {
                        user[key] = 0;
                    }
                    form_data.append(key, user[key]);
                });

                this.$store.dispatch('updateUser', form_data)
                    .then(res => {
                        this.showNotification('Profile updated successfully!');
                    });
            }
        },
    }
</script>

<style scoped>
    .div-image {
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }

    .div-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
</style>

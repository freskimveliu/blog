<template>
    <div>
        <app-navbar v-if="isLogged"></app-navbar>
        <div id="wrapper">
            <div class="container mb-4">
                <router-view></router-view>
            </div>
        </div>
        <!-- set progressbar -->
        <vue-progress-bar></vue-progress-bar>
    </div>
</template>

<script>
    import navbar from './header/navbar';

    export default {
        name: "App",
        components: {
            'app-navbar': navbar,
        },
        computed:{
            isLogged(){
                return this.$store.getters.isLogged
            }
        }
    }
</script>

<style scoped>

</style>

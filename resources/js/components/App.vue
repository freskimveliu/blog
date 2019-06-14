<template>
    <div>
        <app-navbar></app-navbar>
        <div class="container mb-4">
            <router-view></router-view>
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
    }
</script>

<style scoped>

</style>

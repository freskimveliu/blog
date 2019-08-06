<template>
    <div>
        <app-navbar></app-navbar>
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
    }
</script>

<style scoped>

</style>

<template>
    <div class="row my-3">
        <div class="col-md-12 text-center">
            <strong>Total</strong>: {{ pagination.total }}
        </div>
        <div class="col-md-12 mt-3" v-show="1 != pagination.last_page">
            <nav>
                <ul class="pagination">
                    <li :class="{disabled:pagination.current_page === 1}" class="page-item">
                        <a href="#" @click="fetchItems(1)" class="page-link">&laquo;</a>
                    </li>
                    <li :class="{disabled:pagination.current_page === 1}" class="page-item">
                        <a href="#" @click="fetchItems(pagination.current_page - 1)" class="page-link">&lt;</a>
                    </li>
                    <li v-for="n in pagination.last_page" :key="n"
                        :class="{active:pagination.current_page === n}" class="page-item">
                        <a href="#" @click="fetchItems(n)" class="page-link">{{ n }}</a>
                    </li>
                    <li :class="{disabled: pagination.current_page === pagination.last_page}" class="page-item">
                        <a href="#" @click="fetchItems(pagination.current_page + 1)" class="page-link">&gt;</a>
                    </li>
                    <li :class="{disabled:pagination.current_page === pagination.last_page}" class="page-item">
                        <a href="#" @click="fetchItems(pagination.last_page)" class="page-link">&raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
    export default {
        name: "pagination",
        props: ['pagination'],
        methods: {
            fetchItems(id) {
                this.$emit('pagination', id);
            }
        }
    }
</script>

<style scoped>

</style>

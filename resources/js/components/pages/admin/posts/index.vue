<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Posts</h3>
            </div>
            <div class="col-md-12 mt-4">
                <router-link :to="'/admin/posts/create'" class="btn btn-primary mb-3">Create Post</router-link>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Author</th>
                        <th scope="col">Category</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(object,key) in class_objects">
                        <td style="width: 500px !important;"><router-link :to="'/admin/posts/'+object.id">{{ object.title }}</router-link></td>
                        <td>{{ object.user.name}}</td>
                        <td>{{ object.category.name }}</td>
                        <td>{{ object.created_at | full_date }}</td>
                        <td>
                            <router-link class="btn btn-sm btn-info mt-1 text-white" :to="'/admin/posts/'+object.id">
                                Show
                            </router-link>
                            <router-link class="btn btn-sm btn-success mt-1"
                                         :to="'/admin/posts/'+object.id+'/edit'">
                                Edit
                            </router-link>
                            <button @click="deleteItem(object.id,key)"
                                    class="btn btn-sm btn-danger mt-1">Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <app-pagination @pagination="fetchItems" :pagination="this.pagination"></app-pagination>
    </div>
</template>

<script>
    import pagination from '../../../partials/pagination';

    export default {
        name: "admin.posts.index",
        components: {
            'app-pagination': pagination,
        },
        data: function () {
            return {
                class_objects: [],
                pagination: {}
            }
        },
        created: function () {

            this.fetchItems();
        },
        methods: {
            deleteItem(id, key) {
                this.$swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete(`/admin/posts/${id}`)
                            .then(res => {
                                this.class_objects.splice(key, 1);
                                this.pagination.total--;
                                this.showNotification('Item deleted successfully!')
                            })
                            .catch(err => {

                            })
                    }
                })
            },

            fetchItems(page) {
                let url = `/admin/posts?page=${page || 1}`;
                axios.get(url)
                    .then((res) => {
                        let data = res.data;
                        this.class_objects = data.data;
                        let pagination = data.pagination;
                        this.pagination = {
                            current_page: pagination.current_page,
                            total: pagination.total,
                            last_page: pagination.last_page,
                            item_per_page: pagination.item_per_page,
                        };
                    })
                    .catch((err) => {
                        console.log(err)
                    })
            },
        }
    }
</script>

<style scoped>

</style>

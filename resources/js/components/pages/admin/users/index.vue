<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center mt-3">Users</h3>
            </div>
            <div class="col-md-12 mt-4">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(object,key) in class_objects">
                        <td class="text-center">
                            <img v-if="object.image_url" :src="object.image_url" class="table-img"/>
                            <span v-if="!object.image_url">--</span>
                        </td>
                        <td><router-link :to="'/admin/users/'+object.id">{{ object.name }}</router-link></td>
                        <td>{{ object.email }}</td>
                        <td class="text-capitalize">{{ object.role}}</td>
                        <td>{{ object.created_at | full_date }}</td>
                        <td>
                            <router-link class="btn btn-sm btn-info mt-1 text-white" :to="'/admin/users/'+object.id">
                                Show
                            </router-link>
                            <router-link class="btn btn-sm btn-success mt-1"
                                         :to="'/admin/users/'+object.id+'/edit'">
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
        name: "admin.users.index",
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
                        axios.delete(`/admin/users/${id}`)
                            .then(res => {
                                this.class_objects.splice(key, 1);
                                this.pagination.total--;
                                this.showNotification('Item deleted successfully!')
                            })
                            .catch(err => {
                                console.log(err);
                            })
                    }
                })
            },

            fetchItems(page) {
                let url = `/admin/users?page=${page || 1}`;
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
    .table-img{
        height:30px;
        width:30px;
        object-fit:cover;
        border-radius:50%;
    }
</style>

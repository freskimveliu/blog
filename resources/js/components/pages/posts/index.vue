<template>
    <div>
        <div class="row py-4">
            <div class="col-md-8">
                <div class=" justify-content-between">
                    <div class="d-inline-flex mb-4">
                        <div class="dropdown">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                Filter By Category
                            </button>
                            <div class="dropdown-menu">
                                <a v-for="category in categories"
                                   :class="inArray(category.id,selected_categories_ids) ? 'bg-primary text-white' : ''"
                                   @click="categoryClicked(category.id)" class="dropdown-item mb-1"
                                   href="#">{{ category.name }}</a>
                            </div>
                        </div>
                        <div v-if="$store.state.is_logged" class="">
                            <label class="checkbox-container">Only Favorites
                                <input type="checkbox" @click="myFavoritesClicked" v-model="my_favorites">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div class="w-100">
                        <input type="text" class="form-control search-input" placeholder="Search post"
                               @keyup.enter="fetchItems(1)" @change="fetchItems(1)"
                               v-model="search_query">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="post-wrapper mb-5" v-for="(post,index) in posts" v-if="posts.length > 0">
                    <div class="post-user-details m-3">
                        <div>
                            <div class="post-user-image d-inline-block">
                                <router-link :to="'/users/'+post.user.username">
                                    <img :src="post.user.image" class="">
                                </router-link>
                            </div>
                            <div class="post-user-name ml-3 d-inline-block font-weight-bold">
                                <router-link :to="'/users/'+post.user.username">
                                    {{ post.user.name }}
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="post-title m-3">
                        <router-link :to="'/posts/'+post.id">{{ post.title}}</router-link>
                    </div>
                    <router-link :to="'/posts/'+post.id">
                        <div class="post-image">
                            <img :src="post.image_url">
                        </div>
                    </router-link>
                    <div class="post-details">
                        <div class="d-flex justify-content-between mx-3 mt-3">
                            <div>
                                <div class="post-category">
                                    {{ post.category.name }}
                                </div>
                                <div class="post-views font-weight-bold">
                                    {{ post.open_by_users_count }} views
                                </div>
                            </div>
                            <div class="favorite" v-if="$store.state.is_logged"
                                 @click="favoriteAction(post.id,index)">
                                <img :src="post.is_favorite ? star_filled : star">
                            </div>
                        </div>
                        <div class="post-body m-3">
                            <div>
                                {{ post.short_description | str_limit(310)}}
                            </div>
                        </div>
                        <div class="post-created-date m-3">
                            {{ post.diff_for_humans }}
                        </div>
                    </div>
                </div>
                <div class="my-5 text-center" v-if="posts.length === 0">
                    <h5> We couldn't find any post.</h5>
                </div>
            </div>
            <div class="col-md-4 d-sm-none d-md-block">
                <div class="suggested-users">
                    <div class="suggested-users-title m-2 d-flex justify-content-between">
                        <div>
                            Suggestions For You
                        </div>
                        <div>
                            <a href="javascript:void(0)" @click="getSuggestedUsers">
                                <font-awesome-icon icon="sync"/>
                            </a>
                        </div>
                    </div>
                    <div class="suggested-users-content" v-if="suggested_users.length > 0">
                        <div v-for="(suggested_user,key) in suggested_users"
                             class="suggested-user m-2 d-flex justify-content-between">
                            <router-link :to="'/users/'+suggested_user.username">
                                <div class="user-details d-flex">
                                    <div class="user-image">
                                        <img :src="suggested_user.image"/>
                                    </div>
                                    <div class="user-name ml-2">
                                        {{ suggested_user.name }}
                                    </div>
                                </div>
                            </router-link>
                            <div class="actions">
                                <div v-if="suggested_user.my_relationship_status == null">
                                    <a href="javascript:void(0)" @click="followUser(suggested_user.username,key)">Follow</a>
                                </div>
                                <div class="text-capitalize"
                                     :class="suggested_user.my_relationship_status === 'following' ? 'text-primary' : 'text-warning'"
                                     v-if="suggested_user.my_relationship_status !== null">
                                    {{ suggested_user.my_relationship_status }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="m-2 font-weight-bold">
                        Now we don't have any suggestion for you.
                    </div>
                </div>
            </div>
        </div>
        <app-pagination @pagination="fetchItems" :pagination="this.pagination"></app-pagination>
    </div>
</template>

<script>
    import pagination from '../../partials/pagination'

    export default {
        name: "auth.posts.index",
        components: {
            'app-pagination': pagination,
        },
        data: function () {
            return {
                posts: [],
                pagination: {},
                star: 'https://image.flaticon.com/icons/svg/149/149220.svg',
                star_filled: 'https://image.flaticon.com/icons/svg/148/148839.svg',
                selected_categories_ids: [],
                categories: [],
                my_favorites: false,
                search_query: '',
                suggested_users: [],
            }
        },
        created: function () {
            this.fetchItems();
            this.fetchCategories();
            this.getSuggestedUsers();
        },
        methods: {
            fetchItems(page) {
                let url = `/posts?page=${page || 1}`;
                let data = {
                    category: this.selected_categories_ids,
                    favorites: this.my_favorites,
                    q: this.search_query,
                };
                axios.get(url, {
                    params: data,
                })
                    .then(res => {
                        this.posts = res.data.data;
                        let pagination = res.data.pagination;
                        this.pagination = {
                            current_page: pagination.current_page,
                            total: pagination.total,
                            last_page: pagination.last_page,
                            item_per_page: pagination.item_per_page,
                        };
                    })
                    .catch(err => {
                        console.log(err)
                    });
            },
            favoriteAction(post_id, index) {
                let post = this.posts[index];
                axios.post('/posts/' + post_id + '/favorite', {'is_favorite': !post.is_favorite})
                    .then(res => {
                        post.is_favorite = res.data.data;
                        if (post.is_favorite) {
                            this.showNotification('Post added to your favorites.');
                            post.image_src = this.star_filled;
                        } else {
                            this.showNotification('Post removed from your favorites.');
                            post.image_src = this.star;
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            fetchCategories() {
                axios.get('/categories')
                    .then((res) => {
                        let data = res.data;
                        this.categories = data.data;
                    })
                    .catch((err) => {
                        console.log(err)
                    })
            },
            categoryClicked(id) {
                let new_array = this.selected_categories_ids.filter(function (e) {
                    return (e === id);
                });
                if (new_array.length) {
                    for (let i = 0; i < this.selected_categories_ids.length; i++) {
                        if (this.selected_categories_ids[i] === id) {
                            this.selected_categories_ids.splice(i, 1);
                            i--;
                        }
                    }
                } else {
                    this.selected_categories_ids.push(id);
                }
                this.fetchItems(1);
            },
            myFavoritesClicked() {
                this.my_favorites = !this.my_favorites;
                this.fetchItems(1);
            },
            getSuggestedUsers() {
                axios.get('/suggested-users')
                    .then(res => {
                        let data = res.data;
                        this.suggested_users = data.data;
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            followUser(user_name, key) {
                axios.put(`/my/relationships/${user_name}`, {
                    action: 'follow'
                })
                    .then(res => {
                        let response_status = res.data.data.status;

                        if (response_status === 'following') {
                            this.suggested_users[key].my_relationship_status = 'following';
                        } else {
                            this.suggested_users[key].my_relationship_status = 'requested';
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
        },
    }
</script>

<style scoped>
    /* The checkbox-container */
    .checkbox-container {
        margin: 7px 60px;
    }
</style>

<template>
    <div>
        <div class="row my-4">
            <div class="col-md-12 ">
                <div class=" justify-content-between">
                    <div class="d-inline-flex mb-3">
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
                    <div class="d-inline-flex float-sm-left float-md-right">
                        <input type="text" class="form-control search-input" placeholder="Search post"
                               @keyup.enter="fetchItems(1)" @change="fetchItems(1)"
                               v-model="search_query">
                    </div>
                </div>
            </div>
        </div>
        <div class="row post my-2 mb-4 pb-4" v-for="(post,index) in posts">
            <div class="col-md-8">
                <div class="mb-3">
                    <router-link :to="'/posts/'+post.id" class="">
                        <img class="post-image" :src="post.image_url">
                    </router-link>
                    <div class="post-title">
                        <router-link :to="'/posts/'+post.id" class="">{{ post.title}}</router-link>
                    </div>
                    <div class="post-body mt-3">
                        {{ post.short_description | str_limit(250)}}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-inline-flex">
                    <div class="post-options">
                        <div>
                            <font-awesome-icon icon="list" class="icon alt"/>
                            <span>{{ post.category.name }}</span>
                        </div>
                        <div>
                            <font-awesome-icon icon="user" class="icon alt"/>
                            <router-link v-if="post.user" :to="'/users/'+post.user.username">{{ post.user.name }}
                            </router-link>
                        </div>
                        <div>
                            <font-awesome-icon icon="calendar" class="icon alt"/>
                            <span>{{ post.created_at | day}}</span>
                        </div>
                        <div>
                            <font-awesome-icon icon="clock" class="icon alt"/>
                            <span>{{ post.created_at | time }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-inline favorite float-right" v-if="$store.state.is_logged" @click="favoriteAction(post.id,index)">
                    <img :src="post.is_favorite ? star_filled : star">
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
            }
        },
        created: function () {
            this.fetchItems();
            this.fetchCategories();
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
            }
        },
    }
</script>

<style scoped>
    .post .post-image {
        width: 150px;
        display: block;
        float: left;
        margin-right: 15px;
        border-radius: 10px;
    }

    .post-options > div {
        margin-bottom: 10px;
    }

    .post-options {
        margin-top: 0;
    }

    /* The checkbox-container */
    .checkbox-container {
        margin: 7px 60px;
    }

    .search-input {
        min-width: 350px;
        font-size: 14px;
    }

    a {
        text-decoration: none;
    }
</style>

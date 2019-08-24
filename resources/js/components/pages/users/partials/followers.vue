<template>
    <div>
        <app-modal v-if="show_modal" @close="closeModal" @atTheBottom="getFollowers()">
            <template v-slot:header>
                <h6>Followers</h6>
            </template>
            <template v-slot:content>
                <div v-if="followers.length > 0">
                    <div v-for="(object,key) in followers"
                         class="d-flex user w-100 mb-2 justify-content-between">
                        <div class="user-details">
                            <router-link :to="'/users/'+object.user.username">
                                <div class="div-image">
                                    <img :src="object.user.image" alt="">
                                </div>
                                {{ object.user.name }}
                            </router-link>
                        </div>
                        <div class="buttons mr-2" v-if="!object.user.is_my_profile">
                            <button class="btn btn-sm text-capitalize"
                                    :class="object.user.relationship_status_as_a_friend_with_auth_user === 'unfollowing' ?
                                     'btn-primary' : 'btn-relationship'"
                                    @click="callRelationshipAction(key)">
                                {{ object.user.relationship_status_as_a_friend_with_auth_user === 'unfollowing' ?
                                'Follow' : object.user.relationship_status_as_a_friend_with_auth_user }}
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="my-2">We couldn't find any following.</div>
                </div>
            </template>
        </app-modal>
    </div>
</template>

<script>
    import Modal from '../../../partials/modal'

    export default {
        name: "users.followers",
        props: {
            show_followers: {
                required: true,
            }
        },
        components: {
            'app-modal': Modal,
        },
        data: function () {
            return {
                followers: [],
                is_requesting: false,
                show_modal: false,
                pagination: {
                    current_page: 0,
                    last_page: 1,
                }
            }
        },
        methods: {
            openModal() {
                this.show_modal = true;
            },
            closeModal() {
                this.show_modal = false;
                this.$emit('hideFollowers');
                this.followers = [];
                this.is_requesting = false;
                this.show_modal = false;
                this.pagination = {
                    current_page: 0,
                    last_page: 1,
                };
            },
            getFollowers() {
                if (this.is_requesting) {
                    return;
                }

                let page = this.pagination.current_page + 1;
                if (page > this.pagination.last_page) {
                    return false;
                }

                this.is_requesting = true;
                let username = this.$route.params.slug;

                axios.get(`/users/${username}/relationships/?status=followers&page=${page}`)
                    .then(res => {
                        let data = res.data;
                        this.pagination = data.pagination;
                        this.is_requesting = false;
                        this.openModal();
                        this.followers = this.followers.concat(data.data);
                    })
                    .catch(err => {
                        this.is_requesting = false;
                        console.log(err);
                    });
            },
            relationshipAction(params) {
                let username = params.username;
                let action = params.action;
                let key = params.key;
                let status = params.status;

                axios.put(`/my/relationships/${username}`, {
                    action: action
                })
                    .then(res => {
                        this.followers[key].user = res.data.data;

                        this.$emit('relationshipStatuses', {
                            old_status: status,
                            new_status: res.data.data.relationship_status_as_a_friend_with_auth_user,
                        })
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            unFollowAlert(title, params) {
                this.showConfirmAlert(title, this.relationshipAction, params);
            },
            callRelationshipAction(key) {
                let user = this.followers[key].user;

                if (!user) {
                    return;
                }

                let params = {
                    username: user.username,
                    action: user.next_relationship_action_as_a_friend_with_auth_user,
                    status: user.relationship_status_as_a_friend_with_auth_user,
                    key: key,
                };

                if (params.status === 'following' && params.action === 'unfollow') {
                    let title = `Do you want to un follow ${user.name}?`;
                    return this.unFollowAlert(title, params);
                }

                return this.relationshipAction(params);
            }
        },
        watch: {
            show_followers(to, from) {
                if (from === to) {
                    return false;
                }

                if (to === true) {
                    this.getFollowers();
                }

                if (to === false) {
                    this.closeModal();
                }
            },
            $route(to, from) {
                this.closeModal();
            }
        }
    }
</script>

<style scoped>

</style>

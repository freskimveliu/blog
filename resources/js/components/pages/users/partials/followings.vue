<template>
    <div>
        <app-modal v-if="show_modal" @close="closeModal" @atTheBottom="getFollowings()">
            <template v-slot:header>
                <h6>Followings</h6>
            </template>
            <template v-slot:content>
                <div v-if="followings.length > 0">
                    <div v-for="(object,key) in followings"
                         class="d-flex user w-100 mb-2 justify-content-between">
                        <div class="user-details">
                            <router-link :to="'/users/'+object.friend.username">
                                <div class="div-image">
                                    <img :src="object.friend.image" alt="">
                                </div>
                                {{ object.friend.name }}
                            </router-link>
                        </div>
                        <div class="buttons mr-2" v-if="!object.friend.is_my_profile">
                            <button class="btn btn-sm text-capitalize"
                                    :class="object.friend.relationship_status_as_a_friend_with_auth_user === 'unfollowing' ?
                                     'btn-primary' : 'btn-relationship'"
                                    @click="callRelationshipAction(key)">
                                {{ object.friend.relationship_status_as_a_friend_with_auth_user === 'unfollowing' ?
                                'Follow' : object.friend.relationship_status_as_a_friend_with_auth_user }}
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
        name: "users.followings",
        props: {
            show_followings: {
                required: true,
            }
        },
        components: {
            'app-modal': Modal,
        },
        data: function () {
            return {
                followings: [],
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
                this.$emit('hideFollowings');
                this.followings = [];
                this.is_requesting = false;
                this.show_modal = false;
                this.pagination = {
                    current_page: 0,
                    last_page: 1,
                };
            },
            getFollowings() {
                if (this.is_requesting) {
                    return;
                }

                let page = this.pagination.current_page + 1;
                if (page > this.pagination.last_page) {
                    return false;
                }

                this.is_requesting = true;
                let username = this.$route.params.slug;

                axios.get(`/users/${username}/relationships/?status=followings&page=${page}`)
                    .then(res => {
                        let data = res.data;
                        this.pagination = data.pagination;
                        this.is_requesting = false;
                        this.openModal();
                        this.followings = this.followings.concat(data.data);
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
                        this.followings[key].friend = res.data.data;

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
                let friend = this.followings[key].friend;

                if (!friend) {
                    return;
                }

                let params = {
                    username: friend.username,
                    action: friend.next_relationship_action_as_a_friend_with_auth_user,
                    status: friend.relationship_status_as_a_friend_with_auth_user,
                    key: key,
                };

                if (params.status === 'following' && params.action === 'unfollow') {
                    let title = `Do you want to un follow ${friend.name}?`;
                    return this.unFollowAlert(title, params);
                }

                return this.relationshipAction(params);
            }
        },
        watch: {
            show_followings(to, from) {
                if (from === to) {
                    return false;
                }

                if (to === true) {
                    this.getFollowings();
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

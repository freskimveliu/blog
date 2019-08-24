<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container">

                        <div class="modal-header">
                            <div class="d-flex w-100 justify-content-between h-20">
                                <slot name="header">
                                    <div>
                                        Modal
                                    </div>
                                </slot>
                                <div>
                                    <div @click="$emit('close')" class="close">
                                        <font-awesome-icon icon="times-circle" class="icon alt"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-body">
                            <div class="modal-content" @scroll="onScroll">
                                <slot name="content"></slot>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
    export default {
        methods: {
            onScroll({target: {scrollTop, clientHeight, scrollHeight}}) {
                if (scrollTop + clientHeight >= scrollHeight) {
                    this.$emit('atTheBottom')
                }
            }
        },
    }
</script>
<style>
    .close {
        cursor: pointer;
    }

    .modal-mask {
        position: fixed;
        z-index: 100;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }

    .modal-container {
        width: 400px;
        margin: 0 auto;
        padding: 20px 30px;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-enter {
        opacity: 0;
    }

    .modal-leave-active {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave-active .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }

    .modal-container {
        border-radius: 10px;
        padding: 0;

    }

    .modal-header {
        padding: 10px 15px;
    }

    .modal-body {
        padding: 10px 15px;
    }

    .modal-container .modal-content {
        max-height: 335px;
        overflow-y: scroll;
        border: 0;
    }

    .modal-container .user-details {
        max-width: 205px;
        overflow: hidden;
        margin-right: 15px;
        margin-top: 3px;
    }

    .modal-container .div-image {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 5px;
        display: inline-block;
        border: 1px solid gray;
    }

    .modal-container .div-image img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .modal-container .h-20 {
        height: 20px;
    }

    .modal-container .close {
        padding: 0;
        margin: 0;
        font-size: 20px;
    }

    .modal-container .btn {
        padding: 3px 15px;
    }

    .modal-container .btn-relationship {
        border: 1px solid #dbdbdb;
    }
</style>

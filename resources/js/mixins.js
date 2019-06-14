import Vue from 'vue';
import VueSweetalert2 from 'vue-sweetalert2';

Vue.use(VueSweetalert2);

const mixins = Vue.mixin({
    methods: {
        showNotification(title, type) {
            this.$swal({
                title: title,
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                type: type || 'success',
            })
        },
    }
})

export default mixins;

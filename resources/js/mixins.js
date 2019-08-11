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
        inArray(needle, haystack) {
            let length = haystack.length;
            for (let i = 0; i < length; i++) {
                if (haystack[i] == needle) return true;
            }
            return false;
        },
        default_image() {
            return 'https://www.eltis.org/sites/default/files/styles/web_quality/public/default_images/photo_default_2.png?itok=skpFa9MI';
        },
        default_user_image() {
            return '/images/defaults/profile.png';
        },
        default_loader_image(){
            return '/images/defaults/profile.png';
        }
    }
})

export default mixins;

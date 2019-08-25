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
        showErrorNotification(title){
            this.showNotification(title,'error');
        },
        showSuccessNotification(title){
            this.showNotification(title,'success');
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
        },
        showConfirmAlert(title,action,params){
            this.$swal({
                title: title,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!',
                confirmButtonClass: 'btn btn-sm btn-primary',
                buttonsStyling: true,
            }).then((result) => {
                if (result.value) {
                    return action(params);
                }
            })
        }
    }
})

export default mixins;

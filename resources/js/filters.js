import Vue from 'vue';
import moment from 'moment';

Vue.filter('full_date', function (value) {
    return moment(value).format('D MMM Y HH:mm');
});

Vue.filter('time', function (value) {
    return moment(value).format('HH:mm');
});

Vue.filter('day', function (value) {
    return moment(value).format('D MMM Y');
});

Vue.filter('str_limit',function(value,number){
   return value.substring(0,number)+"...";
});

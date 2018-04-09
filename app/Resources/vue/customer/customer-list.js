import 'vuetify/dist/vuetify.min.css'

import 'babel-polyfill'
import Vue from 'vue'
import Vuetify from 'vuetify'
import CustomerList from './CustomerList.vue'

Vue.use(Vuetify)

const vm = new Vue({
    el: '#app',
    template: '<customer-list></customer-list>',
    components: { CustomerList }
})
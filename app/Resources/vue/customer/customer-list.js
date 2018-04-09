import 'babel-polyfill'
import Vue from 'vue'
import { ServerTable, Event } from 'vue-tables-2'
import CustomerList from './CustomerList.vue'

Vue.use(ServerTable)

const vm = new Vue({
    el: '#app',
    template: '<customer-list></customer-list>',
    components: { CustomerList }
})
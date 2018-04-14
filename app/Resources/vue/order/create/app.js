import "babel-polyfill"
import 'vue-loading-overlay/dist/vue-loading.min.css'
import Vue from 'vue'
import { ServerTable, Event } from 'vue-tables-2'
import * as uiv from 'uiv'
import Loading from 'vue-loading-overlay'
import CreateOrder from './CreateOrder'

Vue.use(ServerTable)
Vue.use(uiv)
Vue.use(Loading)

const vm = new Vue({
    el: '#app',
    template: '<create-order></create-order>',
    components: { CreateOrder }
})


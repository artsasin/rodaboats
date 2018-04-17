<template>
    <div id="customer-list-container">
        <v-server-table :url="dataUrl" :columns="columns" :options="options" ref="customerDataTable">
            <div slot="actions" slot-scope="props">
                <a class="btn btn-sm btn-default" @click="edit(props.row)">
                    <i class="fa fa-edit"></i>
                </a>
            </div>
            <button slot="afterFilter" class="btn btn-default" @click="newCustomer()">
                <i class="fa fa-plus"></i> New customer
            </button>
        </v-server-table>
        <new-customer-modal
                v-model="open"
                :model="customer"
                :countries="countries"
                :languages="langs"
                @saved="onSavedCustomer"
        />
    </div>
</template>

<script>
    import Vue from 'vue'
    import NewCustomerModal from '../customer/NewCustomerModal'

    export default {
        name: "customer-list",
        components: { NewCustomerModal },
        data () {
            return {
                customer: Vue.util.extend({}, window.customerModel),
                countries: window.countries,
                langs: window.langs,
                open: false,
                columns: ['firstName', 'lastName', 'phoneNumber', 'actions'],
                dataUrl: Routing.generate('app.customer.get.list'),
                options: {
                    headings: {
                        firstName: 'First name',
                        lastName: 'Last name',
                        phoneNumber: 'Phone number'
                    },
                    sortable: [],
                    highlightMatches: true
                }
            }
        },
        methods: {
            edit (model) {
                this.customer = model
                this.open = true
            },
            newCustomer () {
                this.customer = Vue.util.extend({}, window.customerModel)
                this.open = true
            },
            onSavedCustomer (customer) {
                this.$refs.customerDataTable.refresh()
                this.$refs.customerDataTable.setFilter(customer.firstName + ' ' + customer.lastName)
            }
        }
    }
</script>

<style>
    .VueTables__search-field {
        float: left;
        margin-right: 20px;
    }
    .loading-overlay {
        z-index: 1999 !important;
    }
    .form-group.required .control-label:after {
        content:"*";
        color:red;
    }
</style>
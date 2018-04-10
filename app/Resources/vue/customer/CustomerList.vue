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
        <modal v-model="open" :title="editCustomerModalTitle">
            <alert v-if="saveError" type="danger">
                Fill all required fields
            </alert>
            <div class="row form-group">
                <div class="col-md-6 form-group required">
                    <label class="control-label">First name</label>
                    <input class="form-control input-sm" type="text" v-model="customer.firstName" />
                </div>
                <div class="col-md-6 form-group required">
                    <label class="control-label">Last name</label>
                    <input class="form-control input-sm" type="text" v-model="customer.lastName" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6 form-group required">
                    <label class="control-label">Country</label>
                    <select class="form-control input-sm" v-model="customer.country">
                        <option v-for="country in countries" v-bind:value="country.value">
                            {{ country.text }}
                        </option>
                    </select>
                </div>
                <div class="col-md-6 form-group required">
                    <label class="control-label">Language</label>
                    <select class="form-control input-sm" v-model="customer.language">
                        <option v-for="lang in langs" v-bind:value="lang.value">
                            {{ lang.text }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group required">
                    <label class="control-label">Phone number</label>
                    <input class="form-control input-sm" type="text" v-model="customer.phoneNumber" />
                </div>
                <div class="col-md-6">
                    <label>E-mail</label>
                    <input class="form-control input-sm" type="text" v-model="customer.email" />
                </div>
            </div>
            <div slot="footer">
                <btn @click="open=false">Cancel</btn>
                <btn type="success" @click="save()">Save</btn>
            </div>
        </modal>
    </div>
</template>

<script>
    import Vue from 'vue'
    import axios from 'axios'

    export default {
        name: "customer-list",
        data () {
            return {
                customer: Vue.util.extend({}, window.customerModel),
                countries: window.countries,
                langs: window.langs,
                open: false,
                saveError: false,
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
        computed: {
            editCustomerModalTitle () {
                return (this.customer.id !== null) ? 'Edit customer' : 'New customer'
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
            save () {
                let loading = this.$loading.show()
                const self = this
                axios.post(Routing.generate('app.customer.save'), this.customer)
                  .then(response => {
                      loading.hide()
                      if (response.data.status !== 0) {
                          self.saveError = true;
                      } else {
                          self.saveError = false;
                          self.open = false;
                          self.$refs.customerDataTable.refresh()
                          self.$refs.customerDataTable.setFilter(response.data.customer.firstName + ' ' + response.data.customer.lastName)
                      }
                  })
                  .catch(error => {
                      console.log(error)
                  })
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
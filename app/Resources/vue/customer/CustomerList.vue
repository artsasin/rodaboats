<template>
    <v-app>
        <v-data-table
                :headers="headers"
                :items="items"
                :search="search"
                :pagination.sync="pagination"
                :total-items="totalItems"
                :loading="loading"
                class="elevation-1"
        >
            <template slot="items" slot-scope="props">
                <td>{{ props.item.firstName }}</td>
                <td class="text-xs-right">{{ props.item.lastName }}</td>
                <td class="text-xs-right">{{ props.item.phoneNumber }}</td>
            </template>
        </v-data-table>
    </v-app>
</template>

<script>
    import axios from 'axios';

    export default {
        name: "customer-list",
        data () {
            return {
                search: '',
                totalItems: 0,
                items: [],
                loading: true,
                pagination: {},
                headers: [
                    {
                        text: 'First name',
                        sortable: false,
                        value: 'firstName'
                    },
                    {
                        text: 'Last name',
                        sortable: false,
                        value: 'lastName'
                    },
                    {
                        text: 'Phone number',
                        sortable: false,
                        value: 'phoneNumber'
                    }
                ]
            }
        },
        watch: {
            pagination: {
                handler () {
                    this.loadCustomers()
                },
                deep: true
            }
        },
        mounted () {
            this.loadCustomers()
        },
        methods: {
            loadCustomers () {
                const self = this;
                const { sortBy, descending, page, rowsPerPage } = this.pagination
                const params = {
                    sortBy: sortBy,
                    descending: descending,
                    page: page,
                    rowsPerPage: rowsPerPage
                }
                axios.get(Routing.generate('app.customer.get.list'), { params: params })
                  .then(response => {
                      self.loading = false
                      self.items = response.data.items
                      self.totalItems = response.data.total
                  })
                  .catch(error => {
                      console.log(error)
                  })
            }
        }
    }
</script>

<style>

</style>
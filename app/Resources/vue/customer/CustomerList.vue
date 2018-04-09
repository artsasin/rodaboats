<template>
    <div id="customer-list-container">
        <v-server-table :url="dataUrl" :columns="columns" :options="options">
            <a slot="edit" slot-scope="props" class="btn btn-sm btn-default" @click="edit(props.row.id)">
                <i class="fa fa-edit"></i>
            </a>
        </v-server-table>
    </div>
</template>

<script>
    export default {
        name: "customer-list",
        data () {
            return {
                columns: ['firstName', 'lastName', 'phoneNumber', 'edit'],
                dataUrl: Routing.generate('app.customer.get.list'),
                options: {
                    headings: {
                        firstName: 'First name',
                        lastName: 'Last name',
                        phoneNumber: 'Phone number'
                    }
                }
            }
        },
        methods: {
            edit (id) {
                console.log(id)
            }
        }
    }
</script>

<style>

</style>
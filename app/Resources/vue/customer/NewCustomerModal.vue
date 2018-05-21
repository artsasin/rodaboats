<template>
    <modal v-model="open" :title="title">
        <alert v-if="saveError" type="danger">
            {{ errorMessage }}
        </alert>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group required">
                <label class="control-label">First name</label>
                <input class="form-control input-sm" type="text" v-model="customer.firstName" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group required">
                <label class="control-label">Last name</label>
                <input class="form-control input-sm" type="text" v-model="customer.lastName" />
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group required">
                <label class="control-label">Country</label>
                <select class="form-control input-sm" v-model="customer.country">
                    <option v-for="country in countries" v-bind:value="country.value">
                        {{ country.text }}
                    </option>
                </select>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group required">
                <label class="control-label">Language</label>
                <select class="form-control input-sm" v-model="customer.language">
                    <option v-for="lang in languages" v-bind:value="lang.value">
                        {{ lang.text }}
                    </option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group required">
                <label class="control-label">Phone number</label>
                <input class="form-control input-sm" type="text" v-model="customer.phoneNumber" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group">
                <label>E-mail</label>
                <input class="form-control input-sm" type="text" v-model="customer.email" />
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group">
                <label class="control-label">ID number</label>
                <input class="form-control input-sm" type="text" v-model="customer.identityNumber" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-group">
                <label>License number</label>
                <input class="form-control input-sm" type="text" v-model="customer.licenseNumber" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-md-12 col-sm-12 col-xs-12 form-group">
                <label class="control-label">Comment</label>
                <textarea class="form-control" v-model="customer.comment"></textarea>
            </div>
        </div>
        <div slot="footer">
            <btn @click="open=false">Cancel</btn>
            <btn type="success" @click="save()">Save</btn>
        </div>
    </modal>
</template>

<script>
    import Vue from 'vue'
    import axios from 'axios'

    export default {
        name: "new-customer-modal",
        props: {
            model: {
                type: Object,
                required: true
            },
            countries: {
                type: Array,
                required: true
            },
            languages: {
                type: Array,
                required: true
            },
            value: {
                type: Boolean,
                default: false
            }
        },
        data () {
            return {
                customer: null,
                saveError: false,
                open: this.value,
                errorMessage: ''
            }
        },
        created () {
            this.customer = Vue.util.extend({}, this.model);
        },
        watch: {
            value (v) {
                this.open = v;
            },
            open (v) {
                this.$emit('input', v);
            },
            model (v) {
                this.customer = v;
            }
        },
        computed: {
            title () {
                return (this.customer.id !== null) ? 'Edit customer' : 'New customer';
            }
        },
        methods: {
            save () {
                let loading = this.$loading.show()
                const self = this
                axios.post(Routing.generate('app.customer.save'), this.customer)
                    .then(response => {
                        loading.hide()
                        if (response.data.status !== 0) {
                            self.saveError = true;
                            self.errorMessage = response.data.message;
                        } else {
                            self.saveError = false;
                            self.open = false;
                            self.$emit('saved', response.data.payload);
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
    .form-group.required .control-label:after {
        content:"*";
        color:red;
    }
</style>
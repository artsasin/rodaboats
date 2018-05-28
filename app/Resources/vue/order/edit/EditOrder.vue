<template>
    <div id="create-order-wrapper">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h1 class="page-header">
                    <span class="header-nav" v-show="order.id !== null">
                        <button type="button" class="btn btn-default" @click="save">
                            <i class="fa fa-save"></i>&nbsp;Save order
                        </button>
                        <a :href="order_log_link" class="btn btn-default">
                            <i class="fa fa-clock-o"></i> Log
                        </a>
                        <button class="btn btn-primary" @click="closeOrderModalOpen" :disabled="!allowEdit">
                            <i class="fa fa-check-square"></i> Close
                        </button>
                        <button class="btn btn-danger" @click="cancelOrderModalOpen" :disabled="!allowEdit">
                            <i class="fa fa-remove"></i> Cancel
                        </button>
                    </span>
                    {{ page_title }}
                    <small style="margin-left: 20px;" v-if="order.id !== null">
                        status: <span class="label" :class="status_label_class">{{ status_text }}</span>
                        <span v-if="order.status === 2">by {{ cancellationReason }}</span>
                    </small>
                </h1>
            </div>
        </div>
        <div class="row" v-for="(item, index) in alerts">
            <div class="col-lg-12 col-m-12 col-sm-12 col-xs-12">
                <alert :type="item.type" dismissible :key="item.key" @dismissed="alerts.splice(index, 1)">
                    {{ item.content }}
                </alert>
            </div>
        </div>
        <div class="row">
            <!-- customer block -->
            <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Customer
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row form-group">
                            <dropdown v-model="customerAutocomplete" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 customer-autocomplete">
                                <label class="control-label">Customer name</label>
                                <div class="input-group">
                                    <input class="form-control" ref="customerSearch" type="text" v-model="customerQuery" @focus="customerFocused()" :readonly="customer !== null" />
                                    <div class="input-group-btn">
                                        <btn type="default" v-show="customer !== null" @click="clearCustomer()" :disabled="!allowEdit">
                                            <i class="fa fa-close"></i>
                                        </btn>
                                        <btn type="default" @click="customerPicker=true" :disabled="!allowEdit">
                                            <i class="fa fa-search"></i>
                                        </btn>
                                        <btn type="default" @click="newCustomer" :disabled="!allowEdit">
                                            <i class="fa fa-plus"></i>
                                        </btn>
                                    </div>
                                </div>
                                <template slot="dropdown">
                                    <li v-for="suggest in customerSuggestions">
                                        <a role="button" @click="selectCustomer(suggest)">
                                            {{ suggest.firstName }}&nbsp;{{ suggest.lastName }},&nbsp;{{ suggest.phoneNumber }}
                                        </a>
                                    </li>
                                </template>
                            </dropdown>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Language</label>
                                <input class="form-control" type="text" readonly="readonly" :value="customer_language">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Country</label>
                                <input class="form-control" type="text" readonly="readonly" :value="customer_country">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Phone number</label>
                                <input class="form-control" type="text" readonly="readonly" :value="customer_phoneNumber">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">E-mail</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" readonly="readonly" :value="customer_email">
                                    <div class="input-group-btn">
                                        <btn type="default" :disabled="customer === null" @click="editCustomerEmailModalOpen">
                                            <i class="fa fa-edit"></i>
                                        </btn>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- booking data block -->
            <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                <div class="panel" :class="{'panel-default': !validation.isConflicts, 'panel-danger': validation.isConflicts }">
                    <div class="panel-heading">
                        <h3 class="panel-title" v-html="booking_data_title"></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Order type</label>
                                <select class="form-control" v-model="order.type" :disabled="!allowEdit">
                                    <option value="" disabled="disabled">...</option>
                                    <option v-for="type in orderTypes" :value="type.code">{{ type.text }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <dropdown class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label class="control-label">Date</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" v-model="order.date" readonly="readonly">
                                    <div class="input-group-btn">
                                        <btn class="dropdown-toggle" :disabled="!allowEdit"><i class="glyphicon glyphicon-calendar"></i></btn>
                                    </div>
                                </div>
                                <template slot="dropdown">
                                    <li>
                                        <date-picker v-model="order.date"/>
                                    </li>
                                </template>
                            </dropdown>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label class="control-label">Boat</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" readonly="readonly" :value="boatName">
                                    <div class="input-group-btn">
                                        <dropdown append-to-body menu-right class="boat-dropdown">
                                            <btn type="default" class="dropdown-toggle" :disabled="!allowEdit"><span class="caret"></span></btn>
                                            <template slot="dropdown">
                                                <li v-for="location in locations">
                                                    <strong>{{ location }}</strong>
                                                    <ul>
                                                        <li v-for="boat in locationBoats(location)">
                                                            <a role="button" @click="selectBoat(boat)" :class="{ 'selected': selectedBoat === boat }">
                                                                {{ boat.name }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </template>
                                        </dropdown>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group" :class="{'has-error': validation.isConflicts}">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label class="control-label">Start</label>
                                <table class="table table-condensed" style="margin-bottom: 0">
                                    <tr>
                                        <td>
                                            <select class="form-control" v-model="startHour" :disabled="!allowEdit">
                                                <option v-for="hour in hours" :value="hour">{{ hour }}</option>
                                            </select>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <select class="form-control" v-model="startMinute" :disabled="!allowEdit">
                                                <option v-for="minute in minutes" :value="minute">{{ minute }}</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label class="control-label">Duration</label>
                                <select class="form-control" v-model="duration" :disabled="!allowEdit">
                                    <option value="">pick a duration</option>
                                    <option value="1">1 hour</option>
                                    <option value="2">2 hours</option>
                                    <option value="4">4 hours</option>
                                    <option value="8">8 hours</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label class="control-label">End</label>
                                <table class="table table-condensed" style="margin-bottom: 0">
                                    <tr>
                                        <td>
                                            <select class="form-control" v-model="endHour" :disabled="!allowEdit">
                                                <option v-for="hour in hours" :value="hour">{{ hour }}</option>
                                            </select>
                                        </td>
                                        <td>:</td>
                                        <td>
                                            <select class="form-control" v-model="endMinute" :disabled="!allowEdit">
                                                <option v-for="minute in minutes" :value="minute">{{ minute }}</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <label class="control-label">Peoples</label>
                                <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" @click="decrementPeopleCount()" :disabled="!allowEdit">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </span>
                                    <input class="form-control" type="text" v-model="order['numberOfPeople']" />
                                    <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" @click="incrementPeopleCount()" :disabled="!allowEdit">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </span>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                <label class="control-label">Booked by</label>
                                <input class="form-control" type="text" v-model="order['bookedBy']" :disabled="!allowEdit"/>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label class="control-label">Extras</label>
                                <div class="input-group">
                                    <input class="form-control" type="text" readonly="readonly" :value="order_extras">
                                    <div class="input-group-btn">
                                        <dropdown
                                                ref="extras_dropdown"
                                                :not-close-elements="extras_dropdown_$ele"
                                                menu-right
                                                v-model="extras_dropdown_opened"
                                                class="dropdown-form"
                                        >
                                            <btn type="default" class="dropdown-toggle" :disabled="!allowEdit">
                                                <i class="fa fa-check-square"></i>
                                            </btn>
                                            <template slot="dropdown">
                                                <li class="checkbox" v-for="extra in extras">
                                                    <label>
                                                        <input type="checkbox" :value="extra['code']" v-model="order['extra']"> {{ extra.text }}
                                                    </label>
                                                </li>
                                                <li>
                                                    <btn block type="primary" @click="extras_dropdown_opened=false">Apply</btn>
                                                </li>
                                            </template>
                                        </dropdown>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- price block -->
        </div>
        <div class="row">
            <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Price
                                </h3>
                            </div>
                            <div class="panel-body">
                                <button class="btn btn-default btn-sm" style="position: absolute; top: 5px; right: 25px;" :disabled="order.boatId === null" @click="suggestPrice">
                                    <i class="fa fa-refresh"></i>&nbsp;suggest
                                </button>
                                <div class="row form-group">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label class="control-label">Rent</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input class="form-control" type="tel" v-model.lazy="order_rent" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label class="control-label">Rent discount</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input class="form-control" type="text" v-model.lazy="order_rent_discount" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Rent payment method</label>
                                        <select class="form-control" v-model="order['paymentMethodRent']">
                                            <option value="" disabled="disabled">...</option>
                                            <option v-for="pm in paymentMethods" :value="pm.code">{{ pm.text }}</option>
                                            0                                    </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Petrol cost</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input class="form-control" type="text" v-model.lazy="order_petrol_cost" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Deposit</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input class="form-control" type="text" v-model.lazy="order_deposit" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Deposit payment method</label>
                                        <select class="form-control" v-model="order['paymentMethodDeposit']">
                                            <option value="" disabled="disabled">...</option>
                                            <option v-for="pm in paymentMethods" :value="pm.code">{{ pm.text }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Commission and damage
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Commission</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text" class="form-control" v-model.lazy="commission_amount" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Commission paid to</label>
                                        <input type="text" class="form-control" v-model="order.commissionPaidTo" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Damage</label>
                                        <input type="text" class="form-control" v-model="order.damage" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Damage/correction amount</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">&euro;</span>
                                            <input type="text" class="form-control" v-model.lazy="damage_amount" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="control-label">Damage payment method</label>
                                        <select class="form-control" v-model="order.paymentMethodDamage">
                                            <option value="" disabled="disabled">...</option>
                                            <option v-for="method in paymentMethods" :value="method.code">{{ method.text }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Comments
                        </h3>
                    </div>
                    <div class="panel-body">
                        <textarea class="form-control" v-model="order['comments']" style="height: 58px;" :disabled="!allowEdit"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <button type="button" class="btn btn-lg btn-default" @click="save">
                    <i class="fa fa-save"></i>&nbsp;Save order
                </button>
            </div>
        </div>
        <modal v-model="customerPicker" title="Customer search" size="lg" @show="onCustomerPickerShow">
            <v-server-table
                    :url="customerDatatable.dataUrl"
                    :columns="customerDatatable.columns"
                    :options="customerDatatable.options"
                    ref="customerDatatable"
                    @row-click="customer_dt_row_click"
            />
        </modal>
        <modal v-model="cancelOrderModalOpened" title="Cancel booking">
            <div class="row" v-if="cancelOrderError">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <alert type="danger">{{ cancelOrderErrorMessage }}</alert>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label class="control-label">Cancellation reason</label>
                        <select class="form-control" v-model="order.cancellation">
                            <option value="" disabled="disabled">...</option>
                            <option v-for="reason in cancellationReasons" :value="reason.code">{{ reason.text }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div slot="footer">
                <btn @click="cancelOrderModalOpened=false">Cancel</btn>
                <btn type="primary" @click="cancelOrder">Ok</btn>
            </div>
        </modal>
        <modal v-model="editCustomerEmail.modalOpened" title="Edit email">
            <div class="row" v-if="editCustomerEmail.errorMessage !== ''">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <alert type="danger">{{ editCustomerEmail.errorMessage }}</alert>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <p>Current email: <strong>{{ customer_email }}</strong></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label class="control-label">New email address</label>
                        <input type="email" class="form-control" v-model="editCustomerEmail.newEmail" />
                    </div>
                </div>
            </div>
            <div slot="footer">
                <btn @click="editCustomerEmail.modalOpened=false">Cancel</btn>
                <btn type="primary" @click="updateCustomerEmail">Ok</btn>
            </div>
        </modal>
        <new-customer-modal
                v-model="createCustomer.modalOpened"
                :model="createCustomer.model"
                :countries="list_countries"
                :languages="list_languages"
                @saved="onCustomerSaved"
        />
    </div>
</template>

<script>
    import Vue from 'vue'
    import axios from 'axios'
    import NewCustomerModal from '../../customer/NewCustomerModal'

    export default {
        name: "edit-order",
        components: { NewCustomerModal },
        data () {
            return {
                order: window.rodaboats.order,
                customer: null,
                duration: '',
                customerPicker: false,
                customerAutocomplete: false,
                customerSuggestions: [],
                query: '',
                extras_dropdown_$ele: [],
                extras_dropdown_opened: false,
                createCustomer: {
                    modalOpened: false,
                    model: Vue.util.extend({}, window.rodaboats.referenceData.customerModel)
                },
                editCustomerEmail: {
                    modalOpened: false,
                    newEmail: '',
                    errorMessage: ''
                },
                validation: {
                    isConflicts: false
                },
                customerDatatable: {
                    dataUrl: Routing.generate('app.customer.get.list'),
                    columns: ['firstName', 'lastName', 'phoneNumber', 'email'],
                    options: {
                        headings: {
                            firstName: 'First name',
                            lastName: 'Last name',
                            phoneNumber: 'Phone number'
                        },
                        sortable: [],
                        highlightMatches: true
                    }
                },
                closeOrderModalOpened: false,
                closeOrderError: false,
                closeOrderErrorMessage: '',
                cancelOrderModalOpened: false,
                cancelOrderError: false,
                cancelOrderErrorMessage: '',
                timeouts: {
                    checkConflict: null
                },
                alerts: []
            }
        },
        mounted () {
            this.extras_dropdown_$ele.push(this.$refs.extras_dropdown.$el);
            if (this.order.id !== null) {
                this.customer = window.rodaboats.customer;
            }
            this.checkConflicts();
        },
        methods: {
            updateCustomerEmail() {
                let loading = this.$loading.show();
                const self = this;
                const p = Vue.util.extend({}, this.customer);
                p.email = this.editCustomerEmail.newEmail;
                axios.post(Routing.generate('app.customer.save'), p)
                    .then((response) => {
                        loading.hide();
                        self.customer.email = response.data.payload.email;
                        self.editCustomerEmail.modalOpened = false;
                        self.$notify({
                            title: 'Update customer',
                            content: 'The customer email was updated.',
                            duration: 0,
                            type: 'success'
                        });
                    })
                    .catch((error) => {
                        console.error(error);
                        loading.hide();
                    })
            },
            editCustomerEmailModalOpen() {
                this.editCustomerEmail.newEmail = this.customer_email;
                this.editCustomerEmail.modalOpened = true;
            },
            closeOrderModalOpen () {
                this.closeOrderModalOpened = true;
            },
            closeOrder () {
                const self = this;
                let loading = self.$loading.show();
                this.apiRequest(Routing.generate('app_api_orders_close'))
                  .then((response) => {
                      loading.hide();
                      if (response.data.status !== 0) {
                          self.closeOrderErrorMessage = response.data.message;
                          self.closeOrderError = true;
                      } else {
                          self.order.status = response.data.payload.status;
                          self.closeOrderError = false;
                          self.closeOrderModalOpened = false;
                          self.$notify({
                              title: 'Save order',
                              content: 'Order was saved.',
                              duration: 0,
                              type: 'success'
                          });
                      }
                  })
                  .catch((error) => {
                      console.error(error);
                      loading.hide();
                  });
            },
            cancelOrderModalOpen () {
                this.cancelOrderModalOpened = true;
            },
            cancelOrder () {
                if (this.order.cancellation !== 0) {
                    const self = this;
                    let loading = self.$loading.show();
                    this.apiRequest(Routing.generate('app_api_orders_cancel'))
                      .then((response) => {
                          loading.hide();
                          if (response.data.status !== 0) {
                              self.cancelOrderErrorMessage = response.data.message;
                              self.cancelOrderError = true;
                          } else {
                              self.order.status = response.data.payload.status;
                              self.cancelOrderError = false;
                              self.cancelOrderModalOpened = false;
                              self.$notify({
                                  title: 'Save order',
                                  content: 'Order was saved.',
                                  duration: 0,
                                  type: 'success'
                              });
                          }
                      })
                      .catch((error) => {
                          console.error(error);
                          loading.hide();
                      });
                }
            },
            onCustomerPickerShow () {
                this.$refs.customerDatatable.refresh();
            },
            save () {
                const self = this;
                let loading = self.$loading.show();
                this.apiRequest(Routing.generate('app_api_orders_save'))
                  .then((response) => {
                      loading.hide();
                      if (response.data.status !== 0) {
                          self.alerts.push({
                              type: 'danger',
                              key: new Date().getTime(),
                              content: response.data.message
                          });
                      } else {
                          if (self.order.id === null) {
                              window.location = Routing.generate('app_order_edit', { id: response.data.payload.id });
                          } else {
                              self.$notify({
                                  title: 'Save order',
                                  content: 'Order was saved.',
                                  duration: 0,
                                  type: 'success'
                              });
                          }
                      }
                  })
                  .catch((error) => {
                      loading.hide();
                      console.error(error);
                  })
            },
            apiRequest (url) {
                return axios.post(url, this.order);
            },
            onCustomerSaved (customer) {
                this.customer = customer;
            },
            newCustomer () {
                this.createCustomer.model = Vue.util.extend({}, window.rodaboats.customerModel);
                this.createCustomer.modalOpened = true;
            },
            suggestPrice () {
                const self = this;
                axios.get(Routing.generate('suggestprice'), {
                    params: {
                        start: this.order.start,
                        end: this.order.end,
                        date: this.order.date,
                        type: this.order.type,
                        id: this.order.boatId
                    }
                }).then((response) => {
                    if (response.data.success === true) {
                        self.order.rent = response.data.rent;
                        self.order.petrolCost = response.data.petrolCost;
                        self.order.deposit = response.data.deposit;
                    } else {
                        self.$notify({
                            title: 'Price suggestions',
                            content: 'Unable to suggest prices for the selected booking period. Please enter prices manually.',
                            duration: 0,
                            type: 'warning'
                        })
                    }
                }).catch((error) => {
                    console.error(error);
                })
            },
            checkConflicts () {
                if (this.order.boatId === null) {
                    return;
                }
                if (this.timeouts.checkConflict !== null) {
                    clearTimeout(this.timeouts.checkConflict);
                    this.timeouts.checkConflict = null;
                }
                const self = this;
                this.timeouts.checkConflict = setTimeout(() => {
                    let loading = self.$loading.show();
                    axios.post(Routing.generate('app_api_orders_check_conflicts'), self.order)
                        .then((response) => {
                            loading.hide();
                            self.validation.isConflicts = !response.data.valid;
                        })
                        .catch((error) => {
                            console.error(error);
                        })
                }, 500)
            },
            customer_dt_row_click (data) {
                this.customer = data.row;
                this.customerPicker = false;
            },
            locationBoats (location) {
                const result = [];
                this.boats.forEach(function(boat) {
                    if (boat['locationName'] === location) {
                        result.push(boat);
                    }
                });

                return result;
            },
            selectBoat (boat) {
                this.order.boatId = boat['id'];
            },
            selectCustomer (customer) {
                this.customerSuggestions = [];
                this.query = '';
                this.customer = customer;
            },
            clearCustomer () {
                this.customer = null;
            },
            customerFocused () {
                this.customerAutocomplete = (this.customer === null && this.query !== '' && this.customerSuggestions.length > 0);
            },
            decrementPeopleCount () {
                if (this.order['numberOfPeople'] > 0) {
                    this.order['numberOfPeople']--;
                }
            },
            incrementPeopleCount () {
                this.order['numberOfPeople']++;
            }
        },
        watch: {
            duration () {
                if (this.duration !== '') {
                    let start = moment.unix(this.order.start);
                    let h = start.hour();
                    let end = moment(this.order.end);
                    if (h + parseInt(this.duration) <= this.maxHour) {
                        end.hour(h + parseInt(this.duration));
                        this.order.end = end.unix();
                    }
                }
            },
            query () {
                if (this.query.length > 1) {
                    const self = this
                    axios.get(Routing.generate('app.customer.get.list', { query: this.query }))
                        .then((response) => {
                            let items = response.data.data;
                            if (items.length === 0) {
                                self.$notify({
                                    title: 'Customer search',
                                    content: 'No data was found!',
                                    type: 'warning'
                                });
                                self.query = '';
                            } else {
                                self.customerSuggestions = response.data.data;
                                self.customerAutocomplete = Boolean(self.customerSuggestions.length);
                            }
                        })
                        .catch((error) => {
                            console.error(error)
                        })
                } else {
                    this.customerSuggestions = [];
                    this.customerAutocomplete = false;
                }
            },
            customer () {
                this.order.customerId = (this.customer !== null) ? this.customer.id : null;
            },
            'order.boatId': function () {
                this.checkConflicts();
            },
            'order.date': function() {
                this.checkConflicts();
            },
            'order.start': function() {
                let start = moment.unix(this.order.start);
                start.utc();
                let end = moment.unix(this.order.end);
                end.utc();

                if (this.duration !== '') {
                    let h = parseInt(this.duration);
                    end.hour(start.hour() + h);
                    end.minute(start.minute());
                    this.order.end = end.unix();
                } else {
                    if (start > end) {
                        end.hour(start.hour() + 1);
                        end.minute(start.minute());
                        this.order.end = end.unix();
                    }
                }

                this.checkConflicts();
            },
            'order.end': function() {
                this.checkConflicts();
            }
        },
        computed: {
            order_log_link () {
                return (this.order.id !== null) ? Routing.generate('app.order.log', {id: this.order.id}) : '#';
            },
            allowEdit () {
                return this.order.status !== 4 && this.order.status !== 2;
            },
            cancellationReasons () {
                return window.rodaboats.referenceData.cancellationReasons;
            },
            cancellationReason () {
                let result = '';
                const self = this;
                this.cancellationReasons.forEach((reason) => {
                    if (reason.code === self.order.cancellation) {
                        result = reason.text;
                    }
                });

                return result;
            },
            commission_amount: {
                get () {
                    let r = (this.order['commission'] !== '') ? this.order['commission'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['commission'] = parseFloat(r);
                    } else {
                        this.order['commission'] = 0;
                    }
                }
            },
            kickback_amount: {
                get () {
                    let r = (this.order['kickback'] !== '') ? this.order['kickback'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['kickback'] = parseFloat(r);
                    } else {
                        this.order['kickback'] = 0;
                    }
                }
            },
            damage_amount: {
                get () {
                    let r = (this.order['damageAmount'] !== '') ? this.order['damageAmount'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['damageAmount'] = parseFloat(r);
                    } else {
                        this.order['damageAmount'] = 0;
                    }
                }
            },
            damage_payment_method () {
                let result = '';
                if (this.order.paymentMethodDamage !== '' && this.order.paymentMethodDamage !== null) {
                    const self = this;
                    this.paymentMethods.forEach(function(method) {
                        if (method['code'] === self.order.paymentMethodDamage) {
                            result = method['text'];
                        }
                    });
                }

                return result;
            },
            page_title () {
                return (this.order.id !== null) ? 'Edit order' : 'Add order';
            },
            statuses () {
                return window.rodaboats.referenceData.statuses;
            },
            status_text () {
                return this.statuses[this.order.status];
            },
            status_label_class () {
                return {
                    'label-success': (this.order.status === 1),
                    'label-danger': (this.order.status === 2),
                    'label-default': (this.order.status === 4)
                };
            },
            paymentMethods () {
                return window.rodaboats.referenceData.paymentMethods;
            },
            orderTypes () {
                return window.rodaboats.referenceData.orderTypes
            },
            hours () {
                return window.rodaboats.referenceData.hours;
            },
            minutes () {
                return window.rodaboats.referenceData.minutes;
            },
            booking_data_title () {
                return (this.validation.isConflicts) ? 'Booking data conflict with existing booking' : 'Booking data';
            },
            countries () {
                return window.rodaboats.referenceData.countries;
            },
            list_countries () {
                let entries = Object.entries(window.rodaboats.referenceData.countries);
                let result = [];
                entries.forEach((entry) => {
                    result.push({
                        value: entry[0],
                        text: entry[1]
                    })
                });
                return result;
            },
            languages () {
                return window.rodaboats.referenceData.languages;
            },
            list_languages () {
                let entries = Object.entries(window.rodaboats.referenceData.languages);
                let result = [];
                entries.forEach((entry) => {
                    result.push({
                        value: entry[0],
                        text: entry[1]
                    })
                });
                return result;
            },
            extras () {
                return window.rodaboats.referenceData.extras;
            },
            order_extras () {
                if (this.order.extra.length === 0) {
                    return '';
                }

                let result = [];
                const self = this;
                this.extras.forEach((extra) => {
                    if (self.order.extra.indexOf(extra['code']) !== -1) {
                        result.push(extra['abbr']);
                    }
                });

                return result.join(', ');
            },
            order_petrol_cost: {
                get () {
                    let r = (this.order['petrolCost'] !== '') ? this.order['petrolCost'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['petrolCost'] = parseFloat(r);
                    } else {
                        this.order['petrolCost'] = 0;
                    }
                }
            },
            order_deposit: {
                get () {
                    let r = (this.order['deposit'] !== '') ? this.order['deposit'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['deposit'] = parseFloat(r);
                    } else {
                        this.order['deposit'] = 0;
                    }
                }
            },
            order_rent_discount: {
                get () {
                    let r = (this.order['rentDiscount'] !== '') ? this.order['rentDiscount'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['rentDiscount'] = parseFloat(r);
                    } else {
                        this.order['rentDiscount'] = 0;
                    }
                }
            },
            order_rent: {
                get () {
                    let r = (this.order['rent'] !== '') ? this.order['rent'] : 0;
                    return r.toFixed(2);
                },
                set (r) {
                    if (r !== '') {
                        this.order['rent'] = parseFloat(r);
                    } else {
                        this.order['rent'] = 0;
                    }
                }
            },
            customerQuery: {
                get() {
                    return (this.customer !== null)
                        ? this.customer.lastName + ' ' + this.customer.firstName
                        : this.query;
                },
                set(query) {
                    this.query = query;
                }
            },
            customer_language () {
                return (this.customer !== null) ? this.languages[this.customer.language] : '';
            },
            customer_country () {
                return (this.customer !== null) ? this.countries[this.customer.country] : '';
            },
            customer_phoneNumber () {
                return (this.customer !== null) ? this.customer.phoneNumber : '';
            },
            customer_email () {
                return (this.customer !== null) ? this.customer.email : '';
            },
            locations () {
                const result = [];
                this.boats.forEach(function(boat) {
                    if (result.indexOf(boat['locationName']) === -1) {
                        result.push(boat['locationName']);
                    }
                });

                return result;
            },
            boats () {
                return window.rodaboats.referenceData.boats;
            },
            boatName () {
                return (this.selectedBoat !== null)
                    ? this.selectedBoat['locationName'] + ': ' + this.selectedBoat['name']
                    : '...';
            },
            selectedBoat () {
                let result = null;

                if (this.order.boatId !== null) {
                    const self = this;
                    this.boats.forEach((boat) => {
                        if (boat['id'] === self.order.boatId) {
                            result = boat;
                        }
                    });
                }

                return result;
            },
            maxHour () {
                let max = 0;
                this.hours.forEach((hour) => {
                    let h = parseInt(hour);
                    if (h > max) {
                        max = h;
                    }
                });

                return max;
            },
            startHour: {
                get () {
                    let dt = moment.unix(this.order.start);
                    dt.utc();
                    let h = dt.hour();
                    return (h > 9) ? h.toString() : '0' + h.toString();
                },
                set (hour) {
                    let h = parseInt(hour);
                    let dt = moment.unix(this.order.start);
                    dt.utc();
                    dt.hour(h);
                    this.order.start = dt.unix();
                }
            },
            startMinute: {
                get () {
                    let dt = moment.unix(this.order.start);
                    dt.utc();
                    let m = dt.minute();
                    return (m > 9) ? m.toString() : '0' + m.toString();
                },
                set (minute) {
                    let m = parseInt(minute);
                    let dt = moment.unix(this.order.start);
                    dt.utc();
                    dt.minute(m);
                    this.order.start = dt.unix();
                }
            },
            endHour: {
                get () {
                    let dt = moment.unix(this.order.end);
                    dt.utc();
                    let h = dt.hour();
                    return (h > 9) ? h.toString() : '0' + h.toString();
                },
                set (hour) {
                    let h = parseInt(hour);
                    let dt = moment.unix(this.order.end);
                    dt.utc();
                    dt.hour(h);
                    this.order.end = dt.unix();
                }
            },
            endMinute: {
                get () {
                    let dt = moment.unix(this.order.end);
                    dt.utc();
                    let m = dt.minute();
                    return (m > 9) ? m.toString() : '0' + m.toString();
                },
                set (minute) {
                    let m = parseInt(minute);
                    let dt = moment.unix(this.order.end);
                    dt.utc();
                    dt.minute(m);
                    this.order.end = dt.unix();
                }
            }
        }
    }
</script>

<style>
    .VueTables__table tr:hover {
        cursor: pointer;
    }

    .VueTables__table tr:hover td {
        background-color: #c1e2b3;
    }

    .VuePagination > nav:before, .VuePagination > nav:after {
        content: " ";
        display: table;
    }
    .VuePagination > nav:after {
        clear: both;
    }
    .dropdown-form .dropdown-menu {
        padding: 10px;
    }
    .open > .dropdown-menu {
        display: block;
    }
    .boat-dropdown {
        width: 100%;
    }
    .customer-autocomplete .dropdown-menu {
        margin-left: 15px;
    }
    .dropdown-menu > li > strong {
        display: block;
        padding: 3px 10px 0 10px;
        clear: both;
        line-height: 1.42857143;
        color: #333;
        white-space: nowrap;
    }
    .dropdown-menu > li > ul {
        list-style: none;
        font-size: 14px;
        text-align: left;
        padding: 0 0 5px 0;
        margin: 2px 0 0;
    }

    .dropdown-menu > li > ul > li > a {
        display: block;
        padding: 3px 20px;
        clear: both;
        font-weight: 400;
        line-height: 1.42857143;
        color: #333;
        white-space: nowrap;
    }
    .dropdown-menu > li > ul > li > a.selected {
        background-color: #cccccc;
    }
    .loading-overlay {
        z-index: 10000;
    }

    .dropdown-form {
        z-index: 9999;
    }
</style>
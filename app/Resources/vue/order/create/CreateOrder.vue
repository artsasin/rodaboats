<template>
    <div id="create-order-wrapper">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <fieldset>
                    <legend>Customer</legend>
                    <div class="row form-group">
                        <dropdown v-model="customerAutocomplete" :append-to-body="true" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">Customer name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" v-model="customerQuery" @focus="customerFocused()" :readonly="customer !== null" />
                                <div class="input-group-btn">
                                    <btn type="default" v-show="customer !== null" @click="clearCustomer()">
                                        <i class="fa fa-close"></i>
                                    </btn>
                                    <btn type="default" @click="customerPicker=true">
                                        <i class="fa fa-search"></i>
                                    </btn>
                                    <btn type="default">
                                        <i class="fa fa-plus"></i>
                                    </btn>
                                </div>
                            </div>
                            <template slot="dropdown">
                                <li v-for="suggest in customerSuggestions">
                                    <a role="button" @click="selectCustomer(suggest)">{{ suggest.lastName }}&nbsp;{{ suggest.firstName }}</a>
                                </li>
                            </template>
                        </dropdown>
                    </div>
                    <div class="row form-group">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="control-label">Language</label>
                            <input class="form-control" type="text" readonly="readonly" :value="customer_language">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="control-label">Country</label>
                            <input class="form-control" type="text" readonly="readonly">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="control-label">Phone number</label>
                            <input class="form-control" type="text" readonly="readonly">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="control-label">E-mail</label>
                            <input class="form-control" type="text" readonly="readonly">
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <fieldset>
                    <legend>Boat, date and time</legend>
                    <div class="row form-group">
                        <dropdown class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="control-label">Date</label>
                            <div class="input-group">
                                <input class="form-control" type="text" v-model="order.date" readonly="readonly">
                                <div class="input-group-btn">
                                    <btn class="dropdown-toggle"><i class="glyphicon glyphicon-calendar"></i></btn>
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
                                        <btn type="default" class="dropdown-toggle"><span class="caret"></span></btn>
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
                    <div class="row form-group">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">Start</label>
                            <table class="table table-condensed">
                                <tr>
                                    <td>
                                        <select class="form-control" v-model="startHour">
                                            <option v-for="hour in hours" :value="hour">{{ hour }}</option>
                                        </select>
                                    </td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-control" v-model="startMinute">
                                            <option v-for="minute in minutes" :value="minute">{{ minute }}</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">Duration</label>
                            <select class="form-control" v-model="duration">
                                <option value="">pick a duration</option>
                                <option value="1">1 hour</option>
                                <option value="2">2 hours</option>
                                <option value="4">4 hours</option>
                                <option value="8">8 hours</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <label class="control-label">End</label>
                            <table class="table table-condensed">
                                <tr>
                                    <td>
                                        <select class="form-control" v-model="endHour">
                                            <option v-for="hour in hours" :value="hour">{{ hour }}</option>
                                        </select>
                                    </td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-control" v-model="endMinute">
                                            <option v-for="minute in minutes" :value="minute">{{ minute }}</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></div>
        </div>
        <modal v-model="customerPicker" title="Customer search" size="lg">
            <p>This is a large modal.</p>
        </modal>
    </div>
</template>

<script>
    import Vue from 'vue'
    import axios from 'axios'

    export default {
        name: "create-order",
        data () {
            return {
                order: Vue.util.extend({}, window.orderModel),
                boats: window.boats,
                hours: window.bookingHours,
                minutes: window.bookingMinutes,
                duration: '',
                customerPicker: false,
                customer: null,
                query: '',
                customerAutocomplete: false,
                customerSuggestions: []
            }
        },
        methods: {
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
            }
        },
        watch: {
            duration () {
                if (this.duration !== '') {
                    let start = new Date(this.order.start);
                    let h = start.getHours();
                    let end = new Date(this.order.end);
                    if (h + parseInt(this.duration) <= this.maxHour) {
                        end.setHours(h + parseInt(this.duration));
                        this.order.end = end.toString();
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
            }
        },
        computed: {
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
                return (this.customer !== null) ? this.customer.language : '';
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
                    let dt = new Date(this.order.start);
                    let h = dt.getHours();
                    return (h > 9) ? h.toString() : '0' + h.toString();
                },
                set (hour) {
                    let h = parseInt(hour);
                    let dt = new Date(this.order.start);
                    dt.setHours(h);
                    this.order.start = dt.toString();
                }
            },
            startMinute: {
                get () {
                    let dt = new Date(this.order.start);
                    let m = dt.getMinutes();
                    return (m > 9) ? m.toString() : '0' + m.toString();
                },
                set (minute) {
                    let m = parseInt(minute);
                    let dt = new Date(this.order.start);
                    dt.setMinutes(m);
                    this.order.start = dt.toString();
                }
            },
            endHour: {
                get () {
                    let dt = new Date(this.order.end);
                    let h = dt.getHours();
                    return (h > 9) ? h.toString() : '0' + h.toString();
                },
                set (hour) {
                    let h = parseInt(hour);
                    let dt = new Date(this.order.end);
                    dt.setHours(h);
                    this.order.end = dt.toString();
                }
            },
            endMinute: {
                get () {
                    let dt = new Date(this.order.end);
                    let m = dt.getMinutes();
                    return (m > 9) ? m.toString() : '0' + m.toString();
                },
                set (minute) {
                    let m = parseInt(minute);
                    let dt = new Date(this.order.end);
                    dt.setMinutes(m);
                    this.order.end = dt.toString();
                }
            }
        }
    }
</script>

<style>
    .boat-dropdown {
        width: 100%;
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
</style>
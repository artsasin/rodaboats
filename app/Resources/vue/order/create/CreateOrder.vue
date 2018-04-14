<template>
    <div id="create-order-wrapper">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <fieldset>
                    <legend>Customer</legend>
                    <div class="row form-group">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">Customer name</label>
                            <div class="input-group">
                                <input class="form-control" type="text" readonly="readonly">
                                <div class="input-group-btn">
                                    <btn type="default">
                                        <i class="fa fa-search"></i>
                                    </btn>
                                    <btn type="default">
                                        <i class="fa fa-plus"></i>
                                    </btn>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label class="control-label">Language</label>
                            <input class="form-control" type="text" readonly="readonly">
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
                duration: ''
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
            }
        },
        computed: {
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
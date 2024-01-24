<template>
    <div>
        <!-- ContentController Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Communications</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Communications</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"> <strong>{{ message_count }} </strong> comms sent</h3>
                            </div>
                            <div class="card-body">
                                <form class="form" role="form"  @submit.prevent="createMessage">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <strong><b>Create a message</b></strong><br /><br />
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-lg-12">
                                        <label  class="groups">Recipients</label>
                                        <select name="roles" class="form-control" v-model="messageForm.role_id" required>
                                            <option v-for="role in roles" :key="role.id"   :id="role.name" :value="role.id"
                                                    >
                                               {{role.name}}
                                            </option>

                                        </select>
                                        <br />
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-lg-5">
                                        <label for="name" class="label">Name</label>
                                        <input required v-model="messageForm.name" type="text" name="name" placeholder="For your record"  class="form-control" id="name"/>
                                    </div>
                                    <div class="col-lg-5">
                                        <label for="headline" class="label">Headline</label>
                                        <input required v-model="messageForm.headline" type="text" name="headline" placeholder="Message headline"  class="form-control" id="headline" />
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="schedule" class="label">Schedule</label>
                                        <input v-model="messageForm.scheduled_at" type="datetime-local" name="schedule" id="schedule" class="form-control">
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-lg-10">
                                        <textarea required  v-model="messageForm.content" name="content" placeholder="Message Content" class="form-control" rows="9"></textarea>

                                    </div>
                                    <div class="col-lg-2">
                                        <b-form-group>
                                            <b-checkbox name="type" v-model="messageForm.type"
                                                        id="sms" value="SMS">
                                                <label>SMS</label>
                                            </b-checkbox>
                                        </b-form-group>
                                        <b-form-group>
                                            <b-checkbox name="type" v-model="messageForm.type"
                                                        id="push" value="PUSH">
                                                <label>Push Notification</label>
                                            </b-checkbox>
                                        </b-form-group>
                                        <b-form-group>
                                            <b-checkbox name="type" v-model="messageForm.type"
                                                        id="email" value="EMAIL">
                                                <label>Email</label>
                                            </b-checkbox>
                                        </b-form-group>
                                        <b-form-group>
                                            <b-checkbox name="whatapps" v-model="messageForm.type"
                                                        id="whatapps" value="WHATSAPP">
                                                <label>WhatApps</label>
                                            </b-checkbox>
                                        </b-form-group>
                                        <br />
                                        <button class="btn btn-success"  type="submit" > SEND / SCHEDULE</button>
                                        <br />

                                    </div>

                                </div>
                                </form>


                                <br />


                                <template class="row">
                                    <b-table
                                        show-empty
                                        striped
                                        hover
                                        :filter="filter"
                                        :fields="fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :busy="isBusy"
                                        @filtered="onFiltered"
                                        :items="scheduled_messages"
                                        @fetch="formatDate"

                                        >
                                        <div slot="table-busy" class="text-center text-danger my-2">
                                            <b-spinner class="align-middle"></b-spinner>
                                            <strong>Loading...</strong>
                                        </div>
                                        <template  v-slot:cell(actions)="row">
                                            <b-button  class="btn btn-primary">...</b-button>
                                        </template>

                                    </b-table>

                                    <br />

                                    <b-col md="12" class="mb-12">
                                        <b-pagination
                                            v-model="currentPage"
                                            :total-rows="totalRows"
                                            :per-page="perPage"
                                            class="my-0"
                                        ></b-pagination>
                                    </b-col>
                                </template>
                                <br />

                                <br />


                                <template class="row">
                                    <b-table
                                        show-empty
                                        striped
                                        hover
                                        :filter="filter"
                                        :fields="past_message_fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :busy="pastMessagesIsBusy"
                                        @filtered="onFiltered"
                                        :items="past_messages"
                                        @fetch="formatDateForPastMessages"

                                    >
                                        <div slot="table-busy" class="text-center text-danger my-2">
                                            <b-spinner class="align-middle"></b-spinner>
                                            <strong>Loading...</strong>
                                        </div>
                                        <template  v-slot:cell(actions)="row">
                                            <b-button  class="btn btn-primary">...</b-button>
                                        </template>

                                    </b-table>

                                    <br />

                                    <b-col md="12" class="mb-12">
                                        <b-pagination
                                            v-model="currentPage"
                                            :total-rows="totalPastRows"
                                            :per-page="perPage"
                                            class="my-0"
                                        ></b-pagination>
                                    </b-col>
                                </template>
                                <br />


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import moment from "moment";
    import toast from "./mixins/toast";

    export default {
        mixins: [toast],
        data() {
            return {
                message_count:0,
                roles: [],
                scheduled_messages:[],
                past_messages:[],
                filter: null,
                totalRows: 1,
                totalPastRows: 1,
                currentPage: 1,
                perPage: 10,
                pageOptions: [5, 10, 15, 20, 25, 50, 100],
                isBusy: false,
                pastMessagesIsBusy: false,
                messageForm : new Form( {
                    name: '',
                    role_id:'',
                    headline:'',
                    scheduled_at: null,
                    content:"",
                    type: [],
                }),
                fields :[
                    { key: 'content', label: 'Upcoming/Scheduled', sortable: true, sortDirection: 'asc' },
                    { key: 'scheduled_at', label: 'Date and Time', sortable: true, sortDirection: 'asc' },
                    { key: 'actions', label: 'Action' },
                    { key: 'recipients', label: 'Recipients', sortable: true, sortDirection: 'asc' },
                    { key: 'views', label: 'Views', sortable: true, sortDirection: 'asc' },
                ],
                past_message_fields :[
                    { key: 'content', label: 'Past events', sortable: true, sortDirection: 'asc' },
                    { key: 'created_at', label: 'Date and Time', sortable: true, sortDirection: 'asc' },
                    { key: 'actions', label: 'Action' },
                    { key: 'recipients', label: 'Recipients', sortable: true, sortDirection: 'asc' },
                    { key: 'views', label: 'Views', sortable: true, sortDirection: 'asc' },
                ]
            }
        },
        created() {
            this.getMessages();
            this.non_admin_roles();
            this.getScheduledMessages();
            this.getPastMessages();
        },
        methods: {
            getMessages(){
                axios.get('/messages').then(res => {
                    this.message_count = res.data.length;
                });
            },
            toggleBusy() {
                this.isBusy = !this.isBusy
            },
            togglePastMessageBusy() {
                this.pastMessagesIsBusy = !this.pastMessagesIsBusy
            },
            non_admin_roles(){
                axios.get('/none_admin').then(res => {
                    console.log(res.data);
                    this.roles = res.data;
                });
            },
            getScheduledMessages(){
                this.toggleBusy();
                axios.get('/messages/scheduled').then(res => {
                    this.toggleBusy();
                    console.log(res.data);
                    this.scheduled_messages = res.data;
                    this.totalRows = this.scheduled_messages.length;
                });

            },
            getPastMessages(){
                this.togglePastMessageBusy();
                axios.get('/messages/past').then(res => {
                    this.togglePastMessageBusy();
                    console.log(res.data);
                    this.past_messages = res.data;
                    this.totalPastRows = this.past_messages.length;
                });

            },
            onFiltered(filteredItems) {
                this.totalRows = filteredItems.length
                this.currentPage = 1
            },
            createMessage(){
                if(this.messageForm.type.length == 0){
                    this.toast.fire(
                        {
                            icon: 'error',
                            title: 'Please select  a least one message type'
                        }
                    );
                } else {
                    this.messageForm.submit('post', '/messages')
                        .then(result => {
                            const action = this.messageForm.scheduled_at == null ? 'Scheduled':'Sent';
                            this.messageForm.errors.clear();
                            this.toast.fire(
                                {
                                    icon: 'success',
                                    title: `Message ${action}`
                                }
                            ).finally(() => {
                                this.getMessages();
                                this.non_admin_roles();
                                this.getScheduledMessages();
                                this.getPastMessages();
                            });
                            console.log(result.data);
                        });
                }

            }

        },
        computed:{
            formatDate(){
                return this.scheduled_messages.map(scheduled_message =>{
                    scheduled_message.created_at = moment(String( scheduled_message.created_at)).format('YYYY-MM-DD hh:mm');
                    scheduled_message.updated_at = moment(String( scheduled_message.updated_at)).format('YYYY-MM-DD hh:mm');
                    scheduled_message.scheduled_at = moment(String( scheduled_message.scheduled_at)).format('YYYY-MM-DD hh:mm');
                });
            },
            formatDateForPastMessages(){
                return this.past_messages.map(past_message =>{
                    past_message.created_at = moment(String( past_message.created_at)).format('YYYY-MM-DD hh:mm');
                    past_message.updated_at = moment(String( past_message.updated_at)).format('YYYY-MM-DD hh:mm');
                    past_message.scheduled_at = moment(String( past_message.scheduled_at)).format('YYYY-MM-DD hh:mm');
                });
            }
        }
    }

</script>

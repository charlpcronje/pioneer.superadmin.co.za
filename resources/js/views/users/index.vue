<template>

    <div>
        <!-- ContentController Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Users</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Users</li>
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
                                <h3 class="card-title"> <strong> {{ users.length}} </strong> Users  in the system</h3>

                                <div class="card-tools">
                                    <div class="input-group input-group-sm">
                                        <input v-model="filter" type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                        <div class="input-group-append">
                                            <button type="submit" @click="onFiltered" class="btn btn-default"><i class="fas fa-search"></i></button>
                                            <b-button class="btn btn-success" @click="openModal($event.target, true)">ADD <i class="fa fa-plus-circle"></i> </b-button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0">
                                <br />
                                <b-container>
                                    <b-row>
                                        <b-col sm="3"></b-col>
                                        <b-col sm="3"></b-col>
                                        <b-col sm="3"></b-col>
                                        <b-col sm="3">
                                            <b-form-group
                                                label="Show"
                                                label-cols-sm="4"
                                                label-cols-md="4"
                                                label-cols-lg="4"
                                                label-align-sm="right"
                                                label-size="sm"
                                                label-for="perPageSelect"
                                                class="mb-0"
                                            >
                                                <b-form-select
                                                    v-model="perPage"
                                                    id="perPageSelect"
                                                    size="sm"
                                                    :options="pageOptions"
                                                ></b-form-select>
                                            </b-form-group>
                                        </b-col>

                                    </b-row>
                                </b-container>



                                <b-table
                                    show-empty
                                    striped
                                    hover
                                    :items="users"
                                    :filter="filter"
                                    :fields="fields"
                                    :current-page="currentPage"
                                    :per-page="perPage"
                                    :busy="isBusy"
                                    @filtered="onFiltered"
                                    @fetch="formatDate">
                                    <div slot="table-busy" class="text-center text-danger my-2">
                                        <b-spinner class="align-middle"></b-spinner>
                                        <strong>Loading...</strong>
                                    </div>
                                    <!--<template  v-slot:cell(name)="row">
                                       <b-col v-if="row.item.profile_pic != null">
                                           <img :src="row.item.profile_pic" />
                                       </b-col>
                                       <b-col><span v-html="row.item.name"></span></b-col>
                                    </template>-->
                                    <template  v-slot:cell(actions)="row">
                                        <b-button class="btn btn-default"  @click="editItem(row.item.id)"> <i class="fa fa-edit"></i> Edit </b-button>
                                        <b-button class="btn btn-default" @click="removeItem(row.item.id)"> <i class="fa fa-trash"></i> Delete </b-button>
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



                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>



                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /.content -->

        <!-- Info modal -->
        <b-modal :id="infoModal.id" title="Add User" ok-title="Save" @ok="saveInstance">
            <b-form class="form" role="form"  @keydown="modalForm.errors.clear()">
                <b-form-group>
                    <b-input-group>
                        <b-input type="text" name="name" placeholder="name" v-model="modalForm.name"></b-input>
                    </b-input-group>
                    <br v-if="onlyView" />
                    <b-input-group v-if="onlyView">
                        <b-input type="email" name="email" placeholder="Email address" v-model="modalForm.email"></b-input>
                    </b-input-group>
                    <br />
                    <b-input-group>
                        <b-select v-model="modalForm.role_id">

                            <b-select-option v-for="role in roles" :key="role.id"
                                             :id="role.id" :value="role.id"
                                             >
                                {{role.name}}
                            </b-select-option>
                        </b-select>
                    </b-input-group>
                    <br />
                    <b-input-group>
                        <b-textarea  name="notes" placeholder="Notes" v-model="modalForm.notes"
                                    ></b-textarea>
                    </b-input-group>
                    <br />
                    <b-input-group>
                        <b-checkbox name="send_password" v-model="modalForm.send_password">
                            <label>Send Password?</label>
                        </b-checkbox>
                    </b-input-group>
                </b-form-group>


            </b-form>

        </b-modal>

    </div>
</template>
<script>
    import moment from 'moment';
    export default {
        data() {
            return {
                users:[],
                roles:[],
                filter: null,
                totalRows: 1,
                currentPage: 1,
                perPage: 10,
                itemId:0,
                pageOptions: [5, 10, 15, 20, 25, 50, 100],
                isBusy: false,
                onlyView:false,
                infoModal: {
                    id: 'info-modal',
                    title: '',
                    content: '',
                    saveTitle:'Save',
                    onlyView: false
                },
                modalForm : new Form( {
                    name: '',
                    notes:'',
                    profile_pic: '',
                    send_password:'',
                    role_id:'1',
                    email: ''

                }),
                fields: [
                    { key: 'name', label: 'Name', sortable: true, sortDirection: 'asc' },
                    { key: 'role_name', label: 'Role', sortable: true, sortDirection: 'asc' },
                    //{ key: 'email', label: 'Email', sortable: true, class: 'text-center' },
                    { key: 'notes', label: 'Notes', sortable: true},
                   // { key: 'created_at', label: 'Created', sortable: true, class: 'text-center' },
                    //{ key: 'updated_at', label: 'Updated', sortable: true, class: 'text-center' },
                    { key: 'actions', label: 'Functions' }
                ]
            }
        },
        created() {
            this.loadData();
            this.fetchRoles();
            this.$on("modal-closed", this.closeModal);
        },
        computed: {
            formatDate(){
                return this.users.map(user =>{
                    user.created_at = moment(String( user.created_at)).format('YYYY-MM-DD hh:mm');
                    user.updated_at = moment(String( user.updated_at)).format('YYYY-MM-DD hh:mm');
                    user.email_verified_at = moment(String( user.updated_at)).format('YYYY-MM-DD hh:mm');

                });
            }
        },
        methods: {
            toggleBusy() {
                this.isBusy = !this.isBusy
            },
            openModal(button, viewOnly) {

                this.onlyView = viewOnly;
                this.$root.$emit('bv::show::modal', this.infoModal.id, button)
            },
            roleSelected(element){
                //alert(element.value);
               this.modalForm.role_id = element.value;
            },
            closeModal() {
                this. modalForm = new Form( {
                    name: '',
                    notes:'',
                    profile_pic: '',
                    send_password:'',
                    role_id:'',
                    email: ''

                });
                this.onlyView = false;
                this.$root.$emit("bv::hide::modal",  this.infoModal.id, null)
            },
            editItem(id) {
                //this.infoModal.onlyView =  false;
                this.modalForm.selectedPermissions = [];
                axios.get('/user/'+id).then(res => {
                        this.modalForm.name = res.data.name;
                        this.modalForm.role_id = res.data.role.id;
                        this.modalForm.email = res.data.email;
                        this.modalForm.notes  = res.data.notes;
                        this.itemId = id;
                        this.openModal(null, false);
                    });

            },
            viewItem(id) {
                //this.infoModal.onlyView =  true;
                this.modalForm.selectedPermissions = [];
                axios.get('/user/'+id).then(res => {
                    this.modalForm.name = res.data.name;
                    //this.selectedPermissions = res.data.permissions

                    this.openModal(null, true);
                });
            },
            loadData() {
                this.toggleBusy();
                axios.get('/user')
                    .then( res => {
                        // console.log(res.data);
                        this.toggleBusy();
                        this.users = res.data;
                        this.totalRows =  this.users.length;
                    });
            },
            removeItem(id){
                const Toast =  this.$swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000
                });
                this.$swal(
                    {
                        title: "Are you sure to delete the record?",
                        text: "You will not be able to recover this!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Yes, I am sure!',
                        cancelButtonText: "No, cancel it!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    }).then( res =>{
                    if(res.value){
                        axios.delete('/user/'+id).then(res => {
                            if(res.error){
                                // this.$swal('Deleted', 'You successfully deleted the permission', 'success');
                                Toast.fire(
                                    {
                                        icon: 'error',
                                        title: res.error
                                    }
                                );

                            } else {
                                Toast.fire(
                                    {
                                        icon: 'success',
                                        title: 'You successfully deleted the user type'
                                    }
                                ).finally(() =>  this.loadData());

                            }
                        });
                    }
                });
            },
            resetModal(){
                this.modalForm = new Form( {
                    name: '',
                    notes:'',
                    profile_pic: '',
                    send_password:'',
                    role:''

                });
                this.itemId = 0;
            },
            saveInstance(event){
                if(!this.infoModal.onlyView){
                    event.preventDefault();
                    if(this.modalForm.name === ''){
                        this.$swal('', 'Name is required', 'error');
                        // event.preventDefault();
                    } else {

                        if(this.itemId == 0 ){
                            this.modalForm.submit('post','/user')
                                .then(res => {
                                    console.log(res);
                                    const Toast =  this.$swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 1000
                                    });
                                    if(res.success) {
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'User saved successfully'
                                        }).finally(res => {
                                            this.closeModal();
                                            this.loadData();
                                        });
                                    } else {
                                        Toast.fire({
                                            icon: 'error',
                                            title: res.error
                                        })
                                    }

                                });
                        } else {
                            this.modalForm.submit('put', '/user/'+this.itemId)
                                .then(res => {
                                    console.log(res);


                                    const Toast =  this.$swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 1000
                                    });
                                    if(res.success) {
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'User saved successfully'
                                        }).finally(res => {
                                            this.closeModal();
                                            this.loadData();
                                        });
                                    } else {
                                        Toast.fire({
                                            icon: 'error',
                                            title: res.error
                                        })
                                    }
                                });
                        }

                    }
                }

            },
            onFiltered(filteredItems) {
                this.totalRows = filteredItems.length
                this.currentPage = 1
            },
            fetchRoles () {
                axios.get('/roles')
                .then( res => { this.roles = res.data; console.log("Roles"); console.log(this.roles); } );

            }
        },
        mounted: function () {

        }
    }

</script>


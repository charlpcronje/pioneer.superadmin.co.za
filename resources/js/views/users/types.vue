<template>

    <div>
        <!-- ContentController Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">  User types</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">User Types</li>
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
                                <h3 class="card-title"> <strong>{{ roles.length }} </strong> User types in the system </h3>

                                <div class="card-tools">
                                    <div class="input-group input-group-sm">
                                        <input v-model="filter" type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                        <div class="input-group-append">
                                            <button type="submit" @click="onFiltered" class="btn btn-default"><i class="fas fa-search"></i></button>
                                            <b-button class="btn btn-success" @click="openModal($event.target, false)">ADD <i class="fa fa-plus-circle"></i> </b-button>
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
                                                label-cols-sm="3"
                                                label-cols-md="3"
                                                label-cols-lg="3"
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
                                    :items="roles"
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
                                    <template  v-slot:cell(actions)="row">
                                        <b-button class="btn btn-default"  @click="editItem(row.item.id)"> <i class="fa fa-edit"></i> Permissions </b-button>
                                        <b-button class="btn btn-default" @click="viewItem(row.item.id)"> <i class="fa fa-eye"></i> View </b-button>
                                        <b-button class="btn btn-default"  @click="duplicateItem(row.item.id)"> <i class="fa fa-copy"></i> Duplicate </b-button>
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
        <b-modal :id="infoModal.id" title="Add User type" :ok-title="infoModal.onlyView ? 'OK' : 'Save'" @cancel="resetModal"   @ok="saveUserType">
            <b-form class="form" role="form"  @keydown="modalForm.errors.clear()">
                <b-form-group>
                    <b-input-group>
                        <b-input type="text" name="name" placeholder="User type" v-model="modalForm.name"
                                 :disabled="infoModal.onlyView"></b-input>
                    </b-input-group>
                </b-form-group>
                <label> Permissions </label>
                <b-form-group  v-for="permission in permissions" :key="permission.id">
                    <b-checkbox name="permissions"    :id="permission.name" :value="permission.id"
                                v-model="modalForm.selectedPermissions" :disabled="infoModal.onlyView">
                        <label :for="permission.name">{{permission.name}}</label>
                    </b-checkbox>
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
                roles: [],
                permissions:[],
                filter: null,
                totalRows: 1,
                currentPage: 1,
                perPage: 10,
                pageOptions: [5, 10, 15, 20, 25, 50, 100],
                isBusy: false,
                itemId:0,
                infoModal: {
                    id: 'info-modal',
                    title: '',
                    content: '',
                    saveTitle:'Save',
                    onlyView: false
                },
                modalForm : new Form( {
                    name: '',
                    selectedPermissions: []
                }),
                fields: [
                    { key: 'name', label: 'Name', sortable: true, sortDirection: 'asc' },
                    { key: 'count', label: 'Count', sortable: true, sortDirection: 'asc' },
                    //{ key: 'created_at', label: 'Created', sortable: true, class: 'text-center' },
                    //{ key: 'updated_at', label: 'Updated', sortable: true, class: 'text-center' },
                    { key: 'actions', label: 'Functions' },
                ]
            }
        },
        created() {
            this.loadData();
            this.$on("modal-closed", this.closeModal);
        },
        computed: {
            formatDate(){
                return this.roles.map(role =>{
                    role.created_at = moment(String( role.created_at)).format('YYYY-MM-DD hh:mm');
                    role.updated_at = moment(String( role.updated_at)).format('YYYY-MM-DD hh:mm');

                });
            }
        },
        mounted() {
            this.fetchPermissions();
        },
        methods: {
            toggleBusy() {
                this.isBusy = !this.isBusy
            },
            openModal(button, viewOnly) {
                this.infoModal.onlyView = viewOnly;
                this.$root.$emit('bv::show::modal', this.infoModal.id, button)
            },
            resetModal(){
              this.modalForm = new Form( {
                    name: '',
                    selectedPermissions: []
                })
              this.itemId = 0;
            },
            closeModal() {
                this.$root.$emit("bv::hide::modal",  this.infoModal.id, null)
            },
            editItem(id) {
                //this.infoModal.onlyView =  false;
                this.modalForm.selectedPermissions = [];
                axios.get('/roles/'+id).then(res => {
                        this.modalForm.name = res.data.name;
                        //this.selectedPermissions = res.data.permissions;
                        res.data.permissions.forEach(permission => {
                            this.modalForm.selectedPermissions.push(permission.id);
                        });
                        this.itemId = id;
                        this.openModal(null, false);
                    });

            },
            viewItem(id) {
                //this.infoModal.onlyView =  true;
                this.modalForm.selectedPermissions = [];
                axios.get('/roles/'+id).then(res => {
                    this.modalForm.name = res.data.name;
                    //this.selectedPermissions = res.data.permissions;
                    res.data.permissions.forEach(permission => {
                        this.modalForm.selectedPermissions.push(permission.id);
                    });

                    this.openModal(null, true);
                });
            },
            duplicateItem(id) {
                this.modalForm.selectedPermissions = [];
                axios.get('/roles/'+id).then(res => {
                    this.modalForm.name = res.data.name+"-1";
                    //this.selectedPermissions = res.data.permissions;
                    res.data.permissions.forEach(permission => {
                        this.modalForm.selectedPermissions.push(permission.id);
                    });
                    this.itemId = 0;
                    this.openModal(null, false);
                });
            },
            loadData() {
                this.toggleBusy();
                axios.get('/roles')
                    .then( res => {
                        // console.log(res.data);
                        this.toggleBusy();
                        this.roles = res.data;
                        this.totalRows =  this.roles.length;
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
                        axios.delete('/roles/'+id).then(res => {
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
                                )

                            }
                        });
                    }
                });
            },
            saveUserType(event){
                if(!this.infoModal.onlyView){
                    event.preventDefault();
                    if(this.modalForm.name === ''){
                        this.$swal('', 'Name is required', 'error');
                        // event.preventDefault();
                    } else if(this.modalForm.selectedPermissions.length == 0){
                        this.$swal('', 'Please select a least one permission', 'error');
                        // event.preventDefault();
                    } else {

                        if(this.itemId == 0 ){
                            this.modalForm.submit('post','/roles')
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
                                            title: 'User type saved successfully'
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
                            this.modalForm.submit('put', '/roles/'+this.itemId)
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
                                            title: 'User type saved successfully'
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
            fetchPermissions() {
                // var vm = this;
                axios.get('/permissions')
                    .then(res => {
                        this.permissions = res.data;
                    });
            }
        }
    }

</script>


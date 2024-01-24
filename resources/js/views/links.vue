<template>
    <div>
        <!-- ContentController Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">File Links Management</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">File Links Management</li>
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
                                <h3 class="card-title"> <strong>{{ count }} </strong> File Link(s) </h3>
                                <div class="card-tools">
                                    <div class="input-group input-group-sm">
                                        <input v-model="filter" type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                        <div class="input-group-append">
                                            <button type="submit" @click="onFiltered" class="btn btn-default"><i class="fas fa-search"></i></button>
                                            <b-button class="btn btn-success" @click="openModal()">ADD <i class="fa fa-plus-circle"></i> </b-button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <b-form-group>
                                    <b-form class="form" role="form"   id="link_form">
                                    </b-form>
                                </b-form-group>

                                <div class="table-responsive p-0">
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
                                    <br />


                                    <b-table
                                        show-empty
                                        striped
                                        hover
                                        :items="items"
                                        :filter="filter"
                                        :fields="fields"
                                        :current-page="currentPage"
                                        :per-page="perPage"
                                        :busy="isBusy"
                                        @filtered="onFiltered"
                                    >
                                        <div slot="table-busy" class="text-center text-danger my-2">
                                            <b-spinner class="align-middle"></b-spinner>
                                            <strong>Loading...</strong>
                                        </div>
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
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <b-modal :id="infoModal.id" title="Adding Content" ok-title="Save Media" @cancel="resetModal"   @ok="save_path($event)">
            <b-form class="form" role="form"  @keydown="form.errors.clear()">
                <label> Path </label>
                <b-form-group>
                    <b-select  v-model="form.path"  required>
                        <b-select-option value="0">--Please Select--</b-select-option>
                        <b-select-option v-for="folder in folders" :key="folder"
                                         :id="folder" :value="folder"> {{folder}}</b-select-option>
                    </b-select>
                </b-form-group>

                <label> Name </label>

                <b-form-group>
                    <b-input-group>
                        <b-input type="text"  placeholder="Name" v-model="form.name"  required></b-input>
                    </b-input-group>
                </b-form-group>
                <label> Link </label>
                <b-form-group>
                    <b-input-group>
                        <b-input type="text"  placeholder="Link" v-model="form.link"  required></b-input>
                    </b-input-group>
                </b-form-group>
            </b-form>
        </b-modal>
    </div>

</template>
<script>
import Toast from "./mixins/toast";
export default {
    name: "links",
    mixins:[Toast],
    created() {
        this.loadData();
        this.$on("modal-closed", this.closeModal);
    },
    data(){
        return {
            count:0,
            items: [],
            folders:[],
            filter: null,
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 50, 100],
            isBusy: false,
            itemId:0,
            form : new Form({
                name:'',
                link: '',
                path:''
            }),
            infoModal: {
                id: 'info-modal',
                title: ''
            },
            fields: [
                { key: 'path', label: 'Path ', sortable: true, sortDirection: 'asc' },
                { key: 'name', label: 'Name', sortable: true, sortDirection: 'asc' },
                { key: 'link', label: 'Link', sortable: false, sortDirection: 'asc' },
                { key: 'actions', label: 'Functions' },
            ]
        }
    },
    methods: {
        onFiltered(filteredItems) {
            this.totalRows = filteredItems.length
            this.currentPage = 1
        },
        save_path(event){
            event.preventDefault();
            if(this.form.path.length == 0){
                this.$swal('','The path is required', 'error');
            } else if(this.form.path.name == 0){
                this.$swal('','The name is required', 'error');
            } else if(this.form.path.link == 0){
                this.$swal('','The link is required', 'error');
            } else {
                if(this.itemId == 0 ) {
                    this.form.post('/fm-links')
                        .then(res => {
                            if (res.success) {
                                this.toast.fire({
                                    icon: 'success',
                                    title: 'Link saved successfully'
                                }).then(() => {
                                    this.loadData();
                                    this.closeModal();
                                });
                            } else {
                                this.toast.fire({
                                    icon: 'error',
                                    title: res.error
                                });
                            }
                        });
                } else {
                    this.form.put('/fm-links/'+this.itemId)
                        .then(res => {
                            if (res.success) {
                                this.toast.fire({
                                    icon: 'success',
                                    title: 'Link saved successfully'
                                }).then(() => {
                                    this.loadData();
                                    this.closeModal();
                                });
                            } else {
                                this.toast.fire({
                                    icon: 'error',
                                    title: res.error
                                });
                            }
                        });
                }
            }

        },
        editItem(id){
            axios.get('/fm-links/'+id)
            .then(res => {
                this.form.name = res.data.name;
                this.form.path = res.data.path;
                this.form.link = res.data.link;
                this.itemId = res.data.id;
                this.openModal();

            });

        },
        removeItem(id){
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
                    axios.delete('/fm-links/'+id).then(res => {
                        if(res.error){
                            this.toast.fire(
                                {
                                    icon: 'error',
                                    title: res.error
                                }
                            );

                        } else {
                            this.toast.fire(
                                {
                                    icon: 'success',
                                    title: 'You successfully deleted the user type'
                                }
                            ).then(res => {
                                this.loadData();
                            });

                        }
                    });
                }
            });

        },
        getFolders(){
            axios.get('/directory-list')
                .then( res => {
                    console.log(res.data);
                    this.folders = res.data;
                });

        },
        loadData(){
            this.toggleBusy();
            this.getFolders();
            axios.get('/fm-links')
                .then( res => {
                    // console.log(res.data);
                    this.toggleBusy();
                    this.items = res.data;
                    this.totalRows =  this.items.length;
                    this.count = this.items.length
                });

        },
        openModal(){
            this.$root.$emit('bv::show::modal', this.infoModal.id)
        },
        closeModal() {
            this.$root.$emit("bv::hide::modal",  this.infoModal.id, null)
        },
        resetModal(){
            form : new Form({
                name:'',
                link: '',
                path:''
            });
            this.closeModal();
        },
        toggleBusy(){
            this.isBusy = !this.isBusy
        }
    }

}
</script>

<style scoped>

</style>

<template>
    <div>
        <!-- ContentController Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Media Links Management</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Media Links Management</li>
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
                                <h3 class="card-title"> <strong>{{ count }} </strong> videos(s) links </h3>
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

                                <!--<b-form-group>
                                    <b-form class="form" role="form"   id="content_form" name="content_form">

                                        <b-form-group>
                                            <b-button class="btn btn-success float-right " @click="openModal()">ADD Media <i class="fa fa-plus-circle"></i> </b-button>
                                            <br class="clearfix"/>
                                            <br />
                                            <label> Medias </label>
                                            <b-select  v-model="medialLinkForm.media_id"  required>
                                                <b-select-option value="0">--Please Select--</b-select-option>
                                                <b-select-option v-for="media in medias" :key="media.id"
                                                                 :id="media.name" :value="media.id"> {{media.name}}</b-select-option>
                                            </b-select>
                                        </b-form-group>
                                        <label> Name </label>
                                        <b-input type="text" placeholder="Name" required  v-model="medialLinkForm.name"/>

                                        <label> Link </label>
                                        <b-input type="url" placeholder="Link" required v-model="medialLinkForm.link"/>

                                        <br class="clearfix"/>
                                        <br />


                                        <b-button class="btn btn-success float-right " @click="save_link">SAVE MEDIA LINK <i class="fa fa-plus-circle"></i> </b-button>
                                        <br class="clearfix"/>
                                        <br />



                                    </b-form>

                                </b-form-group>-->
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
                                        <!--<template  v-slot:cell(marketing_content)="row">
                                            <i class="fa fa-check text-success" v-if="row.item.marketing_content == 1"></i>
                                            <i class="fa fa-close text-danger" v-if="row.item.marketing_content == 0"></i>
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <b-modal :id="infoModal.id" title="Adding Content" ok-title="Save Media" @cancel="resetModal"   @ok="save_link($event)">
            <b-form class="form" role="form"  @keydown="medialLinkForm.errors.clear()">
                <label> Name </label>
                <b-form-group>
                    <b-input-group>
                        <b-input type="text"  placeholder="Name" v-model="medialLinkForm.name"  required></b-input>
                    </b-input-group>
                </b-form-group>
                <label> Link </label>
                <b-form-group>
                    <b-input-group>
                        <b-input type="text"  placeholder="Link" v-model="medialLinkForm.link"  required></b-input>
                    </b-input-group>
                </b-form-group>
                <label> Media </label>
                <select name="roles" class="form-control" v-model="medialLinkForm.media_id" required>
                    <option v-for="media in medias" :key="media.id"   :id="media.name" :value="media.id"
                    >
                        {{media.name}}
                    </option>

                </select>
                <br />
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import Toast from './mixins/toast';

export default {
    name: "media",
    data() {
        return {
            count:0,
            items: [],
            medias:[],
            filter: null,
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 50, 100],
            isBusy: false,
            itemId:0,
            medialLinkForm: new Form({
                media_id : 0,
                name:'',
                link:'',
              //  marketing_content: false
            }),
            mediaForm : new Form({
               name:''
            }),
            infoModal: {
                id: 'info-modal',
                title: ''
            },
            fields: [
                { key: 'name', label: 'Name', sortable: true, sortDirection: 'asc' },
                { key: 'link', label: 'Link', sortable: false, sortDirection: 'asc' },
                { key: 'media_id', label: 'Media ', sortable: true, sortDirection: 'asc' },
              //  { key: 'marketing_content', label: 'Marketing Content?', sortable: true, sortDirection: 'asc' },
                { key: 'actions', label: 'Functions' },
            ]
        }
    },
    mixins:[Toast],
    methods:{
        onFiltered(filteredItems){

        },
        scrollToTop(){
            window.scrollTo(0,0);
        },
        save_link(event) {
            event.preventDefault();
            if (this.medialLinkForm.media_id == 0) {
                this.$swal('', 'Please select media', 'error');
            } else if (this.medialLinkForm.name.length === 0) {
                this.$swal('', 'Name is required', 'error');
            } else if (this.medialLinkForm.link.length === 0) {
                this.$swal('', 'Link is required', 'error');
            } else {
                if (this.itemId === 0) {
                    this.medialLinkForm.post('media-links')
                        .then(res => {
                            if (res.success) {
                                this.toast.fire({
                                    icon: 'success',
                                    title: 'Media link saved successfully'
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
                    this.medialLinkForm.put('media-links/'+this.itemId)
                    .then(res => {
                        if (res.success) {
                            this.toast.fire({
                                icon: 'success',
                                title: 'Media link saved successfully'
                            }).then(() => {
                                this.loadData();
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
        toggleBusy(){
            this.isBusy = !this.isBusy
        },
        fetchMedias(){
            axios.get('medias')
                .then( res => {
                    // console.log(res.data);
                    this.medias = res.data;


                });

        },
        loadData(){
            this.toggleBusy();
            this.fetchMedias();

            axios.get('/media-links')
                .then( res => {
                    // console.log(res.data);
                    this.toggleBusy();
                    this.items = res.data;
                    this.totalRows =  this.items.length;
                    this.count = this.items.length
                });

        },
        closeModal(){
            this.$root.$emit("bv::hide::modal",  this.infoModal.id, null);
        },
        editItem(id){
            axios.get('/media-links/'+id)
            .then(res =>{
                console.log(res.data);
                this.medialLinkForm.media_id = res.data.media_id;
                this.medialLinkForm.name = res.data.name;
                this.medialLinkForm.link = res.data.link;
                this.itemId =  res.data.id;
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
                    axios.delete('/media-links/'+id).then(res => {
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
        openModal(){
            this.resetModal();
            this.$root.$emit('bv::show::modal', this.infoModal.id)
        },

        resetModal(){
            this.mediaFormLink = new Form( {
                media_id : 0,
                name:'',
                link:'',
            })
            //this.itemId = 0;
        },
    },
    created() {
        this.loadData();
        this.$on("modal-closed", this.closeModal);
    },
}
</script>

<style scoped>

</style>

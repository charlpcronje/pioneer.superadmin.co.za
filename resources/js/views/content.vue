<template>
    <div>
        <!-- ContentController Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Content Management</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Content Management</li>
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
                                <h3 class="card-title"> <strong>{{ count }} </strong> content(s) created </h3>
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
                                    <template v-slot:cell(featured_image)="row">
                                        <img :src="row.item.featured_image" :alt="row.item.title" width="120"/>
                                    </template>
                                    <template v-slot:cell(intro_text)="row">
                                        <p v-html="row.item.intro_text" />
                                    </template>
                                    <template v-slot:cell(full_text)="row">
                                        <p v-html="row.item.full_text" />
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

                    </div>
                </div>
            </div>
        </div>
        <b-modal :id="infoModal.id" title="Adding Content" ok-title="Save" @cancel="resetModal"   @ok="save($event)">
            <b-form class="form" role="form"  @keydown="contentForm.errors.clear()" enctype="multipart/form-data"
                    id="content_form">
                <label> Title </label>
                <b-form-group>
                    <b-input-group>
                        <b-input type="text"  placeholder="Title" v-model="contentForm.title"  required></b-input>
                    </b-input-group>
                </b-form-group>
                <label> Categories </label>
                <b-form-group>
                    <b-select  v-model="contentForm.categories_id"  required>
                        <b-select-option value="0">--Please Select--</b-select-option>
                        <b-select-option v-for="category in categories" :key="category.id"
                                         :id="category.name" :value="category.id"> {{category.name}}</b-select-option>
                    </b-select>
                </b-form-group>
                <label> Featured image </label>
                <b-form-group>
                    <b-input-group>
                        <b-form-file
                            accept="image/*"
                            v-model="contentForm.featured_image"
                            :state="null"
                            placeholder="Featured Image"
                            drop-placeholder="Drop file here..."
                            required
                        ></b-form-file>
                    </b-input-group>
                </b-form-group>
                <label> Intro Text </label>
                <b-form-group>
                    <b-input-group>
                        <ckeditor  name="intro_text" v-model="contentForm.intro_text"
                                   :editor="editor" :config="editorConfig"></ckeditor>
                    </b-input-group>
                </b-form-group>
                <label> Full Text </label>
                <b-form-group>
                    <b-input-group>
                        <ckeditor name="full_text"  v-model="contentForm.full_text" :editor="editor" :config="editorConfig"></ckeditor>
                    </b-input-group>
                </b-form-group>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Toast from './mixins/toast';

export default {
    name: "app_content",

    data(){
        return {
            count:0,
            items: [],
            categories:[],
            filter: null,
            totalRows: 1,
            currentPage: 1,
            perPage: 10,
            pageOptions: [5, 10, 15, 20, 25, 50, 100],
            isBusy: false,
            itemId:0,
            infoModal: {
                id: 'info-modal',
                title: ''
            },
            editor: ClassicEditor,
            editorConfig: {
                // The configuration of the editor.
            },
            contentForm: new Form({
                    title:'',
                    categories_id: 0,
                    full_text:'',
                    intro_text:'',
                    featured_image : new File([],'')
                },
            ),
            fields: [
                { key: 'title', label: 'Name', sortable: true, sortDirection: 'asc' },
                { key: 'featured_image', label: 'Featured Image', sortable: false, sortDirection: 'asc' },
                { key: 'intro_text', label: 'Intro ', sortable: true, sortDirection: 'asc' },
                { key: 'full_text', label: 'Full Text ', sortable: true, sortDirection: 'asc' },
                { key: 'categories_id', label: 'Category', sortable: true, sortDirection: 'asc' },
                //{ key: 'count', label: 'Count', sortable: true, sortDirection: 'asc' },
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
    mixins :[Toast],
    methods: {
        loadData(){
            this.toggleBusy();
            axios.get('/content')
                .then( res => {
                    // console.log(res.data);
                    this.toggleBusy();
                    this.items = res.data;
                    this.totalRows =  this.items.length;
                    this.count = this.items.length
                });

        },
        fetchCategories(){
            axios.get('/categories')
                .then( res => {
                    // console.log(res.data);
                    this.categories = res.data;
                });
        },
        onFiltered(filteredItems) {
            this.totalRows = filteredItems.length
            this.currentPage = 1
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
                    axios.delete('/content/'+id).then(res => {
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

        editItem(id){
            axios.get('/content/'+id).then(res => {
                this.contentForm.title = res.data.title;
                this.contentForm.categories_id = res.data.categories_id;
                this.contentForm.intro_text = res.data.intro_text;
                this.contentForm.full_text = res.data.full_text;
                this.itemId = id;
                this.openModal();
            });

        },
        openModal(){
            this.$root.$emit('bv::show::modal', this.infoModal.id)
        },
        closeModal() {
            this.$root.$emit("bv::hide::modal",  this.infoModal.id, null)
        },
        resetModal(){
            this.contentForm = new Form( {
                title:'',
                categories_id: 0,
                full_text:'',
                intro_text:'',
                featured_image: new File([],'')
            })
            this.itemId = 0;
        },
        save(event){
            event.preventDefault();
            if(this.contentForm.title.length === 0) {
                this.$swal('', 'Title is required', 'error');
            } else if (this.contentForm.categories_id == 0) {
                this.$swal('', 'Please select a category', 'error');
            } if(this.contentForm.intro_text.length === 0) {
                this.$swal('', 'Intro text is required', 'error');
            } if(this.contentForm.full_text.length === 0) {
                this.$swal('', 'Full text is required', 'error');
            } else {
                let fd = new FormData();

                fd.append('featured_image', this.contentForm.featured_image);
                fd.append('title', this.contentForm.title);
                fd.append('full_text', this.contentForm.full_text);
                fd.append('intro_text', this.contentForm.intro_text);
                fd.append('categories_id', this.contentForm.categories_id);


                if(this.itemId === 0) {
                    axios.post('/content', fd).then(d => {
                        if(d.data.success){
                            this.toast.fire({
                                icon: 'success',
                                title: 'ContentController saved successfully'
                            }).finally(res => {
                                this.closeModal();
                                this.resetModal();
                                this.loadData();
                            });
                        } else {
                            this.toast.fire({
                                icon: 'error',
                                title: d.data.error
                            });
                        }
                    });
                } else {
                    let fd = new FormData();

                    fd.append('featured_image', this.contentForm.featured_image);
                    fd.append('title', this.contentForm.title);
                    fd.append('full_text', this.contentForm.full_text);
                    fd.append('intro_text', this.contentForm.intro_text);
                    fd.append('categories_id', this.contentForm.categories_id);
                    fd.append('id', this.itemId);
                    axios.post('/content', fd)
                    .then(res => {
                        if(res.data.success){
                            this.toast.fire({
                                icon: 'success',
                                title: 'ContentController saved successfully'
                            }).finally(() => {
                                this.closeModal();
                                this.resetModal();
                                this.loadData();
                            });
                        } else {
                            this.toast.fire({
                                icon: 'error',
                                title: res.data.error
                            });
                        }

                    });
                }


            }




        },
        toggleBusy(){
            this.isBusy = !this.isBusy
        }
    },
    mounted() {
        this.fetchCategories();
    },
}
</script>

<style scoped>

</style>

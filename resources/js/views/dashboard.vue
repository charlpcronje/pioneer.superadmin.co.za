<template>
<div>
    <!-- ContentController Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">



        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner" v-if="!loading_files">
                            <h3>{{files}}</h3>
                            <p>Files in {{folders}} Folders</p>
                        </div>
                        <div  class="inner text-center text-primary my-2" v-if="loading_files">
                            <b-spinner class="align-middle"></b-spinner><br />
                            <strong>Loading...</strong>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner" v-if="!loading_users">
                            <h3>{{users}}</h3>

                            <p>Users</p>
                        </div>
                        <div  class="inner text-center text-warning my-2"  v-if="loading_users">
                            <b-spinner class="align-middle"></b-spinner><br />
                            <strong>Loading...</strong>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner" v-if="!loading_messages">
                            <h3>{{comms}}</h3>

                            <p>Commnication sents</p>
                        </div>
                        <div  class="inner text-center text-white my-2"  v-if="loading_messages">
                            <b-spinner class="align-middle"></b-spinner><br />
                            <strong>Loading...</strong>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner" v-if="!loading_medias">
                            <h3>{{medias}}</h3>
                            <p>Media links</p>

                        </div>
                        <div  class="inner text-center text-primary my-2" v-if="loading_medias">
                            <b-spinner class="align-middle"></b-spinner> <br />
                            <strong>Loading...</strong>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Latest Medias Saved</h5>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                        <i class="fas fa-wrench"></i>
                                    </button>
                                    <!--<div class="dropdown-menu dropdown-menu-right" role="menu">
                                        <a href="#" class="dropdown-item">Action</a>
                                        <a href="#" class="dropdown-item">Another action</a>
                                        <a href="#" class="dropdown-item">Something else here</a>
                                        <a class="dropdown-divider"></a>
                                        <a href="#" class="dropdown-item">Separated link</a>
                                    </div>-->
                                </div>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="latest_link in latest_links">

                                        <td><a :href="latest_link.link">{{latest_link.name}}</a></td>
                                        <td>{{latest_link.media_name}} </td>
                                        <td>{{latest_link.created_at}} </td>
                                    </tr>
                                    </tbody>
                                </table>


                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- ./card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Latest Users Login</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool"  v-on:click="downloadUserInfos">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>User</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="login in logins">
                                        <td>{{login.login_date}} </td>
                                        <td>{{login.name}} </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Top Downloads</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool" v-on:click="topDownloads">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Filename</th>
                                        <th>Count</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="download in downloads">
                                        <td>{{download.filename}} </td>
                                        <td>{{download.total}} </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">File log Access</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool" v-on:click="fileAccessDownload">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>File name </th>
                                        <th>Access type </th>
                                        <th>Date </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="access in file_access">
                                        <td>{{access.user_id.name}} </td>
                                        <td>{{access.filename}} </td>
                                        <td>{{access.access_type.toLowerCase()}} </td>
                                        <td>{{access.created_at}} </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Page Access Log </h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool" v-on:click="pageAccessDownload">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Page Link</th>
                                        <th> Count </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="page in page_access">
                                        <td>{{page.page_link}} </td>
                                        <td>{{page.total}} </td>

                                    </tr>

                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>


            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>

</template>

<script>
export  default  {
    name: "dashboard",
    data() {
        return {
            files: 0,
            comms:0,
            users:0,
            medias:0,
            folders:0,
            latest_links: [],
            loading_files: true,
            loading_users: true,
            loading_medias:true,
            loading_messages:true,
            logins:[],
            file_access:[],
            downloads:[],
            page_access:[],

        }

    },
    created() {
        this.getFilesCount();
        this.getUsersCount();
        this.getMessages();
        this.getMedias();
        this.getLatest();
        this.getfileAccess();
        this.getUserLogins();
        this.getDownloadCount();
        this.getPageAccess();
    },
    methods: {
       getFilesCount() {
           axios.get('file-manager/count')
           .then(res => {
               this.loading_files = false;
               this.files = res.data.files;
               this.folders = res.data.folders;

           });
       },
        downloadUserInfos(){
           window.location.href = 'api/v1/export/user_logins';
        },
        topDownloads(){
            window.location.href = 'api/v1/export/top_downloads';
        },
        fileAccessDownload(){
            window.location.href = 'api/v1/export/file_access';
        },
        pageAccessDownload(){
            window.location.href = 'api/v1/export/page_access';
        },
        getfileAccess(){
        axios.get('api/v1/file_access_log')
            .then(res => {
                this.file_access = res.data;
            });
        },
        getUserLogins(){
           axios.get('api/v1/user_logins')
            .then(res => {
                this.logins = res.data;
            });

        },
        getDownloadCount(){
            axios.get('api/v1/file_access_log/downloads')
                .then(res => {
                    this.downloads = res.data;
                });
        },
        getPageAccess(){
            axios.get('api/v1/page_access_log')
                .then(res => {
                    this.page_access = res.data;

                });
        },
       getUsersCount(){
           axios.get('user')
               .then(res => {
                   this.loading_users = false;
                   this.users = res.data.length;

               });
       },
        getMessages(){
            axios.get('messages').then(res => {
                this.loading_messages  = false;
                this.comms = res.data.length;

            });
        },
        getMedias(){
            axios.get('medias')
                .then( res => {
                    // console.log(res.data);
                    this.loading_medias  = false;
                    this.medias = res.data.length;
                });

        },
        getLatest(){
           axios.get('links/latest')
            .then( res => {
                this.latest_links = res.data;
                console.log(this.latest_links );
            });
        }
    }
}

</script>

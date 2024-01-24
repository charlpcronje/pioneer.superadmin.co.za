
<template>
    <!-- /.login-logo -->



    <div class="card">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="error">
            {{error}}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>



        <div class="card-body login-card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

            <form  method="post"  @submit.prevent="onSubmit" @keydown="form.errors.clear()">
                <div class="input-group mb-3">
                    <input type="email" required class="form-control" placeholder="Email" name="email"  v-model="form.email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <span class="invalid-feedback d-block" role="alert" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></span>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="/login">Login</a>
            </p>

        </div>
        <!-- /.login-card-body -->
    </div>
    <!-- /.login-box -->



</template>

<script>

    export default {
        data() {
            return {
                error:'',
                user: null,
                form: new Form({
                    'email': '',
                })
            }

        },


        created() {
           // axios.get('/users')
             //   .then(({data}) => this.users = data);
        },

        methods: {
            onSubmit(){
                this.form.post('/verify-email')
                .then(result => {
                    if(result.error){
                        this.error = result.error;
                    } else {

                        this.$router.push({name:'recovery_password', params:{user:result}})
                    }
                })
            }
        }
    }

</script>

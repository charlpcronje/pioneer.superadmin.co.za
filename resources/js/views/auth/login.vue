<template>
    <!-- /.login-logo -->
    <div class="card">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="error">
            {{error}}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>

        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form  method="post"  @submit.prevent="onSubmit" @keydown="form.errors.clear()">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" required name="email" v-model="form.email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password"
                    name="password" required v-model="form.password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember" v-model="form.remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>



            <p class="mb-1">
                <a href="/forgot-password">I forgot my password</a>
            </p>

        </div>
        <!-- /.login-card-body -->
    </div>


</template>

<script>

    export default {
        data() {
            return {
                error:'',
                form: new Form({
                    'remember': '',
                    'email': '',
                    'password': ''
                })
            }

        },


        created() {

        },

        methods: {
            onSubmit(){
              this.form.post('/auth').then(res => {
                  if(res.error){
                      this.error = res.error;
                  } else{
                      location.reload();
                  }
              });
            }
        }
    }

</script>

<template>

    <div class="card">
        <div :class="messageClass"  class="alert alert-dismissible fade show" role="alert" v-if="error">
            {{error}}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>

        <div class="card-body login-card-body">
            <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>

            <form  method="post"  @submit.prevent="onSubmit" @keydown="form.errors.clear()">
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password"  name="password" v-model="form.password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Confirm Password" name="conf_password" v-model="form.conf_password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="user_id" v-model="form.user_id" />
                        <button type="submit" class="btn btn-primary btn-block">Change password</button>
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



</template>

<script>

    export default {

        data() {
            return {
                error: '',
                messageClass:'alert-danger',
                form: new Form({
                    'password': '',
                    'conf_password': '',
                    'user_id': this.$route.params.user.id
                })
            }

        },
        created() {


        },

        methods: {
            onSubmit(){
                if(this.form.conf_password != this.form.password){
                    this.error = 'Password Mismatch';
                } else {
                    this.form
                    .post('/reset-password')
                    .then(res => {
                        if(res.error){
                            this.error = res.error;
                        } else {
                            this.messageClass = 'alert-success';
                            this.error = 'Password changed successfully'
                        }
                        setTimeout(() =>{
                            this.$router.push('login');
                        }, 3000);


                    });
                }

            }
        }
    }

</script>

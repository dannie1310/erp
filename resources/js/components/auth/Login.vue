<template>
    <div class="login row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Login</h3>
                </div>
                <div class="card-body">
                    <form @submit.prevent="authenticate">
                        <div class="form-group row" v-if="authError">
                            <p class="error">
                                {{authError}}
                            </p>
                        </div>
                        <div class="form-group row">
                            <label for="usuario">Usuario</label>
                            <input type="text" class="form-control" v-model="formLogin.usuario" placeholder="Nombre de Usuario">
                        </div>
                        <div class="form-group row">
                            <label for="clave">Clave</label>
                            <input type="password" class="form-control" v-model="formLogin.clave" placeholder="Contraseña">
                        </div>
                        <div class="form-group row">
                            <input type="submit" value="Login" class="btn btn-outline-primary ml-auto">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                formLogin: {
                    usuario: '',
                    clave: ''
                },
                error: null
            }
        },
        methods:{
            authenticate(){
                this.$store.dispatch('login');
                return new Promise((res, rej) => {
                    axios.post('/api/auth/login', this.$data.formLogin)
                        .then(response => {
                            res(response.data);
                        })
                        .catch(err => {
                            rej('Wrong Email/Password combination.')
                        });
                })
                    .then(res => {

                        this.$session.start();
                        this.$session.set('jwt', res.access_token);
                        this.$session.set('user', res.user);

                        this.$store.commit("loginSuccess", res);
                        this.$router.push({path: '/dashboard'});
                    })
                    .catch(error => {
                        this.$store.commit("loginFailed", {error});
                    })
            }
        },
        computed:{
            authError(){
                return this.$store.getters.authError
            }
        }
    }
</script>

<style scoped>
    .error{
        text-align: center;
        color: red;
    }
</style>
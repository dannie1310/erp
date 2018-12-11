<template>
    <div class="login-box">

        <div class="login-logo">
            <img src="../../../img/logo_hc.png" class="img-responsive img-rounded" width="100%">
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Iniciar Sesión</p>

                <form @submit.prevent="authenticate">
                    <div class="input-group mb-3">
                        <input type="text" name="usuario" v-validate="'required'" class="form-control" placeholder="Usuario" v-model="formLogin.usuario">
                        <div class="input-group-append">
                            <span class="fa fa-user input-group-text"></span>
                        </div>
                    </div>
                    <span>{{ errors.first('usuario') }}</span>
                    <div class="input-group mb-3">
                        <input type="password" name="clave" v-validate="'required'" class="form-control" placeholder="Clave" v-model="formLogin.clave">
                        <div class="input-group-append">
                            <span class="fa fa-lock input-group-text"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

    import { mapGetters, mapState } from 'vuex'

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
                this.$store.dispatch('auth/login');
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

                        this.$store.commit("auth/loginSuccess", res);
                        this.$router.push({name: 'obras'});
                    })
                    .catch(error => {
                        this.$store.commit("auth/loginFailed", {error});
                    })
            }
        },
        computed:{
            authError(){
                return this.$store.getters['auth/authError'];
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
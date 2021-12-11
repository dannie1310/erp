<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3" >
                        <div class="info-box">
                            <span :class="'info-box-icon bg-danger elevation-1'"><i class="fa fa-users-slash" title="Empresas Boletinadas" ></i></span>

                            <div class="info-box-content">
                                <span class="info-box-number">VER EMPRESAS BOLETINADAS</span>
                                <router-link :to="{name: 'empresas-boletinadas'}" >
                                    <span class="info-box-text">Ingresar <i class="fa fa-arrow-circle-o-right" /></span>
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3" v-for="(aplicacion, i) in aplicaciones">
                        <div class="info-box">
                            <span :class="aplicacion.color" class="info-box-icon elevation-1" ><i :class="aplicacion.icon"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-number">{{aplicacion.menu}}</span>

                                <a :href="`${aplicacion.ruta}?origen=${url}`" :target="aplicacion.target">
                                    <span class="info-box-text">IR <i class="fa fa-arrow-circle-o-right"></i> </span>
                                </a>
                                <a :href="`${aplicacion.manual}`" v-if="aplicacion.manual" target="_blank">
                                    <span class="info-box-text" align="right" title="Ver manual de usuario"><i class="fa fa-file-pdf-o" /></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    import TwoFactorAuthModal from "../seguridad/twofactorauth/Modal";
    export default {
        name: "portal",
        data() {
            return {
                cargando : false
            }
        },
        components: {TwoFactorAuthModal},
        mounted() {
            this.$Progress.start()
            this.index()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            index() {
                this.cargando = true;
                return new Promise((resolve, reject) => {
                    this.$store.commit('igh/aplicacion/SET_APLICACIONES', []);

                    return this.$store.dispatch('igh/aplicacion/index', {
                        params: {
                            scope: 'PorUsuario:' + this.currentUser.idusuario,
                            sort: 'menu',
                            order: 'ASC'
                        }
                    })
                        .then(data => {
                            this.$store.commit('igh/aplicacion/SET_APLICACIONES', data);
                            resolve();
                        })
                        .catch(error => {
                            reject(error);
                        })
                        .finally(() => {
                            this.cargando = false;
                        })
                })
            }
        },

        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            aplicaciones() {
                return this.$store.getters['igh/aplicacion/aplicaciones']
            },
            url() {
                return process.env.MIX_APP_URL;
            }
        }
    }
</script>

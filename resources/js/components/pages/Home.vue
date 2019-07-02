<template>
    <span>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3" v-for="(sistema, i) in sistemas">
                <div class="info-box">
                    <span :class="'info-box-icon '+sistema.color+' elevation-1'"><i :class="sistema.icon"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-number">{{sistema.name.toUpperCase()}}</span>
                        <router-link :to="{name:sistema.url}" v-if="!sistema.externo">
                            <span class="info-box-text">Ingresar <i class="fa fa-arrow-circle-o-right"></i> </span>
                        </router-link>

                        <a :href="`${sistema.url}?origen=${url}`" target="_blank" v-else>
                            <span class="info-box-text">Ingresar <i class="fa fa-arrow-circle-o-right"></i> </span>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "home",
        data() {
            return {
                loading: false
            }
        },
        mounted() {
            this.index();
        },
        methods: {
            index() {
                this.$store.commit('seguridad/sistema/SET_SISTEMAS', []);
                this.$session.remove('sistemas');

                return this.$store.dispatch('seguridad/sistema/index', {
                    params: { scope: 'porUsuario'}
                })
                    .then(data => {
                        this.$store.commit('seguridad/sistema/SET_SISTEMAS', data);
                        this.$session.set('sistemas', data);
                    })
            }
        },

        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            sistemas() {
                return this.$store.getters['seguridad/sistema/sistemas']
            },
            url() {
                return process.env.MIX_APP_URL;
            }
        }
    }
</script>
<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3" v-for="(aplicacion, i) in aplicaciones">
                        <div class="info-box">
                            <span :class="`${aplicacion.color}`" class="info-box-icon elevation-1" ><i :class="`${aplicacion.icon}`"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-number">{{aplicacion.menu}}</span>

                                <a :href="`${aplicacion.ruta}?origen=${url}`" target="_blank">
                                    <span class="info-box-text">IR <i class="fa fa-arrow-circle-o-right"></i> </span>
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
        components: {TwoFactorAuthModal},
        mounted() {
            var self = this;

            var left = (screen.width/2)-(361/2);
            var top = (screen.height/2)-(167/2);
            PromiseWindow.open('/google-2fa', {
                width: 500      ,
                height: 500,
                window: {
                    scrollbars: 'no',
                    toolbar: 'no',
                    resizable: 'no',
                    top: top,
                    left: left
                },
                windowName: 'Verificación de dos pasos Google Auth'
            }).then(
                // Success
                function(data) {
                    alert('success')
                    self.index();
                    // data.result == 'awesome' (1)
                },

                // Error
                function(error) {
                    switch(error) {
                        case 'closed':
                            alert('close')
                            // window has been closed
                            break;
                        case 'my-custom-message':
                            // 'my-custom-message' postMessage has been sent from target URL (2)
                            break;
                    }
                }
            );
        },
        methods: {
            index() {
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
                    });
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
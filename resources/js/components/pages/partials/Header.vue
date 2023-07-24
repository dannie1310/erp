<template>
    <span>
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav" v-if="$route.name != 'portal'">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
                <li v-if="currentObra" class="nav-item d-none d-sm-inline-block">
                    <router-link :to="{name: 'obras'}" class="nav-link" title="Cambiar Obra">{{ currentObra.nombre }} {{currentObra && currentObra.datos_contables && currentObra.datos_contables.BDContPaq !=='null' ? '('+currentObra.datos_contables.BDContPaq+')' :''}}</router-link>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul v-if="currentUser" class="navbar-nav ml-auto">
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">
                        {{ `${currentUser.nombre}  ${currentUser.apaterno}  ${currentUser.amaterno}` }}
                    </a>
                </li>
                <li class="nav-item" v-if="$route.name == 'portal'">
                    <a href="#" class="nav-link" @click="$refs.modal2fa.init()" data-slide="true" title="Verificación de 2 Pasos">
                        <img style="max-height: 14px" src="../../../../img/phome-check.png">
                    </a>
                </li>
                <li class="nav-item">
                    <router-link :to="{name: 'portal'}" class="nav-link" @click="logout" data-slide="true" href="#" title="Ir al Portal">
                        <i class="fa fa-th-large"></i>
                    </router-link>
                </li>
                <li class="nav-item">
                    <a class="nav-link" @click="logout" data-slide="true" href="#" title="Cerrar Sesión">
                         <i class="fa fa-sign-out"></i>
                    </a>
                </li>
                <li class="nav-item" v-if="$route.name != 'portal'">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->
        <two-factor-auth-modal ref="modal2fa"></two-factor-auth-modal>
    </span>

</template>

<script>
    import TwoFactorAuthModal from "../../seguridad/twofactorauth/Modal";
    export default {
        name: 'app-header',
        components: {TwoFactorAuthModal},
        methods:{
            logout(){
                swal({
                    icon: 'warning',
                    title: 'Cerrar Sesión',
                    text: 'La sesión actual se cerrará, ¿Desea continuar?',
                    buttons: ['Cancelar', 'Si, Cerrar Sesión'],
                    dangerMode: true,
                })
                    .then((success) => {
                        if (success) {
                            this.$store.dispatch('auth/logout')
                                .then(() => {
                                    axios.post('/logout')
                                        .then(() => {
                                            this.$session.destroy();
                                            window.location.replace('/login');
                                        })

                                })
                                .catch(error => {
                                    axios.post('/ logout')
                                        .then(() => {
                                            this.$session.destroy();
                                            window.location.replace('/login');
                                        })
                                })
                        }
                    });
            }
        },
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            currentObra() {
                return this.$store.getters['auth/currentObra']
            }
        }
    }
</script>

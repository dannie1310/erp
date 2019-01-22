<template>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
            </li>
            <li v-if="currentObra" class="nav-item d-none d-sm-inline-block">
                <router-link :to="{name: 'obras'}" class="nav-link" title="Cambiar Obra">{{ currentObra.nombre }}</router-link>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul v-if="currentUser" class="navbar-nav ml-auto">
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">
                    {{ `${currentUser.nombre}  ${currentUser.apaterno}  ${currentUser.amaterno}` }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" @click="logout" data-slide="true" href="#" title="Cerrar Sesión">
                     <i class="fa fa-sign-out"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->
</template>

<script>
    export default {
        name: 'app-header',
        methods:{
            logout(){
                Swal({
                    'type': 'warning',
                    'title': 'Cerrar Sesión',
                    'text': 'La sesión actual se cerrará, ¿Desea continuar?',
                    showCancelButton: true,
                    confirmButtonText: 'Si, Continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.$store.commit('auth/logout');
                        this.$session.destroy();
                        this.$router.push({name: 'login'});
                    }
                })
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
<template>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fa fa-gear"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number">CONTABILIDAD</span>
                    <router-link :to="{name: 'contabilidad'}">
                        <span class="info-box-text">Ingresar <i class="fa fa-arrow-circle-o-right"></i> </span>
                    </router-link>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
</template>

<script>
    export default {
        name: "home",
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },

            sistemas() {
                return this.$store.getters['']
            }
        }
    }
</script>
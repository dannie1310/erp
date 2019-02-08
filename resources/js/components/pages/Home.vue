<template>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fa fa-calculator"></i></span>

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
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fa fa-usd"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number">TESORERÍA</span>
                    <router-link :to="{name: 'tesoreria'}">
                        <span class="info-box-text">Ingresar <i class="fa fa-arrow-circle-o-right"></i> </span>
                    </router-link>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fa fa-drivers-license"></i></span>

                <div class="info-box-content">
                    <span class="info-box-number">SUBCONTRATOS</span>
                    <router-link :to="{name: 'subcontratos'}">
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
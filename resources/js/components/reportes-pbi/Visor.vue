<template>
    <span v-if="reporte">
         <div class="row">
            <div class="col-md-6">
                <h5>{{reporte.nombre}}</h5>
            </div>
             <div class="col-md-6">
                <button type="button" class="btn btn-secondary pull-right"  @click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
            </div>
        </div>
         <div class="row">
            <div class="col-md-12">
                <iframe width="1200" height="768" :src="reporte.url" frameborder="0" allowFullScreen="true"></iframe>
            </div>
        </div>
    </span>
</template>
<script>
    export default {
        name: "Visor",
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        mounted() {
            this.find()
        },
        methods:{
            find() {
                this.cargando = true;
                this.$store.commit('reportes/reporte/SET_REPORTE', null);
                return this.$store.dispatch('reportes/reporte/find', {
                    id: this.id,
                    params: {

                    }
                }).then(data => {
                    this.$store.commit('reportes/reporte/SET_REPORTE', data);
                }) .finally(() => {
                    this.cargando = false;
                })
            },
            regresar() {
                this.$router.push({name: 'reportes-pbi'});
            },
        },
        computed: {
            reporte() {
                return this.$store.getters['reportes/reporte/currentReporte']
            }
        }
    }
</script>

<style scoped>

</style>
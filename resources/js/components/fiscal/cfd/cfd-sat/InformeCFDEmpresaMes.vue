<template>
    <span>informe</span>
</template>

<script>
    export default {
        name: "InformeCFDEmpresaTiempo",
        data() {
            return {
                informe : [],
                fechas : [],
                cargando : false
            }
        },
        mounted() {
            this.getInforme();
        },
        methods :{
            getInforme() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/cfd-sat/obtenerInformeCFDEmpresaMes', {

                })
                    .then(data => {
                        this.informe = data.informe;
                        this.fechas = data.fechas;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            }
        }
    }
</script>

<style scoped>

</style>
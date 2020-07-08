<template>
    <span>
        <div class="row" v-if="cargando">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                   <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <span v-else-if="informe && cargando== false">
            <div class="row" >
                <div class="col-md-12">
                    <table class="table">
                        <tbody>
                            <tr style="background-color: #757575; color:#FFF; text-align:center" >
                                <td class="index_corto">#</td>
                                <td >Empresa</td>
                                <template v-for="(anio_mes,i) in informe.anios_meses">
                                    <td>{{anio_mes}}</td>
                                </template>

                            </tr>
                            <template v-for="(empresa,j) in informe.empresas">
                               <tr >
                                   <td class="index_corto">{{empresa.k}}</td>
                                   <td>{{empresa.valor}}</td>
                                   <template v-for="(anio_mes,i) in informe.anios_meses">
                                       <td style="text-align: right" v-if="informe.valores[j][i] != null">{{informe.valores[j][i]}}</td>
                                       <td style="text-align: right" v-else>-</td>
                                   </template>
                               </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </span>
    </span>
</template>

<script>
    export default {
        name: "InformeCFDEmpresaTiempo",
        data() {
            return {
                informe : [],
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
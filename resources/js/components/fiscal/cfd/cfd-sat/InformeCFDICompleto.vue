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
                    <table class="table table-bordered" style="font-size: 10px">
                        <tbody>
                            <template v-for="(empresa,j) in informe.empresas">
                               <tr style="background-color: #888888">
                                   <td class="index_corto">{{empresa.k}}</td>
                                   <td colspan="24">{{empresa.valor}}</td>
                               </tr>
                                <tr>
                                    <td class="index_corto"></td>
                                    <template v-for="(mes,i) in informe.meses">
                                        <td colspan="2" style="text-align: center" >{{mes.mes_txt}}</td>
                                   </template>
                                </tr>
                                <tr v-for="(anio,k) in informe.anios_empresa[j]">
                                    <td>
                                        {{anio}}
                                    </td>
                                    <template v-for="(mes,l) in informe.meses">

                                        <td v-if="informe.valores[j][anio][mes.id]" style="text-align: right" >{{informe.valores[j][anio][mes.id]["cantidad"]}}</td>
                                        <td v-else style="text-align: right" >-</td>
                                        <td v-if="informe.valores[j][anio][mes.id]" style="text-align: right" >{{informe.valores[j][anio][mes.id]["total"]}}</td>
                                        <td v-else style="text-align: right" >-</td>
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
        name: "InformeCFDCompleto",
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
                return this.$store.dispatch('fiscal/cfd-sat/obtenerInformeCFDICompleto', {

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
font{
    size: 12px;
}

</style>

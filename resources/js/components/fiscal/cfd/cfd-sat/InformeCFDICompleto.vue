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

                <div class="offset-md-6 col-md-6">
                    <ImpresionInforme ></ImpresionInforme>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                            <template v-for="(empresa,j) in informe.empresas">
                                <tr >
                                   <td colspan="27"></td>
                               </tr>
                               <tr style="background-color: #aaaaaa">
                                   <td class="index_corto">{{empresa.k}}</td>
                                   <td colspan="26">{{empresa.valor}}</td>
                               </tr>
                                <tr>
                                    <td class="index_corto"></td>
                                    <template v-for="(mes,i) in informe.meses">
                                        <td colspan="2" style="text-align: center" >{{mes.mes_txt}}</td>
                                   </template>
                                    <td colspan="2" style="text-align: center" >Total</td>
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
                                    <td style="text-align: right">
                                        {{informe.valores[j][anio]["totales"]["cantidad_f"]}}
                                    </td>
                                    <td style="text-align: right">
                                        {{informe.valores[j][anio]["totales"]["total_f"]}}
                                    </td>
                                </tr>

                                <tr style="background-color: #aaaaaa" v-if="informe.anios_empresa[j].length>1">
                                    <td>
                                    </td>
                                    <template v-for="(mes,l) in informe.meses">
                                        <td v-if="informe.valores[j]['totales'][mes.id]" style="text-align: right" >{{informe.valores[j]["totales"][mes.id]["cantidad_f"]}}</td>
                                        <td v-else style="text-align: right" >-</td>
                                        <td v-if="informe.valores[j]['totales'][mes.id]" style="text-align: right" >{{informe.valores[j]["totales"][mes.id]["total_f"]}}</td>
                                        <td v-else style="text-align: right" >-</td>
                                   </template>
                                    <td style="text-align: right">
                                        {{informe.valores[j]["totales"]["cantidad_f"]}}
                                    </td>
                                    <td style="text-align: right">
                                        {{informe.valores[j]["totales"]["total_f"]}}
                                    </td>
                                </tr>

                            </template>
                            <tr >
                               <td colspan="27"></td>
                           </tr>
                            <tr style="background-color: #000; color:#FFFFFF" >
                                <td>
                                </td>
                                <template v-for="(mes,l) in informe.meses">
                                    <td v-if="informe.valores['totales'][mes.id]" style="text-align: right" >{{informe.valores["totales"][mes.id]["cantidad_f"]}}</td>
                                    <td v-else style="text-align: right" >-</td>
                                    <td v-if="informe.valores['totales'][mes.id]" style="text-align: right" >{{informe.valores["totales"][mes.id]["total_f"]}}</td>
                                    <td v-else style="text-align: right" >-</td>
                               </template>
                                <td style="text-align: right">
                                    {{informe.valores["totales"]["cantidad_f"]}}
                                </td>
                                <td style="text-align: right">
                                    {{informe.valores["totales"]["total_f"]}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </span>
    </span>
</template>

<script>
    import ImpresionInforme from './../partials/ImpresionInforme';
    export default {
        name: "InformeCFDCompleto",
        components:{ImpresionInforme},
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


.table th, .table td {
    padding: 0.25rem;
    font-size: 10px;
}

</style>

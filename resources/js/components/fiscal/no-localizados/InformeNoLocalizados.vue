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
                <div class="col-md-6">
                    <div v-if="fechas">{{fechas.lista_no_localizados}}</div>
                    <div v-if="fechas">{{fechas.cfd_recibidos}}</div>
                </div>
                <div class="col-md-6">
                    <div class="pull-right">
                        <ImpresionInforme v-if="cantidad_partidas > 0" ></ImpresionInforme>
                        <ImpresionInformeTranspuesto v-if="cantidad_partidas > 0"></ImpresionInformeTranspuesto>
                    </div>
                </div>
            </div>
            <br>
            <div v-if="cantidad_partidas == 0">
                 <table class="table">
                     <tbody>
                         <tr>
                             <td style="text-align: center">
                                 <h5>No existen contribuyentes no localizados por el SAT relacionados con las empresas del grupo.</h5>
                             </td>
                         </tr>
                     </tbody>
                 </table>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <table class="table">
                        <tbody>

                            <template v-for="(tipo,i) in informe">
                                <template v-for="(partidas, j) in tipo">
                                     <template v-if="partidas.tipo == 'titulo'">
                                         <tr>
                                            <td colspan="13" style="background-color: #fff" ></td>
                                        </tr>

                                        <tr style="background-color: #757575; color:#FFF; text-align:center" >
                                            <td class="index_corto">#</td>
                                            <td>RFC</td>
                                            <td>Razón Social</td>
                                            <td>Fecha Primera Publicación</td>
                                            <td>Entidad Federativa</td>
                                            <td>Empresa</td>
                                            <td>Inicio de Operaciones </td>
                                            <td>Fin de Operaciones </td>
                                            <td># CFDI</td>
                                            <td>Importe incluyendo IVA</td>
                                        </tr>
                                    </template>
                                    <tr v-else-if="partidas.tipo == 'partida'" >
                                        <td class="index_corto">{{partidas.indice}}</td>
                                        <td>{{partidas.rfc}}</td>
                                        <td>{{partidas.razon_social}}</td>
                                        <td style="text-align: center">{{partidas.fecha_primera_publicacion}}</td>
                                        <td>{{partidas.entidad_federativa}}</td>
                                        <td>{{partidas.empresa}}</td>
                                        <td style="text-align: center">{{partidas.inicio_operaciones}}</td>
                                        <td style="text-align: center">{{partidas.fin_operaciones}}</td>
                                        <td style="text-align:right">{{partidas.no_CFDI}}</td>
                                        <td style="text-align:right">{{partidas.importe_format}}</td>
                                    </tr>

                                    <tr v-else :style="{'background-color': partidas.bg_color_hex, 'color': partidas.color_hex}"  >
                                        <td class="index_corto">{{partidas.contador}}</td>
                                        <td></td>
                                        <td colspan="6">{{partidas.etiqueta}}</td>

                                        <td style="text-align:right">{{partidas.contador_cfdi}}</td>
                                        <td style="text-align:right">{{partidas.importe_format}}</td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </span>

    </span>
</template>

<script>
    import ImpresionInforme from './partials/ImpresionInforme';
    import ImpresionInformeTranspuesto from "./partials/ImpresionInformeTranspuesto";
    export default {
        name: "InformeNoLocalizados",
        components:{ImpresionInformeTranspuesto, ImpresionInforme},
        data() {
            return {
                informe : [],
                fechas : [],
                cargando : false,
                cantidad_partidas : 0,
            }
        },
        mounted() {
            this.getInforme();
        },
        methods :{
            getInforme() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/ctg-no-localizado/obtenerInforme', {

                })
                    .then(data => {
                        this.informe = data.informe;
                        this.fechas = data.fechas;
                        this.cantidad_partidas = data.informe[0].length;
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

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
                    <div v-if="fechas">{{fechas.lista_efos}}</div>
                    <div v-if="fechas">{{fechas.cfd_recibidos}}</div>
                </div>
                <div class="col-md-6">
                    <ImpresionInforme ></ImpresionInforme>
                </div>
            </div>
            <br>
            <div class="row" >
                <div class="col-md-12">
                    <table class="table">
                        <tbody>

                            <template v-for="(tipo,i) in informe">
                                <template v-for="(partidas, j) in tipo">
                                     <template v-if="partidas.tipo == 'titulo'">
                                         <tr>
                                            <td colspan="12" style="background-color: #fff" ></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" :style="{'background-color': partidas.bg_color_hex, 'color': partidas.color_hex}" >
                                                {{partidas.etiqueta}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #757575; color:#FFF; text-align:center" >
                                            <td class="index_corto" rowspan="2">#</td>
                                            <td rowspan="2">Estatus</td>
                                            <td rowspan="2">RFC</td>
                                            <td rowspan="2">Razón Social</td>
                                            <td rowspan="2">Fecha Presunto</td>
                                            <td rowspan="2">Fecha Presunto DOF</td>
                                            <td rowspan="2">Fecha Definitivo</td>
                                            <td rowspan="2">Fecha Definitivo DOF</td>
                                            <td rowspan="2">Fecha Corrección</td>
                                            <td rowspan="2">Empresa</td>
                                            <td colspan="2">Antes</td>
                                            <td colspan="2">2016</td>
                                            <td colspan="2">2017</td>
                                            <td colspan="2">2018</td>
                                            <td colspan="2">2019</td>
                                            <td colspan="2">2020</td>
                                            <td colspan="2">Total</td>
                                        </tr>
                                         <tr style="background-color: #757575; color:#FFF; text-align:center">
                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>

                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>

                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>

                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>

                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>

                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>

                                             <td># CFD</td>
                                            <td>Importe incluyendo IVA</td>


                                         </tr>
                                    </template>
                                    <tr v-else-if="partidas.tipo == 'partida'" >
                                        <td class="index_corto">{{partidas.indice}}</td>
                                        <td>{{partidas.estatus}}</td>
                                        <td>{{partidas.rfc}}</td>
                                        <td>{{partidas.razon_social}}</td>
                                        <td>{{partidas.fecha_presunto}}</td>
                                        <td>{{partidas.fecha_presunto_dof}}</td>
                                        <td>{{partidas.fecha_definitivo}}</td>
                                        <td>{{partidas.fecha_definitivo_dof}}</td>
                                        <td>{{partidas.fecha_autocorreccion}}</td>
                                        <td>{{partidas.empresa}}</td>
                                        <td style="text-align:right">{{partidas.no_CFDI}}</td>
                                        <td style="text-align:right">{{partidas.importe_format}}</td>
                                    </tr>

                                    <tr v-else :style="{'background-color': partidas.bg_color_hex, 'color': partidas.color_hex}"  >
                                        <td class="index_corto">{{partidas.contador}}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{partidas.etiqueta}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
    export default {
        name: "InformeEFOSCFD",
        components:{ImpresionInforme},
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
                return this.$store.dispatch('seguridad/finanzas/ctg-efos/obtenerInformeCFD', {

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

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
                                    </template>
                                    <template v-else-if="partidas.tipo == 'subtitulo'">
                                        <tr>
                                            <td colspan="12" style="background-color: #fff" ></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" :style="{'background-color': partidas.bg_color_hex, 'color': partidas.color_hex}" >
                                                {{partidas.etiqueta}}
                                            </td>
                                        </tr>
                                    </template>
                                    <template v-else-if="partidas.tipo == 'subtitulo_final'">
                                        <tr>
                                            <td colspan="12" :style="{'background-color': partidas.bg_color_hex, 'color': partidas.color_hex}" >
                                                {{partidas.etiqueta}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #D5D5D5; color:#000; text-align:center" >
                                            <td class="index_corto">#</td>
                                            <td>Estatus</td>
                                            <td>RFC</td>
                                            <td>Razón Social</td>
                                            <td>Fecha Presunto</td>
                                            <td>Fecha Presunto DOF</td>
                                            <td>Fecha Definitivo</td>
                                            <td>Fecha Definitivo DOF</td>
                                            <td>Fecha Corrección</td>
                                            <td>Empresa</td>
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
                                        <td colspan="4">{{partidas.etiqueta}}</td>
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
    import ImpresionInforme from './partials/ImpresionInformeDesglosado';
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
                return this.$store.dispatch('seguridad/finanzas/ctg-efos/obtenerInformeCFDDesglosado', {

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

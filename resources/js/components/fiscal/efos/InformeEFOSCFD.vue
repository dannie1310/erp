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
                    <ImpresionInformeDefinitivos></ImpresionInformeDefinitivos>
                    <button @click="descargarInforme" class="btn btn-primary pull-right" title="Descargar Informe">
                        <i class="fa fa-download"></i> Descargar
                    </button>
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
                                            <td colspan="14" style="background-color: #fff" ></td>
                                        </tr>
                                        <tr>
                                            <td colspan="14" :style="{'background-color': partidas.bg_color_hex, 'color': partidas.color_hex}" >
                                                {{partidas.etiqueta}}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #757575; color:#FFF; text-align:center" >
                                            <td class="index_corto">#</td>
                                            <td>Estatus</td>
                                            <td>RFC</td>
                                            <td>Razón Social</td>
                                            <td>Fecha Presunto</td>
                                            <td>Fecha Presunto DOF</td>
                                            <td>Fecha Definitivo</td>
                                            <td>Fecha Definitivo DOF</td>
                                            <td>Fecha Límte Aclaración SAT</td>
                                            <td>Fecha Límte Aclaración DOF</td>
                                            <td>Fecha Corrección</td>
                                            <td>Empresa</td>
                                            <td># CFDI</td>
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
                                        <td>{{partidas.fecha_limite_sat}}</td>
                                        <td>{{partidas.fecha_limite_dof}}</td>
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
    import ImpresionInformeDefinitivos from "./partials/ImpresionInformeDefinitivos";
    export default {
        name: "InformeEFOSCFD",
        components:{ImpresionInformeDefinitivos, ImpresionInforme},
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
            descargarInforme(){
                this.descargando = true;
                return this.$store.dispatch('seguridad/finanzas/ctg-efos/descargarInformeCFDIDesglosado',
                {

                })
                .then(data => {
                    this.$emit('success');
                }).finally(() => {
                    this.descargando = false;
                });
            },
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

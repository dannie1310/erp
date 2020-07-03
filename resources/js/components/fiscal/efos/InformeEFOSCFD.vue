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
                    <ImpresionInforme ></ImpresionInforme>
                </div>
            </div>
            <div class="row" >
                <div class="col-md-12">
                    <table class="table">
                        <tbody>
                            <tr style="background-color: #757575; color:#FFF; text-align:center" >
                                <td class="index_corto">#</td>
                                <td>Estatus</td>
                                <td>RFC</td>
                                <td>Razón Social</td>
                                <td>Fecha Presunto</td>
                                <td>Fecha Definitivo</td>
                                <td>Empresa</td>
                                <td># CFD</td>
                                <td>Importe</td>
                            </tr>
                            <template v-for="(tipo,i) in informe">
                                <template v-for="(partidas, j) in tipo">
                                    <tr v-if="partidas.tipo == 'partida'" >
                                        <td class="index_corto">{{partidas.indice}}</td>
                                        <td>{{partidas.estatus}}</td>
                                        <td>{{partidas.rfc}}</td>
                                        <td>{{partidas.razon_social}}</td>
                                        <td>{{partidas.fecha_presunto}}</td>
                                        <td>{{partidas.fecha_definitivo}}</td>
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
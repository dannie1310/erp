<template>
    <span>
        <div class="row" v-if="informe">
            <div class="col-md-12">
                <table class="table">
                    <tbody>
                    <template v-for="(informe_empresa,i) in informe">
                        <tr style="background-color: #000; color: #FFF">
                            <td colspan="11">{{informe_empresa.empresa}}</td>
                        </tr>
                        <template v-for="(informe_tipo, j) in informe_empresa.informe">
                            <tr style="background-color: #555555; color: #FFF">
                                <td colspan="11">{{informe_tipo.tipo}} ({{informe_tipo.cantidad}})</td>
                            </tr>
                            <tr style="background-color: #999999" >
                                <td class="index_corto">#</td>
                                <td>Base de Datos Revisada</td>
                                <td>Base de Datos Referencia</td>
                                <td>Ejercicio</td>
                                <td>Periodo</td>
                                <td>Tipo Póliza</td>
                                <td>Folio</td>
                                <td>No. Movto.</td>
                                <td>Valor</td>
                                <td>Valor referencia</td>
                                <td>Solicitud</td>
                            </tr>
                            <template v-for="(diferencias, k) in informe_tipo.informe">
                                <tr >
                                    <td>{{k+1}}</td>
                                    <td>{{diferencias.base_datos_revisada}}</td>
                                    <td>{{diferencias.base_datos_referencia}}</td>
                                    <td>{{diferencias.ejercicio}}</td>
                                    <td>{{diferencias.periodo}}</td>
                                    <td>{{diferencias.tipo}}</td>
                                    <td>{{diferencias.numero_folio_poliza}}</td>
                                    <td>{{diferencias.numero_movimiento}}</td>
                                    <td>{{diferencias.valor}}</td>
                                    <td>{{diferencias.valor_referencia}}</td>
                                    <td>
                                        <router-link :to="{name: 'solicitud-edicion-poliza-show', params: { id: diferencias.solicitud_id }}" target="_blank" v-if="diferencias.solicitud_id > 0">
                                            {{diferencias.solicitud_numero_folio}}
                                        </router-link>
                                    </td>
                                </tr>

                            </template>
                        </template>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "InformeEFOSCFD",
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
                        this.informe = data;
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
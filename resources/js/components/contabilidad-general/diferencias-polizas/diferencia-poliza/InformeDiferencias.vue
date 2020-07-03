<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-row">
                            <div class="col">
                                Empresa
                                <select class="form-control" v-model="id_empresa" @change="limpia()">
                                    <option value>-- Empresa --</option>
                                    <option v-for="item in empresas" v-bind:value="item.id">{{ item.nombre }}</option>
                                </select>
                            </div>
                            <div class="col">
                                Solicitud Relacionada
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="solicitud_relacionada"  v-model="sin_solicitud_relacionada" @change="limpia()">
                                    <label for="solicitud_relacionada" class="custom-control-label" v-model="sin_solicitud_relacionada">Sin Solicitud Relacionada</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="con_solicitud_relacionada"  v-model="con_solicitud_relacionada" @change="limpia()">
                                    <label for="con_solicitud_relacionada" class="custom-control-label" v-model="con_solicitud_relacionada">Con Solicitud Relacionada</label>
                                </div>
                            </div>
                            <div class="col">
                                ¿Sólo Diferencias Activas?
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="diferencias_activas"  v-model="solo_diferencias_activas" @change="limpia()">
                                    <label for="diferencias_activas" class="custom-control-label" v-model="solo_diferencias_activas">Si</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="diferencias_activas_no"  v-model="no_solo_diferencias_activas" @change="limpia()">
                                    <label for="diferencias_activas_no" class="custom-control-label" v-model="no_solo_diferencias_activas">No</label>
                                </div>
                            </div>
                            <div class="col">
                                Agrupación
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="por_tipo" name="agrupacion" checked value="1" v-model="tipo_agrupacion" @change="limpia()">
                                    <label class="custom-control-label" for="por_tipo">Empresa->Tipo->Diferencia</label>
                                </div>

                                                                <!-- Group of default radios - option 2 -->
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="por_poliza" name="agrupacion" value="2" v-model="tipo_agrupacion" @change="limpia()">
                                    <label class="custom-control-label" for="por_poliza">Empresa->Póliza->Diferencia</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button @click="consultar" class="btn btn-primary float-right" :disabled="cargando">
                                            <i class="fa fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-search" v-else></i>Consultar
                                        </button>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <PdfDiferencias v-bind:value="datos"></PdfDiferencias>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="informe && tipo_agrupacion == 1">
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
        <div class="row" v-if="informe && tipo_agrupacion == 2">
            <div class="col-md-12">
                <table class="table">
                    <tbody>
                    <template v-for="(informe_empresa,i) in informe">
                        <tr style="background-color: #000; color: #FFF">
                            <td colspan="9">{{informe_empresa.empresa}}</td>
                        </tr>
                        <template v-for="(informe_poliza, j) in informe_empresa.informe">
                            <tr style="background-color: #555555; color: #FFF">
                                <td colspan="9">{{informe_poliza.poliza}}
                                    <ImpresionPolizas v-bind:id="informe_poliza.id_relacion"></ImpresionPolizas> ({{informe_poliza.cantidad}})</td>
                            </tr>
                            <tr style="background-color: #999999" >
                                <td class="index_corto">#</td>
                                <td>Base de Datos Revisada</td>
                                <td>Base de Datos Referencia</td>
                                <td>Tipo Diferencia</td>
                                <td>Código Cuenta</td>
                                <td>No. Movto.</td>
                                <td>Valor</td>
                                <td>Valor referencia</td>
                                <td>Solicitud</td>
                            </tr>
                            <template v-for="(diferencias, k) in informe_poliza.informe">
                                <tr >
                                    <td>{{k+1}}</td>
                                    <td>{{diferencias.base_datos_revisada}}</td>
                                    <td>{{diferencias.base_datos_referencia}}</td>
                                    <td>{{diferencias.tipo}}</td>
                                    <td>{{diferencias.codigo_cuenta}}</td>
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
    import ImpresionPolizas from "./partials/ImpresionPolizas";
    import PdfDiferencias from "./PdfDiferencias";
    export default {
        name: "InformeDiferencias",
        components: {ImpresionPolizas, PdfDiferencias},
        data() {
            return {
                id_empresa:'',
                empresas :[],
                informe : [],
                tipo_agrupacion : 1,
                sin_solicitud_relacionada : true,
                con_solicitud_relacionada : false,
                solo_diferencias_activas : true,
                no_solo_diferencias_activas : false,
                cargando : false
            }
        },
        mounted() {
            this.getEmpresas();
        },
        computed:{
            datos(){
                return this.$data;
            },
        },
        methods :{
            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/index', {
                    params: {
                        sort: 'Nombre',
                        order: 'asc',
                        scope:'conDiferencias',
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.cargando = false;
                    })
            },
            limpia(){
                this.informe = [];
            },
            consultar() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/incidente-poliza/obtenerInforme', {
                    id_empresa: this.id_empresa,
                    sin_solicitud_relacionada : this.sin_solicitud_relacionada,
                    solo_diferencias_activas : this.solo_diferencias_activas,
                    con_solicitud_relacionada : this.con_solicitud_relacionada,
                    no_solo_diferencias_activas : this.no_solo_diferencias_activas,
                    tipo_agrupacion : this.tipo_agrupacion,
                })
                    .then(data => {
                        this.informe = data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            ver_solicitud() {
                this.$router.push({name: 'solicitud-edicion-poliza-show', params: {id: this.value.id}});
            },
        }
    }
</script>

<style scoped>

</style>
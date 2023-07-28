<template>
    <span>
        <div class="spinner-border text-success" role="status" v-if="!poliza">
           <span class="sr-only">Cargando...</span>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-sm table-fs-sm">
                        <thead>
                            <tr>
                                <th class="c200">Empresa</th>
                                <th class="c90">Fecha de Póliza</th>
                                <th class="c90">Folio de Poliza</th>
                                <th class="c90">Tipo de Póliza</th>
                                <th class="c200">CFDI Asociado</th>
                                <th>Concepto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{poliza.empresa}}
                                </td>
                                <td style="text-align: center">
                                    {{poliza.fecha}}
                                </td>
                                <td>
                                    {{poliza.folio}}
                                </td>
                                <td>
                                    {{poliza.tipo.nombre}}
                                </td>
                                <td>
                                    <div  v-for="(cfdi, i) in poliza.cfdi.data">
                                        <CFDI v-if="cfdi" v-bind:txt="cfdi.uuid" v-bind:id="cfdi.id" @click="cfdi.id" ></CFDI>
                                    </div>
                                </td>
                                <td>
                                    {{poliza.concepto}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 table-responsive" style="overflow-y: auto;max-height: 600px;">
                    <table class="table table-sm table-fs-sm">
                        <thead>
                            <tr>
                                <th class="index_corto">#</th>
                                <th class="no_parte">Cuenta</th>
                                <th class="no_parte">Descripción</th>
                                <th class="">Cargo</th>
                                <th class="">Abono</th>
                                <th class="">Referencia</th>
                                <th class="c200">CFDI Asociado</th>
                                <th class="">Concepto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(movimiento, i) in poliza.movimientos_poliza.data">
                                <td>{{ i + 1 }}</td>
                                <td>{{movimiento.cuenta.cuenta_format}}</td>
                                <td>{{movimiento.cuenta.descripcion}}</td>
                                <td style="text-align: right">{{movimiento.cargo_format}}</td>
                                <td style="text-align: right">{{movimiento.abono_format}}</td>
                                <td>{{movimiento.referencia}}</td>
                                <td v-if="movimiento.asociacion_cfdi">
                                    <CFDI v-if="movimiento.asociacion_cfdi.cfdi" v-bind:txt="movimiento.asociacion_cfdi.cfdi.uuid" v-bind:id="movimiento.asociacion_cfdi.cfdi.id" @click="movimiento.asociacion_cfdi.cfdi.id" ></CFDI>
                                </td>
                                <td v-else></td>
                                <td>{{movimiento.concepto}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="border: none; text-align: right"><b>Total Póliza:</b></td>
                                <td style="text-align: right">{{poliza.cargos_format}}</td>
                                <td style="text-align: right">{{poliza.abonos_format}}</td>
                                <td colspan="3" style="border: none"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    Usuario Registró: <strong>{{poliza.usuario_nombre}}</strong>
                </div>

            </div>
        </div>
    </span>

</template>

<script>
import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI";
export default {
    name: "poliza-partial-show",
    props : ['id', 'id_empresa','poliza_parametro'],
    components : {CFDI},
    data(){
        return {
            cargando:false,
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find()
        {
            if(this.poliza == null){
                this.cargando = true;
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
                return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                    id: this.id,
                    params: {
                        include: ['movimientos_poliza.asociacion_cfdi', 'tipo', 'asociacion_cfdi', 'posibles_cfdi'],
                        id_empresa: this.id_empresa
                    }
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                }).finally(() => {
                    this.cargando = false;
                });
            }
            else if(this.id > 0 && this.id_empresa > 0 && this.id != this.poliza.id) {
                this.cargando = true;
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
                return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                    id: this.id,
                    params: {
                        include: ['movimientos_poliza.asociacion_cfdi', 'tipo', 'asociacion_cfdi', 'posibles_cfdi'],
                        id_empresa: this.id_empresa
                    }
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                }).finally(() => {
                    this.cargando = false;
                });
            }
            else if(this.poliza_parametro != null){
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', this.poliza_parametro);
                this.cargando = false;
            }
        }
    },
    computed: {
        poliza(){
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        },
    },
}
</script>

<style scoped>

tr.hover td{
    background-color: #b8daa9;
}

tr.click td{
    background-color: #50b920;
}

.form-control {
    font-size: 10px !important;
}
table {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}
table.table-fs-sm{
    font-size: 10px;
}

table th,  table td {
    border: 1px solid #dee2e6;
}

table thead th
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: bold;
    color: black;
    overflow: hidden;
    text-align: center;

    position: sticky;
    position: -webkit-sticky;
    top: 0;
    z-index: 2;
}

table thead th.no_negrita
{
    padding: 0.2em;

    background-color: #f2f4f5;
    font-weight: normal;
    color: black;
    overflow: hidden;
    text-align: center;
}

table td.sin_borde {
    border: none;
    padding: 2px 5px;
}

table thead th {
    text-align: center;
}
table tbody tr
{
    border-width: 0 1px 1px 1px;
    border-style: none solid solid solid;
    border-color: white #CCCCCC #CCCCCC #CCCCCC;
}
table tbody td,
table tbody th
{
    border-right: 1px solid #ccc;
    color: #242424;
    line-height: 20px;
    overflow: hidden;
    padding: 2px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

.encabezado{
    text-align: center;
    background-color: #f2f4f5;
    font-weight: bold;
}

.sin_borde{
    border:none; background-color:#FFF
}
</style>

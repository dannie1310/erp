<template>
    <span>
        <div class="card" v-if="solicitud == null">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <encabezado v-bind:reembolso="solicitud" />
                        <div class="row" v-if="reembolso && solicitud">
                            <div class="col-md-12" style="text-align: right;">
                                <h5><b>Con Segmentos Cargados (TOTALES)</b></h5>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr>
                                            <th class="encabezado">
                                                Folio de Documentos Asociados
                                            </th>
                                            <th class="encabezado">
                                                Empresa
                                            </th>
                                            <th class="encabezado">
                                                Empleado
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>
                                                {{reembolso.folio}}
                                            </th>
                                            <th>
                                                {{solicitud.empresa.razon_social}}
                                            </th>
                                            <th>
                                                {{reembolso.empleado_descripcion}}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="encabezado">
                                                Proveedor
                                            </th>
                                            <th class="encabezado">
                                                Concepto
                                            </th>
                                            <th class="encabezado  c130">
                                                Fecha
                                            </th>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ solicitud.proveedor.razon_social }}
                                            </td>
                                            <td>
                                                <input name="concepto"
                                                       id="concepto"
                                                       class="form-control"
                                                       v-model="solicitud.concepto"
                                                       v-validate="{required: true}"
                                                       data-vv-as="Concepto"
                                                       :class="{'is-invalid': errors.has('concepto')}" />
                                                <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                            </td>

                                            <td>
                                               {{ solicitud.fecha_format }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="encabezado  c130">
                                                Fecha Limite de Pago
                                            </th>
                                            <th class="encabezado">Moneda</th>
                                            <th class="encabezado">Importe</th>
                                        </tr>
                                        <tr>
                                            <td>{{ solicitud.fecha_vencimiento }}</td>
                                            <td><b>{{ solicitud.moneda }}</b></td>
                                            <td style="text-align: right; font-size: 15px"><b>{{ solicitud.importe_format }}</b></td>
                                        </tr>
                                        <tr>
                                            <th class="encabezado">IVA</th>
                                            <th class="encabezado">Retenciones</th>
                                            <th class="encabezado">Otros Impuestos</th>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; font-size: 15px"><b>{{ solicitud.iva_format }}</b></td>
                                            <td style="text-align: right; font-size: 15px"><b>{{ solicitud.retenciones_format}}</b></td>
                                            <td style="text-align: right; font-size: 15px"><b>{{ solicitud.otros_format }}</b></td>
                                        </tr>
                                        <tr>
                                            <th class="encabezado">Total</th>
                                            <th class="encabezado">Forma de Pago</th>
                                            <th class="encabezado">Tipo de Pago</th>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; font-size: 15px"><b>{{ solicitud.total_format }}</b></td>
                                            <td>
                                                <select class="form-control"
                                                        data-vv-as="Forma de Pago"
                                                        id="forma_pago"
                                                        name="forma_pago"
                                                        :class="{'is-invalid': errors.has('forma_pago')}"
                                                        v-validate="{required: true}"
                                                        v-model="solicitud.id_forma_pago">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in formas_pago" :value="m.id">{{m.nombre}}</option>
                                                </select>
                                                <div style="display:block" class="invalid-feedback" v-show="errors.has('forma_pago')">{{ errors.first('forma_pago') }}</div>
                                            </td>
                                            <td>
                                                <label class="form-control">Pago a Proveedor</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="encabezado">Cuenta Bancaria</th>
                                            <th class="encabezado">Instrucciones de Entrega</th>
                                            <th class="encabezado">Solicitante</th>
                                        </tr>
                                        <tr>
                                            <td v-if="solicitud.id_forma_pago != '' && solicitud.id_forma_pago != 1">
                                                <select class="form-control"
                                                        data-vv-as="Cuenta Bancaria"
                                                        id="cuenta"
                                                        name="cuenta"
                                                        :class="{'is-invalid': errors.has('cuenta')}"
                                                        v-validate="{required: true}"
                                                        v-model="solicitud.cuenta">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in cuentas" :value="m.id">{{m.banco_descripcion}} {{m.numero_cuenta}} -</option>
                                                </select>
                                                <div style="display:block" class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                                            </td>
                                            <td v-else>
                                                <label class="form-control"> NO APLICA </label>
                                            </td>
                                            <td v-if="solicitud.id_forma_pago != ''">
                                                <select  class="form-control"
                                                         data-vv-as="Forma de Pago"
                                                         id="instruccion"
                                                         name="instruccion"
                                                         :class="{'is-invalid': errors.has('instruccion')}"
                                                         v-validate="{required: true}"
                                                         v-model="solicitud.id_entrega">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in instrucciones" :value="m.id">{{m.descripcion}}</option>
                                                </select><div style="display:block" class="invalid-feedback" v-show="errors.has('instruccion')">{{ errors.first('instruccion') }}</div>
                                            </td>
                                            <td v-else>

                                            </td>
                                            <td>
                                                <select  class="form-control"
                                                         data-vv-as="Solicitante"
                                                         id="solicitante"
                                                         name="solicitante"
                                                         :class="{'is-invalid': errors.has('solicitante')}"
                                                         v-validate="{required: true}"
                                                         v-model="reembolso.id_solicitud">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in solicitantes" :value="m.id">{{m.descripcion_st}}</option>
                                                </select><div style="display:block" class="invalid-feedback" v-show="errors.has('solicitante')">{{ errors.first('solicitante') }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-info" :disabled="errors.count() > 0" @click="editar"><i class="fa fa-save" ></i> Actualizar</button>
                    <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0" @click="eliminar"><i class="fa fa-trash"></i> Eliminar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Encabezado from "./partials/Encabezado";
export default {
    name: "solicitud-edit",
    components: {Encabezado},
    props: ['id', 'tipo'],
    data() {
        return {
            cargando: false,
            solicitud: null,
            formas_pago: [],
            forma_pago: '',
            cuentas: [],
            cuenta: '',
            instrucciones: [],
            instruccion: '',
            solicitantes: [],
            solicitante: '',
            reembolso: null
        }
    },
    mounted() {
        this.find();
        this.getFirmasFirmantes();
        this.getFormaPago();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.reembolso = data
                if(this.reembolso.estado == 600)
                {
                    this.findPorSolicitud();
                }
                if(this.reembolso.estado == 700)
                {
                    this.findPagoAProveedor();
                }
            })
        },
        findPorSolicitud() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/pago-reembolso-por-solicitud/find', {
                id: this.reembolso.id_solicitud,
                params: {include: [ 'proveedor.cuentas' ]}
            }).then(data => {
                this.solicitud = data;
                this.cuentas = data.proveedor.cuentas.data;
                this.solicitante = data.id_solicitante;
            }).finally(() => {
                this.cargando = false;
            })
        },
        findPagoAProveedor() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/pago-a-proveedor/find', {
                id: this.reembolso.id_solicitud,
                params: {include: [ 'proveedor.cuentas' ]}
            }).then(data => {
                this.solicitud = data;
                this.cuentas = data.proveedor.cuentas.data;
                this.solicitante = data.id_solicitante;
            }).finally(() => {
                this.cargando = false;
            })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.editar();
                }
            });
        },
        editar() {
            if(moment(this.solicitud.fecha_final_editar).format('YYYY/MM/DD') < moment(this.solicitud.fecha_inicio_editar).format('YYYY/MM/DD'))
            {
                swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
            }
            else {
                return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/update', {
                    id: this.solicitud.id,
                    data: this.solicitud
                }).then((data) => {
                    this.solicitud = data;
                })
            }
        },
        eliminar() {
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/delete', {
                id: this.id,
                params: {}
            }).then(() => {
                this.salir();
            })
        },
        getFormaPago() {
            return this.$store.dispatch('controlRecursos/forma-pago/index', {
                params: { scope:'activo' }
            }).then(data => {
                this.formas_pago = data.data;
            })
        },
        getInstrucciones() {
            return this.$store.dispatch('controlRecursos/entrega/index', {
                params: { scope:'tipo:'+ this.forma_pago }
            }).then(data => {
                this.instrucciones = data.data;
            })
        },
        getFirmasFirmantes() {
            return this.$store.dispatch('controlRecursos/firma-firmante/index', {
                params: { scope:'firmasSolicitantes' }
            }).then(data => {
                this.solicitantes = data.data;
            })
        },
    },
    watch: {
        forma_pago(value) {
            if (value) {
                if(value != '') {
                    this.getInstrucciones();
                }

            }
        },
    }
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}
td, th{
    border: 1px #dee2e6 solid;
}
</style>

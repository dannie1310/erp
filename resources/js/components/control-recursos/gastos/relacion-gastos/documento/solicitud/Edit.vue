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

                                            <td style="text-align: center;">
                                                <b>{{ solicitud.fecha_format }}</b>
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
                                            <td>{{ solicitud.fecha_vencimiento_format }}</td>
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
                                                        v-model="forma_pago">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in formas_pago" :value="m.id">{{m.nombre}}</option>
                                                </select>
                                                <div style="display:block" class="invalid-feedback" v-show="errors.has('forma_pago')">{{ errors.first('forma_pago') }}</div>
                                            </td>
                                            <td>
                                                <b>Pago a Proveedor</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="encabezado">Cuenta Bancaria</th>
                                            <th class="encabezado">Instrucciones de Entrega</th>
                                            <th class="encabezado">Solicitante</th>
                                        </tr>
                                        <tr>
                                            <td v-if="forma_pago != '' && forma_pago != 1">
                                                <select class="form-control"
                                                        data-vv-as="Cuenta Bancaria"
                                                        id="cuenta"
                                                        name="cuenta"
                                                        :class="{'is-invalid': errors.has('cuenta')}"
                                                        v-validate="{required: true}"
                                                        v-model="cuenta">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in cuentas" :value="m.id">{{m.banco_descripcion}} {{m.numero_cuenta}} -</option>
                                                </select>
                                                <div style="display:block" class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                                            </td>
                                            <td v-else>
                                                <b>NO APLICA </b>
                                            </td>
                                            <td v-if="forma_pago != ''">
                                                <select  class="form-control"
                                                         data-vv-as="Instrucción"
                                                         id="instruccion"
                                                         name="instruccion"
                                                         :class="{'is-invalid': errors.has('instruccion')}"
                                                         v-validate="{required: true}"
                                                         v-model="instruccion">
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
                                                         v-model="solicitante">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(m) in solicitantes" :value="m.id">{{m.descripcion_st}}</option>
                                                </select><div style="display:block" class="invalid-feedback" v-show="errors.has('solicitante')">{{ errors.first('solicitante') }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-info" :disabled="errors.count() > 0" @click="editar" v-if="$root.can('editar_solicitud_pago_reembolso', true)"><i class="fa fa-save" ></i> Actualizar</button>
                    <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0" @click="eliminar" v-if="$root.can('eliminar_solicitud_pago_reembolso', true)"><i class="fa fa-trash"></i> Eliminar</button>
                    <PDF v-bind:id="this.solicitud.id" v-if="$root.can('consultar_solicitud_pago_reembolso', true)"></PDF>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Encabezado from "./partials/Encabezado";
import PDF from "./Formato";
export default {
    name: "solicitud-edit",
    components: {Encabezado, PDF},
    props: ['id'],
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
        this.getFirmasFirmantes();
        this.getFormaPago();
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                    this.reembolso = data
                console.log(this.reembolso)
                console.log(this.reembolso.reembolsos.data[0].id_tipo)
                if(this.reembolso.reembolsos.data[0].id_tipo == 13)
                {
                    this.findPorSolicitud();
                }
                if(this.reembolso.reembolsos.data[0].id_tipo == 12)
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
                this.forma_pago = data.id_forma_pago;
                this.cuentas = data.proveedor.cuentas.data;
                this.solicitante = data.id_solicitante;
                this.cuenta = data.cuenta;
                this.instruccion = data.id_entrega;
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
                    this.forma_pago = data.id_forma_pago;
                    this.cuentas = data.proveedor.cuentas.data;
                    this.solicitante = data.id_solicitante;
                    this.instruccion = data.id_entrega;
                    this.cuenta = data.cuenta;
                    console.log(this.solicitud)
                console.log(this.forma_pago)
                console.log(this.cuentas)
                console.log(this.solicitante)
                console.log(this.instruccion)
                console.log(this.cuenta)

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
            var id = this.solicitud.id;
            var datos = {
                'id_solicitante' : this.solicitante,
                'id_entrega' : this.instruccion,
                'id_forma_pago' : this.forma_pago,
                'cuenta' : this.cuenta,
                'concepto' : this.solicitud.concepto
            }

            if(this.reembolso.reembolsos.data[0].id_tipo == 13) {
                return this.$store.dispatch('controlRecursos/pago-reembolso-por-solicitud/update', {
                    id: id,
                    data: datos
                }).then((data) => {
                    this.salir()
                })
            }
            if(this.reembolso.reembolsos.data[0].id_tipo == 12) {
                return this.$store.dispatch('controlRecursos/pago-a-proveedor/update', {
                    id: id,
                    data: this.reembolso
                }).then((data) => {
                    this.salir()
                })
            }
        },
        eliminar() {
            if(this.reembolso.reembolsos.data[0].id_tipo == 13) {
                return this.$store.dispatch('controlRecursos/pago-reembolso-por-solicitud/delete', {
                    id: this.solicitud.id,
                    params: {}
                }).then(() => {
                    this.salir();
                })
            }

            if(this.reembolso.reembolsos.data[0].id_tipo == 12) {
                return this.$store.dispatch('controlRecursos/pago-a-proveedor/delete', {
                    id: this.solicitud.id,
                    params: {}
                }).then(() => {
                    this.salir();
                })
            }
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
                    if(value == 1)
                    {
                        this.cuenta = null
                    }
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

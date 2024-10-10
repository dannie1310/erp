<template>
    <span>
        <div class="card" v-if="reembolso == null">
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
                    <div class="col-md-12" v-if="relacion.estado == 6">
                        <encabezado-por-solicitud  v-bind:reembolso="relacion" />
                        <tabla-datos-por-solicitud v-bind:reembolso="relacion" />
                        <hr />
                    </div>
                    <div class="col-md-12" v-if="relacion.estado == 600">
                        <encabezado-pago-a-proveedor  v-bind:reembolso="relacion" />
                        <tabla-datos-pago-a-proveedor v-bind:reembolso="reembolso" />
                        <hr />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="forma_pago">Forma de Pago:</label>
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
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="forma_pago">Tipo de Pago:</label>
                            <label class="form-control">Pago a Proveedor</label>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="forma_pago != '' && forma_pago != 1">
                        <div class="form-group error-content">
                            <label for="cuenta">Cuenta Bancaria:</label>
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
                        </div>
                    </div>
                    <div class="col-md-3" v-else>
                        <div class="form-group error-content">
                            <label for="cuenta">Cuenta Bancaria:</label>
                            <label class="form-control"> NO APLICA </label>
                        </div>
                    </div>
                    <div class="col-md-3"  v-if="forma_pago != ''">
                        <div class="form-group error-content">
                            <label for="instruccion">Instrucciones de entrega:</label>
                            <select  class="form-control"
                                    data-vv-as="Forma de Pago"
                                    id="instruccion"
                                    name="instruccion"
                                    :class="{'is-invalid': errors.has('instruccion')}"
                                    v-validate="{required: true}"
                                    v-model="instruccion">
                                <option value>-- Selecionar --</option>
                                <option v-for="(m) in instrucciones" :value="m.id">{{m.descripcion}}</option>
                            </select><div style="display:block" class="invalid-feedback" v-show="errors.has('instruccion')">{{ errors.first('instruccion') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="solicitante">Solicitante:</label>
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Registrar</button>
                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import EncabezadoPorSolicitud from "../reembolso/por-solicitud/partials/Encabezado";
import EncabezadoPagoAProveedor from "../reembolso/pago-a-proveedor/partials/Encabezado";
import TablaDatosPorSolicitud from "../reembolso/por-solicitud/partials/TablaDatos";
import TablaDatosPagoAProveedor from "../reembolso/pago-a-proveedor/partials/TablaDatos";
export default {
    name: "solicitud-create",
    components: {EncabezadoPagoAProveedor, TablaDatosPagoAProveedor, EncabezadoPorSolicitud, TablaDatosPorSolicitud},
    props: ['id'],
    data() {
        return {
            cargando: false,
            relacion: null,
            reembolso: null,
            formas_pago: [],
            forma_pago: '',
            cuentas: [],
            cuenta: '',
            instrucciones: [],
            instruccion: '',
            solicitantes: [],
            solicitante: ''
        }
    },
    mounted() {
        this.getFirmasFirmantes();
        this.find();
        this.getFormaPago();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: ['reembolsos.proveedor.cuentas']}
            }).then(data => {
                this.relacion = data
                if(this.relacion.reembolsos.data[0].id_tipo == 13)
                {
                    this.getReembolsoSol();
                }
                if(this.relacion.reembolsos.data[0].id_tipo == 12)
                {
                    this.getReembolso()
                }
                this.cuentas =  this.relacion.reembolsos.data[0].proveedor.cuentas.data;
                this.solicitante =  this.relacion.reembolsos.data[0].id_solicitante;
            })
        },
        getReembolso() {
            return this.$store.dispatch('controlRecursos/reembolso-pago-a-proveedor/find', {
                id: this.relacion.reembolsos.data[0].id,
                params:{include: []}
            }).then(data => {
                this.reembolso = data;
            }).finally(()=> {
                this.cargando = false;
            })
        },
        getReembolsoSol() {
            return this.$store.dispatch('controlRecursos/reembolso-gasto-sol/find', {
                id: this.relacion.reembolsos.data[0].id,
                params:{include: []}
            }).then(data => {
                this.reembolso = data;
            }).finally(()=> {
                this.cargando = false;
            })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.reembolso.id_tipo == 13)
                    {
                        this.storePagoReembolsoPorSolicitud();
                    }
                    if(this.reembolso.id_tipo == 12)
                    {
                        this.storePagoAProveedor();
                    }
                }
            });
        },
        storePagoReembolsoPorSolicitud() {
            return this.$store.dispatch('controlRecursos/pago-reembolso-por-solicitud/store', {
                reembolso : this.reembolso,
                forma_pago : this.forma_pago,
                cuenta : this.cuenta,
                instruccion : this.instruccion,
                solicitante : this.solicitante
            }).then(data => {
                this.$router.push({name: 'solicitud-reembolso-edit', params: {id: this.relacion.id}});
            })
        },
        storePagoAProveedor() {
            return this.$store.dispatch('controlRecursos/pago-a-proveedor/store', {
                reembolso : this.reembolso,
                forma_pago : this.forma_pago,
                cuenta : this.cuenta,
                instruccion : this.instruccion,
                solicitante : this.solicitante
            }).then(data => {
                this.$router.push({name: 'solicitud-reembolso-edit', params: {id: this.relacion.id}});
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

</style>

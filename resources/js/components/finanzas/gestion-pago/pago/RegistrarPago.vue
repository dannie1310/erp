<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Registro de pagos
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-3">
                                    <label for="tipo" class="col-lg-12 col-form-label">Seleccionar Tipo</label>
                                    <select
                                        type="text"
                                        name="tipo"
                                        data-vv-as="Tipo"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="tipo"
                                        v-model="tipo"
                                        :class="{'is-invalid': errors.has('tipo')}"
                                    >
                                            <option value>-- Seleccione un Tipo --</option>
                                            <option v-for="(tipo, index) in tipos" :value="index">{{tipo}}</option>
                                        </select>
                                </div>

                                <div class="col-md-8">
                                    <label for="id_documento" class="col-lg-12 col-form-label">Seleccionar Beneficiario</label>
                                        <select
                                            :disabled="datos.length == 0"
                                            type="text"
                                            name="id_documento"
                                            data-vv-as="Tipo"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="id_documento"
                                            v-model="id_documento"
                                            :class="{'is-invalid': errors.has('id_documento')}"
                                        >
                                        <option value>-- Seleccione un Tipo --</option>
                                        <option v-for="(data, index) in datos" :value="data.id_empresa">{{data.razon_social}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row" v-if="id_documento">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Documentos
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-9">
                                    <div class="form-group error-content">
                                        <label for="empresa">Empresa</label>
                                        <input type="text" class="form-control" id="empresa" placeholder="empresa" style="width:100%" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group error-content">
                                        <label for="autorizado">Importe Autorizado</label>
                                        <input type="text" class="form-control" id="autorizado" placeholder="autorizado" style="width:100%" disabled>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="destinatario">Destinatario</label>
                                        <input type="text" class="form-control" id=destinatario placeholder="destinatario" style="width:100%" >
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="concepto">Concepto</label>
                                        <input type="text" class="form-control" id="concepto" placeholder="concepto" style="width:100%" >
                                    </div>
                                </div>
                                <div class="col-md-12 mt-1 text-left" >
                                      <label class="text-secondary">Cheque/Pago </label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group error-content">
                                        <label for="concepto">Cuenta Bancaria</label>
                                        <input type="text" class="form-control" id="concepto" placeholder="cuenta" style="width:100%" >
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group error-content">
                                        <label for="empresa">Moneda</label>
                                        <input type="text" class="form-control" id="empresa" placeholder="moneda" style="width:100%" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="referencia">Número Cheque / Referencia</label>
                                        <input type="text" class="form-control" id="referencia" placeholder="referencia" style="width:100%" >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="emision">Fecha de Emisión</label>
                                        <input type="text" class="form-control" id="emision" placeholder="emision" style="width:100%" >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="cobro">Fecha de Cobro</label>
                                        <input type="text" class="form-control" id="cobro" placeholder="cobro" style="width:100%" >
                                    </div>
                                </div>
                                <div class="col-md-12 mt-1 text-left" >
                                      <label class="text-secondary"></label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "gestion-registro-pago",
        data() {
            return {
                tipos:{
                    0:'Facturas',
                    1:'Solicitudes'
                },
                tipo:'',
                id_documento:'',
                datos:[],
                cargando: false,
            }
        },
        mounted() {
        },
        computed: {
            facturas(){
                return this.$store.getters['finanzas/factura/facturas'];
            },
            meta(){
                return this.$store.getters['finanzas/factura/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        methods: {
            getFacturas(){
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/factura/autorizadas', {
                    params: {
                        scope: 'pendientePago',
                        sort: 'id_transaccion',
                        order: 'ASC'
                    }
                })
                    .then(data => {
                        this.datos = data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            getSolicitudes(){
            },
            getFacturasPendientes(){
                this.cargando = true;
                let self = this
                return self.$store.dispatch('finanzas/factura/pendientes_pago', {
                    id: this.id_documento,
                    params: {
                        scope: ['pendientePago', 'conDocumento'],
                        include:['documento', 'moneda', 'empresa'],
                        sort: 'id_transaccion',
                        order: 'DESC'
                    }
                })
                    .then(data => {
                        console.log(data.data);
                        this.$store.commit('finanzas/factura/SET_FACTURAS', data.data);
                        this.$store.commit('finanzas/factura/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            }
        },
        watch: {
            tipo(value){
                if(value){
                    if(parseInt(value) === 0) {
                        this.getFacturas();
                    }
                    if(parseInt(value) === 1) {
                        this.getSolicitudes();
                    }
                }
            },
            id_documento(value){
                if(value){
                    if(parseInt(this.tipo) === 0) {
                        this.getFacturasPendientes();
                    }
                    if(parseInt(this.tipo) === 1) {
                        this.getSolicitudes();
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>

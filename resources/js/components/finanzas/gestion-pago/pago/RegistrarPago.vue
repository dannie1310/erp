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
                        <datatable v-bind="$data" />
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
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Descripción', field: 'descripcion', sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true},
                    { title: 'Vencimiento', field: 'vencimiento',sortable: true},
                    { title: 'Moneda', field: 'moneda', sortable: true},
                    { title: 'Monto', field: 'monto', sortable: true},
                    { title: 'A Pagar', field: 'pagar', sortable: true},
                    { title: 'T/C', field: 'tipo_cambio'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {sort: 'id_transaccion', order: 'desc'},
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
                        include:['documento', 'moneda'],
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
            facturas: {
                handler(facturas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = facturas.map((factura, i) => ({
                        index: (i + 1),
                        descripcion: factura.observaciones,
                        fecha: factura.fecha_format,
                        vencimiento: factura.vencimiento,
                        moneda: factura.moneda.nombre,
                        monto: factura.monto_format,
                        pagar: factura.a_pagar,
                        tipo_cambio: factura.tipo_cambio,
                        buttons: $.extend({}, {
                            pagar: self.$root.can('registrar_pago') ? true : true,
                            id: factura.id
                        })
                    }));
                },
                deep: true
            },
            meta: {
                handler (meta) {
                    this.total = meta.pagination.total
                },
                deep: true
            },
            query: {
                handler () {
                    this.getFacturasPendientes()
                },
                deep: true
            },
            search(val) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {
                    this.query.search = val;
                    this.query.offset = 0;
                    this.getFacturasPendientes();
                }, 500);
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            },

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

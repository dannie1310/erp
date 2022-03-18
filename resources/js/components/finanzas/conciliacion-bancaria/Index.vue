<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4" v-if="cuentas">
                            <div class="form-group">
                                <label for="id_cuenta">Cuenta Bancaria:</label>
                                <select
                                    class="form-control"
                                    name="id_cuenta"
                                    data-vv-as="Cuenta"
                                    id="id_cuenta"
                                    v-model="id_cuenta"
                                    v-validate="{required: true}"
                                    :class="{'is-invalid': errors.has('id_cuenta')}">
                                    <option value>-- Cuenta --</option>
                                    <option v-for="(item, index) in cuentas" :value="item.id">
                                        {{ `${item.numero} ${item.abreviatura } (${item.empresa.razon_social})` }}
                                    </option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
                            </div>
                        </div>
                        <div class="col-md2">
                            <label>Periodo</label>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <datepicker v-model = "fecha_inicial"
                                            name = "fecha_inicial"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('fecha_inicial')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <datepicker v-model = "fecha_final"
                                            name = "fecha_final"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas:"
                                            :class="{'is-invalid': errors.has('fecha_final')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "conciliacion-bancaria-index",
        components: {datepicker, es},
        data() {
            return {
                es: es,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true},
                    { title: 'Beneficiario', field: 'destino', thComp:require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Cuenta', field: 'numero_cuenta',  thComp:require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Concepto', field: 'observaciones',  thComp:require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Monto', field: 'monto', thClass: 'th_c80', tdClass: 'td_money80', sortable: true},
                    { title: 'Moneda', field: 'id_moneda',  thComp:require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Estado', field: 'estado', thClass: 'th_c80',  thComp:require('../../globals/th-Filter').default, sortable: true },
                    //{ title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['moneda','cuenta','empresa','relaciones' ], scope: '', sort: 'id_transaccion', order: 'desc'},
                estado: "",
                cargando: false,
                cuentas: [],
                id_cuenta: '',
                fecha_inicial: '',
                fecha_final: '',
                fechasDeshabilitadas:{},
            }
        },

        mounted() {
            this.fecha_inicial = new Date();
            this.fecha_final = new Date();
            this.fechasDeshabilitadas.from= new Date();
            this.getCuentas();
            if(this.id_cuenta != ''){
                this.$Progress.start();
                this.paginate()
                    .finally(() => {
                        this.$Progress.finish();
                    })
            }
        },

        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/pago/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/pago/SET_PAGOS', data.data);
                        this.$store.commit('finanzas/pago/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getCuentas() {
                return this.$store.dispatch('cadeco/cuenta/index', {
                }).then(data => {
                    this.cuentas = data.data;
                    this.cargando = false;
                });
            },
        },
        computed: {
            pagos(){
                return this.$store.getters['finanzas/pago/pagos'];
            },
            meta(){
                return this.$store.getters['finanzas/pago/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            pagos: {
                handler(pagos) {
                    let self = this
                    self.$data.data = []
                    pagos.forEach(function (pago, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: pago.numero_folio_format,
                            fecha: pago.fecha_format,
                            destino: pago.destino,
                            numero_cuenta: pago.cuenta.numero,
                            observaciones: pago.observaciones,
                            monto: pago.monto_format,
                            estado: pago.estado_string,
                            id_moneda:pago.moneda.nombre,
                            buttons: $.extend({}, {
                                id: pago.id,
                                delete: self.$root.can('eliminar_pagos') && (pago.es_reemplazo == false) ? true : false,
                                transaccion: {id:pago.id, tipo:82},
                            })
                        })
                    });
                },
                deep: true
            },


            meta: {
                handler(meta) {
                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler(query) {
                    this.paginate(query)
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
                    this.paginate();

                }, 500);
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        }
    }
</script>

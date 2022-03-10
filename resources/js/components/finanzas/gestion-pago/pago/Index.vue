<template>
    <div class="row">
        <div class="col-12"  :disabled="cargando">
            <button  @click="create" title="Crear" class="btn btn-app btn-info float-right"  v-if="$root.can('cargar_bitacora')">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Bitácora
                (SANTANDER)
            </button>
            <router-link  :to="{ name: 'registro-pago'}" v-if="$root.can('registrar_pago')" type="button" class="btn btn-app btn-info float-right" title="Registrar Pagos">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-money" v-else></i>
                Registrar
            </router-link>
            <router-link  :to="{ name: 'carga-masiva'}" v-if="$root.can('consultar_carga_layout_pago')" type="button" class="btn btn-app btn-info float-right" title="Carga masiva">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-file-upload" v-else></i>
                Carga Masiva
            </router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
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
    export default {
        name: "gestion-pago-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true},
                    { title: 'Beneficiario', field: 'destino', thComp:require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Cuenta', field: 'numero_cuenta',  thComp:require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Concepto', field: 'observaciones',  thComp:require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Monto', field: 'monto', thClass: 'th_money', tdClass: 'td_money', sortable: true},
                    { title: 'Moneda', field: 'id_moneda',  thComp:require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Estado', field: 'estado',  thComp:require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['moneda','cuenta','empresa','relaciones' ], sort: 'id_transaccion', order: 'desc'},
                estado: "",
                cargando: false,
            }
        },

        mounted() {
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            create() {
                this.$router.push({name: 'pago-create'});
            },
            create_pago() {
                this.$router.push({name: 'gestion-registro-pago'});
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

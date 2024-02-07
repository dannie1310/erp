<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_comprobante_fondo')" class="btn btn-app pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "comprobante-fondo-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:'th_index_corto', sortable: false },
                    { title: 'Fecha', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default, thClass: 'th_fecha'},
                    { title: 'Número Folio', field: 'numero_folio',  tdClass:'center', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Fondo', field: 'id_referente', tdClass:'center', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Referencia',  field: 'referencia', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Total', field: 'total', tdClass:'center', sortable: false},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_c150', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: { sort: 'numero_folio', order: 'desc'},
                estado: "",
                cargando: false
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
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/comprobante-fondo/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/comprobante-fondo/SET_FONDOS', data.data);
                        this.$store.commit('finanzas/comprobante-fondo/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'comprobante-fondo-create'});
            },
        },
        computed: {
            fondos(){
                return this.$store.getters['finanzas/comprobante-fondo/fondos'];
            },
            meta(){
                return this.$store.getters['finanzas/comprobante-fondo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            fondos: {
                handler(fondos) {
                    let self = this;
                    self.$data.data = [];
                    fondos.forEach(function (fondo, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            fecha: fondo.fecha_format,
                            numero_folio: fondo.numero_folio_format,
                            id_referente: fondo.fondo.descripcion,
                            referencia: fondo.referencia,
                            total: fondo.total_format,
                            buttons: $.extend({}, {
                                show : self.$root.can('consultar_comprobante_fondo') ? true : false,
                                eliminar : self.$root.can('eliminar_comprobante_fondo') ? true : false,
                                id: fondo.id,
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

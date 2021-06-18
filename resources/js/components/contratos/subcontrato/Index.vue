<template>
    <div class="row">
        <div class="col-md-12">
            <Registro @created="paginate()"></Registro>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="search">
                            </div>
                        </div>
                    </div>
                </div>
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
    import Registro from "./partials/Registrar";
    export default {
        name: "estimacion-index",
        components:{Registro},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', tdClass: 'folio', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Referencia', field: 'referencia', sortable: true, thComp: require('../../globals/th-Filter').default },
                    { title: 'Fecha', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default },
                    { title: 'Contrato Proyectado', tdClass: 'folio', field: 'numero_folio_cp', thComp: require('../../globals/th-Filter').default},
                    { title: 'Referencia Contrato Proyectado', field: 'referencia_cp', sortable: false, thComp: require('../../globals/th-Filter').default },
                    { title: 'Contratista', field: 'contratista', sortable: false, thComp: require('../../globals/th-Filter').default },
                    { title: 'Monto', field: 'monto', tdClass: ['th_money', 'text-right'], sortable: true, thComp: require('../../globals/th-Filter').default },
                    { title: 'Estado', field: 'estado', sortable: false, tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default, thComp: require('../../globals/th-Filter').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'DESC', include:['contrato_proyectado']},
                search: '',
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
                return this.$store.dispatch('contratos/subcontrato/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/subcontrato/SET_SUBCONTRATOS', data.data);
                        this.$store.commit('contratos/subcontrato/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            getEstado(estado) {

                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrado'
                        }
                    case 1:
                        return {
                            color: '#0073b7',
                            descripcion: 'Estimado Parcial'
                        }
                    case 2:
                        return {
                            color: '#00a65a',
                            descripcion: 'Estimado Total'
                        }
                    default:
                        return {
                            color: '#d2d6de',
                            descripcion: 'Desconocido'
                        }
                }
            }
        },
        computed: {
            subcontratos(){
                return this.$store.getters['contratos/subcontrato/subcontratos'];
            },
            meta(){
                return this.$store.getters['contratos/subcontrato/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            subcontratos: {
                handler(subcontratos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = subcontratos.map((subcontrato, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: subcontrato.numero_folio_format,
                        referencia_cp: subcontrato.contrato_proyectado.referencia ,
                        numero_folio_cp: subcontrato.contrato_proyectado.numero_folio_format,
                        observaciones: subcontrato.observaciones,
                        referencia: subcontrato.referencia,
                        fecha: subcontrato.fecha_format,
                        contratista: subcontrato.empresa,
                        estado: this.getEstado(subcontrato.estado),
                        monto: subcontrato.monto_format,
                        impuesto:subcontrato.impuesto_format,
                        subtotal: subcontrato.subtotal_format,
                        buttons: $.extend({}, {
                            show: true,
                            id: subcontrato.id,
                            transaccion: {id:subcontrato.id, tipo:51},
                            eliminar: (self.$root.can('eliminar_subcontrato') && subcontrato.estado == 0) ? true: false
                        })
                    }));
                },
                deep: true
            },
            meta: {
                handler (meta) {

                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler (query) {
                    this.paginate()
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


<style>
    .money
    {
        text-align: right;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>

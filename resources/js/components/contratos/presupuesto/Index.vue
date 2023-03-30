<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_presupuesto_contratista')" class="btn btn-app pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
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
    export default {
        name: "presupuesto-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', tdClass: 'folio', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default },
                    { title: 'Contrato Proyectado', tdClass: 'folio', field: 'numero_folio_cp', thComp: require('../../globals/th-Filter').default},
                    { title: 'Referencia Contrato Proyectado', field: 'referencia_cp', sortable: false, thComp: require('../../globals/th-Filter').default },
                    { title: 'Contratista', field: 'contratista', sortable: false, thComp: require('../../globals/th-Filter').default },
                    { title: 'Monto', field: 'monto', tdClass: ['th_money', 'text-right'], sortable: true, },
                    { title: 'Estado', field: 'estado', sortable: false, tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default, thComp: require('../../globals/th-Filter').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'DESC', include: ['contrato_proyectado', 'usuario', 'empresa']},
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
                return this.$store.dispatch('contratos/presupuesto/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/presupuesto/SET_PRESUPUESTOS', data.data);
                        this.$store.commit('contratos/presupuesto/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },

            create() {
                this.$router.push({name: 'presupuesto-selecciona-contrato-proyectado'});
            },
            getEstado(estado) {

                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#ff0000',
                            descripcion: 'Precios Pendientes'
                        }
                    case 1:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrada'
                        }
                    case 2:
                        return {
                            color: '#4f9b34',
                            descripcion: 'En Asignación'
                        }
                }
            },
        },
        computed: {
            presupuestos(){
                return this.$store.getters['contratos/presupuesto/presupuestos'];
            },
            meta(){
                return this.$store.getters['contratos/presupuesto/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            presupuestos: {
                handler(presupuestos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = presupuestos.map((presupuesto, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: presupuesto.numero_folio,
                        fecha: presupuesto.fecha_format,
                        contratista: presupuesto.empresa.razon_social,
                        referencia_cp: presupuesto.contrato_proyectado.referencia ,
                        monto: presupuesto.monto_format,
                        usuario: (presupuesto.usuario) ? presupuesto.usuario.nombre : '-',
                        estado: this.getEstado(presupuesto.estado),
                        numero_folio_cp: presupuesto.contrato_proyectado.numero_folio_format,
                        buttons: $.extend({}, {
                            id: presupuesto.id,
                            delete: self.$root.can('eliminar_presupuesto_contratista') && !presupuesto.asignada && !presupuesto.id_referente > 0 ? true : false,
                            edit: (!presupuesto.asignada && !presupuesto.id_referente > 0) ? true : false,
                            transaccion: {id:presupuesto.id, tipo:50, opcion:1},
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
    .folio
    {
        text-align: center;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>

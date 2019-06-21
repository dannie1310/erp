<template>
    <div class="row">
        <div class="col-md-12">
<!--            <fondo-garantia-create @created="paginate()"></fondo-garantia-create>-->
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header" style="display: none">

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive table-bordered">
                        <datatable v-bind="$data"/>
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
        name: "contrato-proyectado-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', sortable: false },
                    { title: 'Folio de Contrato Proyectado', field: 'subcontrato__numero_folio', thClass: 'th_folio', thComp: require('../../globals/th-Filter'), sortable: true },
                    { title: 'Referencia de Contrato Proyectado', field: 'subcontrato__monto', tdClass: 'money', thClass: 'th_money'},
                    { title: 'Fecha de Contrato Proyectado', field: 'subcontrato__fecha', thClass: 'th_fecha', sortable: true },
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {},
                cargando: false
            }
        },
        mounted() {
            this.query.include = 'subcontrato.empresa';
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contratos/contrato-proyectado/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/contrato-proyectado/SET_FONDOS_GARANTIA', data.data);
                        this.$store.commit('contratos/contrato-proyectado/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            fondosGarantia(){
                return this.$store.getters['contratos/contrato-proyectado/fondosGarantia'];
            },
            meta(){
                return this.$store.getters['contratos/contrato-proyectado/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }

        },
        watch: {
            fondosGarantia: {
                handler(fondosGarantia) {
                    let self = this
                    self.$data.data = []
                    fondosGarantia.forEach(function (fondoGarantia, i) {

                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            saldo: fondoGarantia.saldo_format,
                            empresa__razon_social: fondoGarantia.subcontrato.empresa.razon_social,
                            subcontrato__referencia: fondoGarantia.subcontrato.referencia,
                            subcontrato__numero_folio: fondoGarantia.subcontrato.numero_folio_format,
                            subcontrato__fecha: fondoGarantia.subcontrato.fecha_format,
                            subcontrato__monto: fondoGarantia.subcontrato.monto_format,
                            buttons: $.extend({}, {
                                show: self.$root.can('consultar_fondo_garantia') ? true : undefined,
                                ajustar_saldo: self.$root.can('ajustar_saldo_fondo_garantia') ? true : undefined,
                                nueva_soliciud_movimiento: self.$root.can('registrar_solicitud_movimiento_fondo_garantia') ? true : undefined,
                                id: fondoGarantia.id,
                                objFondoGarantia: fondoGarantia,
                            })
                        })
                    });
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
                handler () {
                    this.paginate()
                },
                deep: true
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        },
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
    .th_fecha, .th_folio
    {
        width: 110px;
        max-width: 110px;
        min-width: 110px;

    }
    .th_index
    {
        width: 15px;
        max-width: 20px;
        min-width: 10px;

    }
    th
    {
        text-align: center;
    }
</style>
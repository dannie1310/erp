<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <button @click="sincronizar_saldos" class="btn btn-warning float-right" :disabled="cargando || sincronizando">
                    <i class="fa fa-spin fa-spinner" v-if="sincronizando"></i>
                    <i class="fa fa-sync" v-else></i> Sincronizar Saldos Contpaq
                </button>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Index",
        data() {
            return {
                cargando: false,
                sincronizando : false,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Base de Datos Contpaq', field: 'base_datos', thClass: 'fecha_hora', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Nombre de Empresa Contpaq', field: 'nombre_empresa', thClass: 'th_c350', thComp: require('../../globals/th-Filter').default, sortable: false},
                    { title: 'Código de Cuenta', field: 'codigo_cuenta', thClass: 'fecha_hora', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Nombre de Cuenta', field: 'nombre_cuenta', thClass: 'th_c350', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Saldo de Cuenta', field: 'saldo_cuenta', tdClass:'td_money_input', sortable: true,  tdComp: require('./partials/RouterSaldo').default},
                    { title: 'Tipo de Cuenta', field: 'tipo', thClass: 'fecha_hora', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Acciones', field: '', sortable: true},
                ],
                data: [],
                total: 0,
                query: {scope:[], include:[], sort: 'id', order: 'desc'},
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
            sincronizar_saldos() {
                this.sincronizando = true;
                return this.$store.dispatch('contabilidadGeneral/cuenta-saldo-negativo/sincronizar',
                    this.$data
                )
                .then(data => {
                    this.resultado = data;
                    this.paginate();

                }).finally(() => {
                    this.sincronizando = false;
                });
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/cuenta-saldo-negativo/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('contabilidadGeneral/cuenta-saldo-negativo/SET_CUENTAS', data.data);
                    this.$store.commit('contabilidadGeneral/cuenta-saldo-negativo/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
            },


        },
        computed: {
            cuentas(){
                return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/cuenta-saldo-negativo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    cuentas.forEach(function (cuenta, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            base_datos:cuenta.base_datos,
                            nombre_empresa:cuenta.nombre_empresa,
                            codigo_cuenta:cuenta.codigo_cuenta,
                            nombre_cuenta:cuenta.nombre_cuenta,
                            tipo:cuenta.tipo,
                            fecha_actualizacion:cuenta.fecha_actualizacion,
                            saldo_cuenta: $.extend({},{saldo: cuenta.saldo_cuenta, id: cuenta.id}),
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
            },
        }
    }
</script>

<style scoped>

</style>

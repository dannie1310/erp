<template>
    <span>
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <button @click="asociar"  class="btn btn-app pull-right" v-if="$root.can('asociacion_masiva_cuentas_contpaq_proveedores',1)">
                    <i class="fa fa-share-alt"></i> Asociar
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <span style="font-weight: bold;">{{currentEmpresa.Descripcion +' ('+currentEmpresa.AliasBDD+')'}}</span>
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
    </span>

</template>

<script>
export default {
    name: "AsociacionCuentaProveedor",
    props: [],
    data() {
        return {
            empresa : '',
            HeaderSettings: false,
            columns: [
                { title: '#', thClass: 'th_index', field: 'index', sortable: false },
                { title: 'Codigo', field: 'codigo', tdClass: 'td_c150', thClass: 'th_c150', thComp: require('../../globals/th-Filter').default,  sortable: true },
                { title: 'Nombre', field: 'nombre', thComp: require('../../globals/th-Filter').default, sortable: true },
                { title: 'RFC', field: 'rfc', tdClass: 'td_c150', sortable: true },
                { title: 'Razón Social', field: 'razon_social', sortable: true },
                { title: 'Acciones', field: 'buttons', tdClass: 'td_money_input',  thClass: 'th_money_input',  tdComp: require('../../contabilidad-general/asociacion-cuenta-proveedor/partials/ActionButtons.vue').default},
            ],
            data: [],
            total: 0,
            query: { sort: 'Codigo', order: 'asc', include:["cuenta_contpaq_proveedor_sat"]},
            search: '',
            cargando: false,
        }
    },
    mounted(){
    },
    methods: {
        paginate(){
            this.$Progress.start();
            this.query.id_empresa = this.currentEmpresa.Id;
            this.query.scope = 'cuentasPasivo:'+this.currentEmpresa.Id;
            this.cargando = true;
            this.$Progress.start();
            this.$store.commit('contabilidadGeneral/cuenta/SET_CUENTAS', []);
            return this.$store.dispatch('contabilidadGeneral/cuenta/paginate',
                {
                    params: this.query
                })
                .then(data => {
                    this.$store.commit('contabilidadGeneral/cuenta/SET_CUENTAS', data.data);
                    this.$store.commit('contabilidadGeneral/cuenta/SET_META', data.meta);
                }).finally(() => {
                    this.$Progress.finish();
                    this.cargando = false;
                });

        },
        asociar()
        {

            return this.$store.dispatch('contabilidadGeneral/cuenta/asociarProveedor',
                {id_empresa: this.currentEmpresa.Id}
            ).then((data) => {
                this.$store.commit("contabilidadGeneral/cuenta/SET_CUENTA",data)
            }).finally(() => {

            });

        }
    },
    computed: {
        cuentas(){
            return this.$store.getters['contabilidadGeneral/cuenta/cuentas'];
        },
        meta(){
            return this.$store.getters['contabilidadGeneral/cuenta/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        },
        currentEmpresa(){
            return this.$store.getters['auth/currentEmpresa']
        },
    },
    watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = cuentas.map((cuenta, i) => ({
                        index: (i + 1) + self.query.offset,
                        codigo: cuenta.cuenta,
                        nombre: cuenta.descripcion,
                        rfc: cuenta.cuenta_contpaq_proveedor_sat?cuenta.cuenta_contpaq_proveedor_sat.rfc_proveedor_sat:'',
                        razon_social: cuenta.cuenta_contpaq_proveedor_sat?cuenta.cuenta_contpaq_proveedor_sat.razon_social_proveedor_sat:'',
                        buttons: $.extend({}, {
                            id_cuenta: cuenta.id,
                            id_empresa: this.currentEmpresa.Id,
                            nombre: cuenta.descripcion,
                            eliminar: cuenta.cuenta_contpaq_proveedor_sat?true:false
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
        },
}
</script>

<style>

</style>

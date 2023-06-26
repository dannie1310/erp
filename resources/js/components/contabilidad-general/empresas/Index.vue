<template>
    <span>
        <div class="row">
            <div class="col-12">
                <button @click="sincronizar"  class="btn btn-app btn-secondary float-right" title="Sincronizar Empresas con Contpaq" :disabled="sincronizando">
                    <i class="fa fa-spin fa-spinner" v-if="sincronizando"></i>
                    <i class="fa fa-sync" v-else></i>
                    Sincronizar con Contpaq
                </button>
                <button @click="actualizaAccesoMetadatos"  class="btn btn-app btn-secondary float-right" title="Sincronizar Empresas con Contpaq" :disabled="verificando">
                    <i class="fa fa-spin fa-spinner" v-if="verificando"></i>
                    <i class="fa fa-network-wired" v-else></i>
                    Verificar Acceso BD
                </button>
            </div>
        </div>
        <div class="row">
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
    </span>

</template>

<script>
    export default {
        name: "lista-empresas-index",
        components: {},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Nombre', field: 'nombre', sortable: true },
                    { title: 'Alias', field: 'alias', sortable: true },
                    { title: 'Con Acceso', field: 'acceso', tdClass:"center", sortable: false, tdComp: require('./partials/EstadoAcceso.vue').default },
                    { title: 'Visible', field: 'visible', sortable: true, tdComp: require('./partials/SwitchVisible').default },
                    { title: 'Editable', field: 'editable', sortable: true, tdComp: require('./partials/SwitchEditable').default },
                    { title: 'Histórica', field: 'historica', sortable: true, tdComp: require('./partials/SwitchHistorica').default },
                    { title: 'Consolidadora ', field: 'consolidadora', sortable: true, tdComp: require('./partials/SwitchConsolidadora').default },
                    { title: 'Desarrollo ', field: 'desarrollo', sortable: true, tdComp: require('./partials/SwitchDesarrollo').default },
                    { title: 'Pólizas-CFDI ', field: 'poliza_cfdi', sortable: true, tdComp: require('./partials/SwitchPolizaCFDI').default },
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                cargando: false,
                sincronizando : false,
                verificando : false,
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
                return this.$store.dispatch('contabilidadGeneral/empresa/paginate', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidadGeneral/empresa/SET_EMPRESAS', data.data);
                        this.$store.commit('contabilidadGeneral/empresa/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
            },
            sincronizar(){
                this.sincronizando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/sincronizar',
                    {
                        params: this.query,
                    })
                    .then(data => {
                        this.$emit('success');
                    }).finally(() => {
                        this.sincronizando = false;
                    });
            },
            actualizaAccesoMetadatos(){
                this.sincronizando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/actualizaAccesoMetadatos',
                    {

                    })
                    .then(data => {
                        this.$emit('success');
                    }).finally(() => {
                        this.sincronizando = false;
                    });
            },
        },

        computed: {
            empresas(){
                return this.$store.getters['contabilidadGeneral/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/empresa/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },

        watch: {
            empresas: {
                handler(empresas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = empresas.map((empresa, i) => ({
                        index: (i + 1) + self.query.offset,
                        nombre: empresa.nombre,
                        alias: empresa.alias,
                        acceso: empresa.acceso,
                        /*visible: empresa.visible == 1?'SI':'NO',*/
                        visible: $.extend({},{id: empresa.id, visible: empresa.visible}),
                        /*editable: empresa.editable == 1?'SI':'NO',*/
                        editable: $.extend({},{id: empresa.id, editable: empresa.editable}),
                        /*historica: empresa.historica == 1?'SI':'NO',*/
                        historica: $.extend({},{id: empresa.id, historica: empresa.historica}),
                        /*consolidadora: empresa.consolidadora == 1?'SI':'NO',*/
                        consolidadora: $.extend({},{id: empresa.id, consolidadora: empresa.consolidadora}),
                        /*desarrollo: empresa.desarrollo == 1 ? 'SI' : 'NO',*/
                        desarrollo: $.extend({},{id: empresa.id, desarrollo: empresa.desarrollo}),
                        poliza_cfdi: $.extend({},{id: empresa.id, poliza_cfdi: empresa.poliza_cfdi}),
                        buttons: $.extend({}, {
                            edit: self.$root.can('configurar_visibilidad_empresa_ctpq', true) || self.$root.can('configurar_editabilidad_empresa_ctpq', true) || self.$root.can('configurar_tipo_empresa_ctpq', true) ? true : false,
                            empresa: empresa,
                            consolidada: empresa.consolidada
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
                handler () {
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

</style>

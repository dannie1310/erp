<template>
    <div class="row">
        <div class="col-12">
            <router-link :to="{name: 'documento-recurso-create'}" v-if="$root.can('registrar_documento_recursos',true)" class="btn btn-app btn-info float-right" :disabled="cargando" @created="paginate()">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
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
    name: "documento-index",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                { title: 'Serie', field: 'idserie', thClass: 'th_c80', sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Proveedor', field: 'IdProveedor', thClass: 'th_c200', sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Tipo Documento', field: 'idtipodocto', thClass: 'th_c150',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Fecha', thClass: 'th_c100', field: 'Fecha', sortable: true,thComp: require('../../globals/th-Date').default},
                { title: 'Folio', field: 'foliodocto',sortable: true,thClass: 'th_c100',  thComp: require('../../globals/th-Filter').default},
                { title: 'Concepto', field: 'concepto',sortable: true,thComp: require('../../globals/th-Filter').default},
                { title: 'Total', field: 'total', tdClass: 'right th_c220', sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Moneda', field: 'idmoneda',thClass: 'th_c150',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Estatus', field: 'estatus', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                { title: 'Acciones', field: 'buttons',thClass: 'th_c150', tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: { scope: ['porTipo:6'],sort: 'IdDocto', order: 'desc'},
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
        paginate() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/documento/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('controlRecursos/documento/SET_DOCUMENTOS', data.data);
                    this.$store.commit('controlRecursos/documento/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        getEstado(estado, color) {
            return {
                color: color,
                descripcion: estado
            }
        },
    },
    computed: {
        documentos(){
            return this.$store.getters['controlRecursos/documento/documentos'];
        },
        meta(){
            return this.$store.getters['controlRecursos/documento/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        documentos: {
            handler(documentos) {
                let self = this
                self.$data.data = []
                self.$data.data = documentos.map((documento, i) => ({
                    index: (i + 1) + self.query.offset,
                    Fecha: documento.fecha_format,
                    IdProveedor: documento.proveedor_descripcion,
                    concepto: documento.concepto,
                    foliodocto: documento.folio_format,
                    total: documento.total_format,
                    idmoneda: documento.moneda,
                    idserie: documento.serie,
                    idtipodocto: documento.tipo_documento,
                    estatus: this.getEstado(documento.estado_descripcion, documento.estado_color),
                    buttons: $.extend({}, {
                        id: documento.id,
                        edit: self.$root.can('editar_documento_recursos', true) ? true : false,
                        delete: self.$root.can('eliminar_documento_recursos', true) ? true : false,
                    })
                }));
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

<style>

</style>

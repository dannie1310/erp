<template>
    <div class="row">
        <div class="col-12">
            <registro-documento v-bind:cargando="cargando"></registro-documento>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'"  />
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
    import RegistroDocumento from "./partials/RegistroDocumento.vue";

    export default {
    name: "documento-index",
        components: {RegistroDocumento},
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                { title: 'Serie', field: 'idserie', thClass: 'th_c60', sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Proveedor', field: 'IdProveedor', thClass: 'th_c200', sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Tipo Documento', field: 'idtipodocto', thClass: 'th_c150',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Fecha', thClass: 'th_c80', field: 'Fecha', sortable: true,thComp: require('../../globals/th-Date').default},
                { title: 'Folio', field: 'foliodocto',sortable: true,thClass: 'th_c80',  thComp: require('../../globals/th-Filter').default},
                { title: 'Concepto', field: 'concepto',sortable: true,thComp: require('../../globals/th-Filter').default},
                { title: 'Total', field: 'total', thClass :'th_c220', tdClass: 'right', sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'Moneda', field: 'idmoneda',thClass: 'th_c100',sortable: true, thComp: require('../../globals/th-Filter').default},
                { title: 'UUID', field: 'consulta_uuid',thClass: 'th_c100',sortable: true, thComp: require('../../globals/th-Filter').default, tdComp: require('../../fiscal/cfd/cfd-sat/CFDI').default},
                { title: 'Estatus', field: 'estatus', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                { title: 'Acciones', field: 'buttons',thClass: 'th_c100', tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: { scope: ['porTipo:1,6'], sort: 'IdDocto', order: 'desc'},
            estado: "",
            cargando: false,
        }
    },
    mounted() {

    },
    methods: {
        paginate() {
            this.cargando = true;
            this.$Progress.start();
            return this.$store.dispatch('controlRecursos/documento/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('controlRecursos/documento/SET_DOCUMENTOS', data.data);
                    this.$store.commit('controlRecursos/documento/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                    this.$Progress.finish();
                })
        },
        getEstado(estado, color, solicitado, segmento) {
            if(solicitado)
            {
                return {
                    color: '#2369C8',
                    descripcion: 'Solicitado'
                }
            }else if (solicitado == false && segmento)
            {
                return {
                    color: '#CFB9B4',
                    descripcion: 'Segmentos Asignados'
                }
            }else {
                return {
                    color: color,
                    descripcion: estado
                }
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
                    estatus: this.getEstado(documento.estado_descripcion, documento.estado_color, documento.solicitado, documento.con_segmento),
                    buttons: $.extend({}, {
                        con_cfdi : documento.cfdi ? true : false,
                        id : documento.id,
                        edit : self.$root.can('editar_documento_recursos', true) && (documento.estado == 5 ||  documento.estado == 1) && documento.solicitado == false && documento.con_segmento == false ? true : false,
                        delete : self.$root.can('eliminar_documento_recursos', true) && (documento.estado != 7 && documento.estado != 2) && documento.solicitado == false && documento.con_segmento == false  ? true : false,
                    }),
                    consulta_uuid : $.extend({}, {
                        uuid: documento.cfdi ? documento.cfdi.uuid : '',
                        corto: true,
                        txt:  documento.uuid,
                        id: documento.cfdi ? documento.cfdi.id : null,
                        cancelado: documento.cfdi ? documento.cfdi.id : null,
                    }),
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

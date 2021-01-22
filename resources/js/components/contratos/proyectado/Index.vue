<template>
    <div class="row">
        <div class="col-12">
            <button @click="registrar" v-if="$root.can('registrar_contrato_proyectado')" class="btn btn-app pull-right" :disabled="cargando">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i> Registrar
            </button>
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
        name: "contrato-proyectado-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Área Subcontratante', field: 'id_area_subcontratante',thComp: require('../../globals/th-Filter').default, sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../globals/th-Filter').default },
                    { title: 'Referencia', field: 'referencia', sortable: true, thComp: require('../../globals/th-Filter').default },
                    { title: 'Fecha', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date').default },
                    { title: 'Acciones', field: 'buttons', thClass: 'th_c150',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include:['areasSubcontratantes','relaciones']},
                search: '',
                cargando: false
            }
        },
        mounted() {
            this.query.sort = 'numero_folio';
            this.query.order = 'DESC';
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
                        this.$store.commit('contratos/contrato-proyectado/SET_CONTRATOS', data.data);
                        this.$store.commit('contratos/contrato-proyectado/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            registrar(){
                this.$router.push({name: 'proyectado-create'});
            },
        },
        computed: {
            contratosProyectados(){
                return this.$store.getters['contratos/contrato-proyectado/contratos'];
            },
            meta(){
                return this.$store.getters['contratos/contrato-proyectado/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            contratosProyectados: {
                handler(contratosProyectados) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = contratosProyectados.map((contratoProyectado, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: contratoProyectado.numero_folio_format,
                        id_area_subcontratante:contratoProyectado.areasSubcontratantes.data.length ? contratoProyectado.areasSubcontratantes.data[0].descripcion : 'Sin Área Subcontratante Asignada',
                        fecha: contratoProyectado.fecha,
                        referencia: contratoProyectado.referencia,
                        buttons: $.extend({}, {
                            cambiaAreaSubcontratante: (this.$root.can('modificar_area_subcontratante_cp') || true) ? true : undefined,
                            id: contratoProyectado.id,
                            numero_folio: `# ${contratoProyectado.numeroFolio}`,
                            fecha: contratoProyectado.fecha,
                            referencia: contratoProyectado.referencia,
                            area:contratoProyectado.areasSubcontratantes.data[0],
                            contratoProyectado: contratoProyectado,
                            delete : (this.$root.can('eliminar_contrato_proyectado')) ? true : false,
                            show : this.$root.can('consultar_contrato_proyectado') ? true : false,
                            edit : this.$root.can('editar_contrato_proyectado') ? true : false,
                            transaccion: {id:contratoProyectado.id, tipo:49, opcion:1},
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

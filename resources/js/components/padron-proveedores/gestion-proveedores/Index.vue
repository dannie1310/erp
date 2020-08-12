<template>
    <span>
        <div class="row">
            <div class="col-12">
                <button @click="create" v-if="$root.can('iniciar_expediente_proveedor',true)" class="btn btn-app float-right" :disabled="cargando">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-folder-plus" v-else></i>
                    Iniciar Expediente
                </button>
            </div>
        </div>
        <div class="row">
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
    </span>
</template>

<script>
    export default {
        name: "padron-proveedores-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:'th_index', sortable: false },
                    { title: 'Razón Social', field: 'razon_social', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'RFC', field: 'rfc', thClass:'th_rfc', tdClass:'center', sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Usuario Inicio',  field: 'usuario_inicio', thClass:'th_c150', sortable: false, thComp: require('../../globals/th-Filter').default},
                    { title: 'Estado Expediente',  field: 'estado_expediente', thClass:'th_c150', sortable: false, thComp: require('../../globals/th-Filter').default},
                    { title: 'Avance Expediente', field: 'avance_expediente', tdClass:'center', thClass:'th_c150', sortable: false, thComp: require('../../globals/th-Filter').default},
                    { title: 'Acciones', field: 'buttons', thClass:'th_c100', tdClass:'center',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: { scope:['proveedores'], include: 'empresa', sort: 'razon_social', order: 'desc'},
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
                return this.$store.dispatch('padronProveedores/empresa/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('padronProveedores/empresa/SET_EMPRESAS', data.data);
                        this.$store.commit('padronProveedores/empresa/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'proveedores-iniciar-expediente'});
            },
        },
        computed: {
            entradas(){
                return this.$store.getters['padronProveedores/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['padronProveedores/empresa/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            entradas: {
                handler(entradas) {
                    let self = this;
                    self.$data.data = [];
                    entradas.forEach(function (entrada, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            razon_social: entrada.razon_social,
                            rfc: entrada.rfc,
                            estado_expediente: entrada.estado_expediente,
                            avance_expediente: entrada.porcentaje_avance_expediente +'% ('+ entrada.avance_expediente+')',
                            usuario_inicio: entrada.usuario_inicio,
                            buttons: $.extend({}, {
                                show: true,
                                id: entrada.id,
                                estado: entrada.estado,
                                pagina: self.query.offset,
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

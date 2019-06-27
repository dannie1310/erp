<template>
    <span>
        <div class="card" id="Auditoria">
            <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="form-group error-content" style=" width: 40%;">
                                    <label for="id_area">Área Subcontratante</label>
                                    <select
                                            type="text"
                                            name="id_area"
                                            data-vv-as="Area"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="id_area"
                                            v-model="id_area"
                                            :class="{'is-invalid': errors.has('id_area')}"
                                    >
                                    <option>--- Área Subcontratante sin Asignar ---</option>
                                    <option>--- Área Subcontratante sin Asignar ---</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_area')">{{ errors.first('id_area') }}</div>
                                </div>
                                <div class="row" >
                                    <div class="table-responsive">
                                        <datatable v-bind="$data" />
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                <!-- /.col -->
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "auditoria-index",
        data() {
            return {
                form: {
                    obra_id: '',
                    proyeco_id: []
                },
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'ID', field: 'numero_folio', sortable: true },
                    { title: 'Descripcion', field: 'descripcion', sortable: true },
                    { title: 'Nombre', field: 'nombre', sortable: true },
                    { title: 'Name', field: 'name', sortable: false },
                    { title: 'Sistema', field: 'sistema_name', sortable: false },
                    // { title: 'Usuario', field: 'referencia', sortable: false },
                    // { title: 'Nombre de Usuario', field: 'referencia', sortable: false },
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                cargando: false
            }
        },
        mounted() {
            this.getObras();

            this.query.sort = 'id';
            this.query.order = 'DESC';
            // this.query.scope = 'PorUsuario';
            this.$Progress.start();
            // this.paginate()
            //     .finally(() => {
            //         this.$Progress.finish();
            //     })
        },
        methods: {
            getObras() {
                return this.$store.dispatch('auditoria/asignacion-permisos/obras')
                    .then(data => {
                        this.permisos_disponibles = data.data.sort((a, b) => (a.display_name > b.display_name) ? 1 : -1);
                    })
            },
            // paginate() {
            //     this.cargando = true;
            //     return this.$store.dispatch('auditoria/asignacion-permisos/paginate', {
            //         params: this.query
            //     })
            //         .then(data => {
            //             this.$store.commit('auditoria/asignacion-permisos/SET_PERMISOS', data.data);
            //             this.$store.commit('auditoria/asignacion-permisos/SET_META', data.meta);
            //         })
            //         .finally(() => {
            //             this.cargando = false;
            //         })
            // },
        },
        computed: {
            obras_agrupadas() {
                if (this.obras)
                    return _.groupBy(this.obras, 'base_datos');
                else
                    return [];
            },
            Permisos(){
                return this.$store.getters['auditoria/asignacion-permisos/Permisos'];
            },
            meta(){
                return this.$store.getters['auditoria/asignacion-permisos/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            'form.obra_id'(id) {
                this.$validator.reset()
                if (id) {
                    this.cargando = true;
                    this.paginate(id)
                        .finally(() => {
                            this.cargando = false;
                        });
                }
            },
            Permisos: {
                handler(Permisos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = Permisos.map((permiso, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: `# ${permiso.id}`,
                        // area:contratoProyectado.areasSubcontratantes.data.length ? contratoProyectado.areasSubcontratantes.data[0].descripcion : 'Sin Área Subcontratante Asignada',
                        // fecha: contratoProyectado.fecha,
                        // referencia: contratoProyectado.referencia,
                        descripcion: permiso.base_datos,
                        nombre: permiso.base_datos,
                        name: permiso.base_datos,
                        sistema_name: permiso.base_datos,

                        // buttons: $.extend({}, {
                            // cambiaAreaSubcontratante: (this.$root.can('aprobar_estimacion_subcontrato')) ? true : undefined,
                            // id: contratoProyectado.id,
                            // numero_folio: `# ${contratoProyectado.numeroFolio}`,
                            // fecha: contratoProyectado.fecha,
                            // referencia: contratoProyectado.referencia,
                            // area:contratoProyectado.areasSubcontratantes.data[0],
                            // permiso: permiso
                        // })
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
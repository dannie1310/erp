<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="form-group row">
                    <label for="user_id" class="col-lg-2 col-form-label">Buscar Usuario</label>
                    <div class="col-lg-6">
                        <usuario-select
                                name="user_id"
                                id="user_id"
                                data-vv-as="Usuario"
                                v-validate="{required: true, integer: true}"
                                v-model="form.user_id"
                                :error="errors.has('user_id')">
                        </usuario-select>
                        <div class="error-label" v-show="errors.has('user_id')">{{ errors.first('user_id') }}</div>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-12">
                         <button @click="descargaListado" v-if="form.user_id" class="btn btn-app btn-info pull-right" :disabled="cargando_excel">
                            <i class="fa fa-spin fa-spinner" v-if="cargando_excel"></i>
                            <i class="fa fa-download" v-else></i>
                            Descargar excel
                        </button>
                     </div>
                </div>
            </div>
        </div>

        <div class="card" id="Auditoria">
            <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                 <div class="form-group error-content" style=" width: 100%;">
                                     <form role="form">
                                        <div class="modal-body">
                                           <div class="col-md-6">

                                           </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="row">
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
    import UsuarioSelect from "../../igh/usuario/Select";
    export default {
        name: "por-usuario-index",
        components:{UsuarioSelect},
        data() {
            return {
                form: {
                    user_id: 0,
                },
                usuario_seleccionado: '',
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Permiso', field: 'permiso', sortable: false, thComp: require('../../globals/th-Filter')},
                    { title: 'Rol', field: 'rol', sortable: false, thComp: require('../../globals/th-Filter') },
                    { title: 'Sistema', field: 'sistema', sortable: false , thComp: require('../../globals/th-Filter')},
                    { title: 'Proyecto', field: 'proyecto', sortable: false, thComp: require('../../globals/th-Filter') },
                    { title: 'Obra', field: 'obra', sortable: false , thComp: require('../../globals/th-Filter') },
                    { title: 'Usuario que Asignó', field: 'asigno', sortable: false},
                    { title: 'Fecha de Asignación', field: 'fecha_asigno', sortable: false }
                ],
                data: [],
                total: 0,
                query: {},
                query2: {},
                cargando: false,
                cargando_excel: false,
            }
        },
        mounted() {
            this.$Progress.start();
            this.query.sort = 'permiso';
            this.query.order = 'desc';
        },
        methods: {
            descargaListado(){
                this.cargando_excel = true;
                this.query2.excel = true;
                return this.$store.dispatch('seguridad/permiso/descargaListadoUsuario', {
                    params: this.query2,
                    id: this.form.user_id
                })
                    .then(() => {
                        this.$emit('success')
                    }).finally(() => {
                        this.cargando_excel = false;
                    })
            },
            paginate(user_id) {
                this.cargando_excel = true;
                this.cargando = true;
                return this.$store.dispatch('seguridad/permiso/porUsuarioAuditoria', {
                    params: this.query,
                    id: user_id
                })
                    .then(data => {
                        this.$store.commit('seguridad/permiso/SET_PERMISOS',data.data);
                        this.$store.commit('seguridad/permiso/SET_META', {
                            "pagination": {
                                "total": data.total,
                                "count": data.to - data.from + 1,
                                "per_page": data.per_page,
                                "current_page": data.current_page,
                                "total_pages": data.last_page,
                            }
                        });
                    }).finally(() => {
                        this.cargando = false;
                        this.cargando_excel = false;
                    })
            }
        },
        computed: {
           permisos() {
                return this.$store.getters['seguridad/permiso/permisos'];
            },

            meta(){
                return this.$store.getters['seguridad/permiso/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            },

        },
        watch: {
            'form.user_id'(id) {
                this.$validator.reset()
                if (id) {
                    this.cargando = true;
                    this.paginate(id)
                        .finally(() => {
                            this.cargando = false;
                        });
                }else if(id === null){
                    this.cargando = true;
                    this.paginate(0)
                        .finally(() => {
                            this.cargando = false;
                        });
                }
            },
            permisos: {
                handler(perms) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = perms.map((permiso, i) => ({
                        index: (i + 1) + self.query.offset,
                        permiso: permiso.permiso,
                        rol: permiso.rol,
                        sistema: permiso.sistema,
                        usuario: permiso.usuario,
                        nombre: permiso.nombre_completo_usuario,
                        asigno: permiso.usuario_asigno,
                        fecha_asigno: permiso.fecha_hora_asignacion,
                        proyecto: permiso.base_datos,
                        obra: permiso.nombre_obra,
                        id: permiso.idusuario

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
                    this.cargando = true;
                    this.paginate(this.form.user_id)
                        .finally(() => {
                            this.cargando = false;
                        });
                },
                deep: true
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
<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="user_id" class="col-lg-4 col-form-label">Buscar por Empleado</label>
                            <div class="col-lg-8">
                                <usuario-select
                                        name="user_id"
                                        id="user_id"
                                        data-vv-as="Usuario"
                                        v-validate="{ integer: true}"
                                        v-model="user_id"
                                        :error="errors.has('user_id')"
                                >
                                </usuario-select>
                                <div class="error-label" v-show="errors.has('user_id')">{{ errors.first('user_id') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="tipoActivo">Tipo Activo:</label>
                            <model-list-select
                                    :disabled="cargando"
                                    name="tipoActivo"
                                    v-model="tipoActivo"
                                    option-value="idGrupo"
                                    option-text="Descripcion"
                                    :list="tiposActivo"
                                    :placeholder="!cargando?'Seleccionar o buscar activo por descripcion':'Cargando...'"
                                    :isError="errors.has(`tipoActivo`)">
                            </model-list-select>
                            <div class="invalid-feedback" v-show="errors.has('tipoActivo')">{{ errors.first('tipoActivo') }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary" v-on:click="getResguardos"><i class="fa fa-angle-left"></i>Consultar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12" v-if="activos">
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
import UsuarioSelect from "../../igh/usuario/Select.vue";
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "camion-index",
    components: {UsuarioSelect, ModelListSelect},
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                { title: 'Nombre Empleado', field: 'empleado', sortable: true},
                { title: 'Ubicación', field: 'ubicacion', sortable: true},
                { title: 'Tipode Activo', field: 'tipo', sortable: true},
                { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
            ],
            data: [],
            total: 0,
            query: {scope:'', sort: '', order: ''},
            estado: "",
            cargando: false,
            tiposActivo:[],
            tipoActivo:'',
            user_id:'',
        }
    },
    mounted() {
        this.$Progress.start();
        this.getLista()
        .finally(() => {
            this.$Progress.finish();
        })
    },
    methods: {
        getResguardos() {
            this.cargando = true;
            return this.$store.dispatch('activo-fijo/resguardo/getResguardos', { 
                params: {
                    ubicacion: '',
                    empleado: this.user_id,
                    tipo: this.tipoActivo, 
                }})
                .then(data => {
                    this.$store.commit('activo-fijo/resguardo/SET_ACTIVOS', data.data);
                    this.$store.commit('activo-fijo/resguardo/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        getLista() {
            this.cargando = true;
            return this.$store.dispatch('activo-fijo/resguardo/getLista', {
                    params: {sort: 'Descripcion', order: 'ASC'}
                }).then(data => {
                    this.tiposActivo = data;
                }).finally(() => {
                    this.cargando = false;
                })
        },
    },
    computed: {
        activos(){
            return this.$store.getters['activo-fijo/resguardo/activos'];
        },
        meta(){
            return this.$store.getters['activo-fijo/resguardo/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
        activos: {
            handler(activos) {
                let self = this
                self.$data.data = []
                self.$data.data = activos.map((activo, i) => ({
                    index: (i + 1) + self.query.offset,
                    empleado: activo.nombreEmpleado,
                    ubicacion: activo.ubicacion,
                    tipo: activo.grupoEquipoNombre,
                    buttons: $.extend({}, {
                        id: activo.id,
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
        // query: {
        //     handler(query) {
        //         this.paginate(query)
        //     },
        //     deep: true
        // },
        // search(val) {
        //     if (this.timer) {
        //         clearTimeout(this.timer);
        //         this.timer = null;
        //     }
        //     this.timer = setTimeout(() => {
        //         this.query.search = val;
        //         this.query.offset = 0;
        //         this.paginate();

        //     }, 500);
        // },
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
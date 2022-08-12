<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group error-content">
                                <label for="ubicaciones">Ubicaciones:</label>
                                <model-list-select
                                        :disabled="cargando"
                                        name="ubicaciones"
                                        v-model="ubicacion"
                                        option-value="idubicacion"
                                        option-text="Ubicacion"
                                        :list="usuariosUbicaciones"
                                        :placeholder="!cargando?'Seleccionar o buscar por ubicación':'Cargando...'"
                                        :isError="errors.has(`ubicaciones`)">
                                </model-list-select>
                                <div class="invalid-feedback" v-show="errors.has('ubicaciones')">{{ errors.first('ubicaciones') }}</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group row">
                                <label for="user_id">Empleado</label>
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
                        <div class="col">
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
                </div>
                <div class="card-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-primary" v-on:click="getResguardos" :disabled="cargando || buscando">
                            <span v-if="!buscando">
                                <i class="fa fa-search"></i>Consultar
                            </span>
                            <span v-else>
                                <i class="fa fa-spin fa-spinner" ></i>Buscando...
                            </span>
                            
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12" v-if="activos.length > 0">
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
                { title: 'Tipo de Activo', field: 'tipo', sortable: true},
                { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
            ],
            data: [],
            total: 0,
            query: {scope:'', sort: '', order: ''},
            estado: "",
            buscando: false,
            cargando: false,
            tiposActivo:[],
            usuariosUbicaciones:[],
            tipoActivo:'',
            ubicacion:'',
            user_id:'',
        }
    },
    mounted() {
        this.cargando = true;
        this.$Progress.start();
        this.getLista();
        this.getListaUbicaciones()
        .finally(() => {
            this.$Progress.finish();
        })
    },
    methods: {
        getResguardos() {
            this.buscando = true;
            return this.$store.dispatch('activo-fijo/resguardo/getResguardos', { 
                params: {
                    ubicacion: this.ubicacion,
                    empleado: this.user_id,
                    tipo: this.tipoActivo, 
                }})
                .then(data => {
                    if(data.data.length == 0){
                        swal('Atención', 'No hay resguardos registrados con los datos ingresados, intente con otros', 'warning');
                    }else{
                        this.$store.commit('activo-fijo/resguardo/SET_ACTIVOS', data.data);
                        this.$store.commit('activo-fijo/resguardo/SET_META', data.meta);
                    }
                    
                })
                .finally(() => {
                    this.buscando = false;
                })
        },
        getLista() {
            return this.$store.dispatch('activo-fijo/resguardo/getLista', {
                    params: {sort: 'Descripcion', order: 'ASC'}
                }).then(data => {
                    this.tiposActivo = data;
                })
        },
        getListaUbicaciones() {
            return this.$store.dispatch('activo-fijo/usuario-ubicacion/getListaUbicaciones', {
                    params: {scope: 'porUsuario'}
                }).then(data => {
                    this.usuariosUbicaciones = data;
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
                    index: (i + 1) ,
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
<template>
    <div class="card" id="configuracion-conceptos" v-if="true">
        <div class="card-header">
            <h3 class="card-title">Configuración de Conceptos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <h6 id="configuracion_conceptos_nodo">Configuración de Tipo de Nodo</h6>
            <div class="form-group row" v-if="conceptos.length > 1">
                <label for="nodo_proyecto" class="col-sm-2 col-form-label">Proyecto</label>
                <div class="col-sm-10">
                    <select class="form-control" id="nodo_proyecto" v-model="nodo_proyecto" 
                            v-validate="{integer: true}"
                            name="nodo_proyecto"
                            data-vv-as="nodo_proyecto"
                            :class="{'is-invalid': errors.has('nodo_proyecto')}"
                    >
                        <option value v-if="conceptos.length > 1">-- SELECCIONE --</option>
                        <option v-for="concepto in conceptos" :value="concepto.id">{{ concepto.descripcion }}</option>
                    </select>
                    <div class="invalid-feedback" v-show="errors.has('nodo_proyecto')">{{ errors.first('nodo_proyecto') }}</div>
                </div>
            </div>
            <div class="form-group row" v-if="conceptos.length === 1">
                <label class="col-sm-2">Proyecto</label>
                <div class="col-sm-10">
                    {{conceptos[0].descripcion}}
                </div>
            </div>
            <fieldset class="form-group" v-if="pendientes.length > 0 || asignados.length > 0">
                <div class="row" v-for="(asignado, j) in asignados">
                    <legend class="col-form-label col-sm-3 pt-0"><b>{{asignado.ctg_tipo_nodo.descripcion}}</b></legend>
                    <div class="col-sm-2" >
                        <b>Asignado</b>
                    </div>
                    <div class="col-sm-4" >
                        <b>{{asignado.descripcion_padre}} -> {{asignado.descripcion_nodo_asignado}}</b>
                    </div>
                    <div>
                        <button type="submit" @click="eliminar(j)" class="btn btn-outline-danger float-right" tittle="Eliminar Asignación"  v-if="$root.can('eliminar_asignacion_nodo_tipo')">
                        <i class="fa fa-trash"></i>
                    </button>
                    </div>
                </div>
                <hr>
                <div class="row" v-for="(pendiente, i) in pendientes">
                    <legend class="col-form-label col-sm-3 pt-0"><b>{{pendiente.descripcion}}</b></legend>
                    <div class="col-sm-2" >
                        <b>Pendiente</b>
                    </div>
                    <div class="col-sm-4"  v-if="$root.can('registrar_asignacion_nodo_tipo')">
                    <concepto-select
                            name="id_concepto"
                            data-vv-as="Concepto"
                            id="id_concepto"
                            v-model="pendiente.id_concepto"
                            :error="errors.has('id_concepto')"
                            ref="conceptoSelect"
                            :disableBranchNodes="false"
                            :nivel_id="nodo_proyecto"
                            :placeholder="placehold"
                        ></concepto-select>
                    </div>
                    <button type="submit" @click="asignar(i)" class="btn btn-outline-primary float-right" v-if="$root.can('registrar_asignacion_nodo_tipo')" :disabled="pendiente.id_concepto === undefined" tittle="Asignar">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
                
            </fieldset>
            <div class="form-group row">
                <div class="col">
                    <!-- <button type="submit" @click="validate" class="btn btn-outline-primary float-right" >
                        <i class="fa fa-save"></i>
                    </button> -->
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
    import ConceptoSelect from "../../cadeco/concepto/SelectHijo";
    export default {
        name: "configuracion-conceptos",
        components: {ConceptoSelect},
        props: ['datosConcepto'],
        data() {
            return {
                placehold:'-- Concepto --',
                nodo_proyecto:'',
                asignacion_nodo:[],
                asignados:[],
                id_concepto:'',
                pendientes:[],
                cargando:false,
            }
        },
        mounted() {
            this.getNodos();
        },
        methods: {
            getNodos(){
                return this.$store.dispatch('cadeco/concepto/index', {
                    params: { scope:['roots', 'conceptoActivo']}
                }).then(data => {
                    this.$store.commit('cadeco/concepto/SET_CONCEPTOS', data.data)
                    if(data.data.length === 1){
                        this.nodo_proyecto = data.data[0].id;
                        this.getAsignacionesNodos();
                    }
                })
                .finally(() => {
                    this.cargando = false;
                });
            },
            getAsignacionesNodos(){
                this.asignados = [];
                this.pendientes =[];
                return this.$store.dispatch('configuracion/nodo-proyecto/find', {
                    id:this.nodo_proyecto,
                    params: {include:['nodo_tipo.ctg_tipo_nodo']}
                }).then(data => {
                    this.asignacion_nodo = data;
                    this.asignados = data.nodo_tipo.data;
                    this.pendientes = data.tipos_pendiente_asignacion;
                })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            asignar(id){
                this.cargando = true;
                return this.$store.dispatch('configuracion/nodo-tipo/store', {
						id_concepto: this.pendientes[id].id_concepto,
                        id_tipo_nodo: this.pendientes[id].id,
						id_concepto_proyecto: this.nodo_proyecto,
					})
                    .then(data=> {
                        this.getAsignacionesNodos();
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            eliminar(id){
                this.cargando = true;
                return this.$store.dispatch('configuracion/nodo-tipo/delete', {
						id: this.asignados[id].concepto
					})
                    .then(data=> {
                        this.getAsignacionesNodos();
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
        },
        computed: {
            conceptos() {
                return this.$store.getters['cadeco/concepto/conceptos']
            },
        },
        watch:{
            nodo_proyecto(id) {
                if (id > 0) {
                    this.getAsignacionesNodos();
                }
            },
        }
    }
</script>

<style scoped>

</style>

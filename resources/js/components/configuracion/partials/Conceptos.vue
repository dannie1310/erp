<template>
    <div class="card" id="configuracion-conceptos" v-if="true">
        <div class="card-header">
            <h3 class="card-title">Configuración Conceptos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <h5 id="configuracion_conceptos_nodo">Configuración Nodo Tipo</h5>
            <div class="form-group row">
                <label for="nodo_proyecto" class="col-sm-2 col-form-label">Proyecto</label>
                <div class="col-sm-10">
                    <select class="form-control" id="nodo_proyecto" v-model="nodo_proyecto"
                            v-validate="{integer: true}"
                            name="nodo_proyecto"
                            data-vv-as="nodo_proyecto"
                            :class="{'is-invalid': errors.has('nodo_proyecto')}"
                    >
                        <option value>-- Proyecto --</option>
                        <option v-for="concepto in conceptos" :value="concepto.id">{{ concepto.descripcion }}</option>
                    </select>
                    <div class="invalid-feedback" v-show="errors.has('nodo_proyecto')">{{ errors.first('nodo_proyecto') }}</div>
                </div>
            </div>
            <fieldset class="form-group" v-if="pendientes.length > 0">
                <div class="row" v-for="pendiente in pendientes">
                    <legend class="col-form-label col-sm-4 pt-0"><b>Penalización / Devolución Penalización</b></legend>
                    <div class="col-sm-8" >
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="penalizacion_antes_iva1" value="1">
                            <label class="form-check-label"> Antes de IVA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="penalizacion_antes_iva0" value="0">
                            <label class="form-check-label"> Después de IVA</label>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        
    </div>
</template>

<script>
    export default {
        name: "configuracion-conceptos",
        props: ['datosConcepto'],
        data() {
            return {
                nodo_proyecto:'',
                asignacion_nodo:[],
                asignados:[],
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
                    params: { scope:['roots']}
                }).then(data => {
                    this.$store.commit('cadeco/concepto/SET_CONCEPTOS', data.data)
                })
                .finally(() => {
                    this.cargando = false;
                });
            },
            getAsignacionesNodos(){
                return this.$store.dispatch('configuracion/nodo-proyecto/find', {
                    id:this.nodo_proyecto,
                    params: {}
                }).then(data => {
                    this.asignacion_nodo = data;
                    this.asignados = data.tipos_asignados;
                    this.pendientes = data.tipos_pendiente_asignacion;
                })
                    .finally(() => {
                        this.cargando = false;
                    });
            }
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

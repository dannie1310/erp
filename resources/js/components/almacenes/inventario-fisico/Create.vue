<template>
    <span>
        <button @click="init" v-if="$root.can('iniciar_inventario_fisico')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Iniciar Inventario Físico
        </button>
                 <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Inventario Fisico</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!--Usuario-->
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="usuario" class="col-sm-3 col-form-label">Usuario:</label>
                                        <div class="col-sm-10">
                                            <input

                                                type="text"
                                                name="usuario"
                                                data-vv-as="Usuario"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="usuario"
                                                placeholder="Usuario Inicío"
                                                v-model="usuario"
                                                :class="{'is-invalid': errors.has('usuario')}">
                                            <div class="invalid-feedback" v-show="errors.has('usuario')">{{ errors.first('usuario') }}</div>
                                        </div>
                                    </div>
                                </div>

                                    <div class="col-md-4">
                                    <div class="form-group row error-content">
                                        <label for="id_tipo" class="col-sm-3 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="id_tipo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos" :key="llave">
                                                    <input type="radio"
                                                           class="btn-group-toggle"
                                                           name="id_tipo"
                                                           :id="'tipo' + llave"
                                                           :value="llave"
                                                           autocomplete="on"
                                                           v-model.number="id_tipo">
                                                            {{ tipo}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Inventario-->
                                    <div class="col-md-2">
                                    <div class="form-group row error-content">
                                        <label for="inventario" class="col-sm-4 col-form-label">Porcentaje:</label>
                                        <div class="col-sm-9">
                                            <select
                                                type="text"
                                                name="inventario"
                                                data-vv-as="Inventario"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="inventario"
                                                v-model="inventario"
                                                :class="{'is-invalid': errors.has('inventario')}"
                                            >
                                                    <option value>-- % --</option>
                                                    <option v-for="inventario in inventarios" :value="inventario.id">{{ inventario.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('inventario')">{{ errors.first('inventario') }}</div>
                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>
    export default {
        name: "inventario-fisico-create",
        data() {
            return {
                cargando: false,
                inventarios: [
                    {id: 1, descripcion: '80%'},
                    {id: 2, descripcion: '60%'},
                    {id: 3, descripcion: '40%'},
                    {id: 4, descripcion: '20%'},
                    {id: 5, descripcion: '10%'}
                ],
                tipos: {
                    1: "Conteo Total",
                    2: "Conteo Aleatorio"
                },
                inventario:'',
                usuario:''
            }
        },
        mounted(){
        },
        methods:{
            init() {
                $(this.$refs.modal).modal('show');
                this.cargando = false;
            },
            store() {
                return this.$store.dispatch('almacenes/inventario-fisico/store', this.dato)
                    .then(data => {
                        this.$emit('created', data);
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>

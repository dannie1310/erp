<template>
    <span>
        <button @click="init" v-if="$root.can('iniciar_inventario_fisico')" class="btn btn-app float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Iniciar Inventario Físico
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Iniciar Inventario Fisico</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="cargando">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <div class="modal-body" v-else>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group row error-content">
                                        <label for="id_tipo" class="col-sm-2 col-form-label">Tipo: </label>
                                        <div class="col-sm-10" align="right">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="id_tipo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos" :key="llave">
                                                    <input type="radio"
                                                           class="btn-group-toggle"
                                                           name="id_tipo"
                                                           data-vv-as="Tipo"
                                                           v-validate="{required: true}"
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
                                <div class="col-md-2"></div>
                                <div class="col-md-5">
                                    <div class="form-group row error-content">
                                        <label for="inventario" class="col-md-5 col-form-label">Porcentaje:</label>
                                        <div class="col-md-5">
                                            <select
                                                :disabled="id_tipo!=2"
                                                type="text"
                                                name="inventario"
                                                data-vv-as="Inventario"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="inventario"
                                                v-model="form.inventario"
                                                :class="{'is-invalid': errors.has('inventario')}">
                                                    <option value>-- % --</option>
                                                    <option v-for="inventario in inventarios" :value="inventario.id">{{ inventario.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('inventario')">{{ errors.first('inventario') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label class="col-md-4 col-form-label">Almacenes: </label>
                                        <div class="col-md-8">
                                        <treeselect v-model="almacenes_seleccionados"
                                                    :multiple="true"
                                                    :options="almacenes"
                                                    data-vv-as="Almacenes"
                                                    placeholder="Seleccione los almacenes deseados">
                                            <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                                        </treeselect>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" v-if="!cargando">Iniciar</button>
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
                    {id: 100,descripcion: '80%'},
                    {id: 75, descripcion: '60%'},
                    {id: 50, descripcion: '40%'},
                    {id: 25, descripcion: '20%'},
                    {id: 12, descripcion: '10%'}
                ],
                tipos: {
                    1: "Total",
                    2: "Parcial"
                },
                id_tipo: 1,
                form: {inventario: '', almacenes: []},
                almacenes: [],
                almacenes_seleccionados: []
            }
        },
        mounted(){
            this.getAlmacenes();
        },
        methods:{
            init() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.cargando = false;
                this.id_tipo=1;
                this.form.inventario='';
                this.form.almacenes=[];
                this.almacenes_seleccionados= [];
                if(this.almacenes == []) {
                    this.getAlmacenes();
                }
            },
            getAlmacenes() {
                this.$store.commit('cadeco/almacen/SET_ALMACENES', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion', order: 'asc', scope: 'tipoMaterialYHerramienta' }
                })
                    .then(data => {
                        this.almacenes = data.data
                        this.almacenesAcomodar();
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
            almacenesAcomodar () {
                this.almacenes = this.almacenes.map(i => ({
                    id: i.id,
                    label: `${i.descripcion}`,
                    customLabel: `${i.descripcion}`,
                }));
            },
            store() {
                if (this.id_tipo == 1) {
                    var datos = {total: true, almacenes: this.almacenes_seleccionados};
                    return this.$store.dispatch('almacenes/inventario-fisico/store', datos)
                        .then(data => {
                            this.$emit('created', data);
                        }).finally(() => {
                            this.cargando = false;
                            $(this.$refs.modal).modal('hide');
                        });
                } else {
                    this.form.almacenes = this.almacenes_seleccionados;
                    return this.$store.dispatch('almacenes/inventario-fisico/store', this.form)
                        .then(data => {
                            this.$emit('created', data);
                        }).finally(() => {
                            this.cargando = false;
                            $(this.$refs.modal).modal('hide');
                        });
                }
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

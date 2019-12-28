<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-md-12" >
                    <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class= "row">
                                <div class="col-md-2">
                                    <div class="form-group row error-content">
                                        <label for="fecha" class="col-sm-2 col-form-label">Fecha: </label>
                                            <datepicker v-model = "fecha"
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>

                            </div>
                            <div class= "row">
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="referencia" class="col-sm-2 col-form-label">Referencia: </label>
                                        <div class="col-sm-10">
                                            <input
                                                    type="text"
                                                    step="any"
                                                    name="referencia"
                                                    data-vv-as="Referencia"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="referencia"
                                                    placeholder="Referencia"
                                                    v-model="referencia"
                                                    :class="{'is-invalid': errors.has('referencia')}">
                                            <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                                <label for="id_almacen" class="col-sm-2 col-form-label">Almacén: </label>
                                                <div class="col-sm-10">
                                                    <select
                                                            type="text"
                                                            name="id_almacen"
                                                            data-vv-as="Almacén"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="id_almacen"
                                                            v-model="id_almacen"
                                                            :class="{'is-invalid': errors.has('id_almacen')}"
                                                    >
                                                            <option value>-- Seleccione un almacén --</option>
                                                            <option v-for="almacen in almacenes" :value="almacen.id">{{ almacen.descripcion }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                                </div>
                                            </div>
                                </div>

                            </div>
                        </div>
                     </form>
                   </div>
                </div>
            </div>

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a aria-controls="nav-home" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-home"
                       id="nav-home-tab" role="tab" v-if="$root.can('registrar_ajuste_positivo')">Ajuste (+)</a>
                    <a aria-controls="nav-profile" aria-selected="false" class="nav-item nav-link" data-toggle="tab"
                       href="#nav-profile" id="nav-profile-tab" role="tab" v-if="$root.can('registrar_ajuste_negativo')">Ajuste (-)</a>
                    <a aria-controls="nav-contact" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-contact"
                       id="nav-contact-tab" role="tab" v-if="$root.can('registrar_nuevo_lote')">Nuevo Lote</a>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div aria-labelledby="nav-home-tab" class="tab-pane fade show active" id="nav-home" role="tabpanel">
                    <ajuste-positivo v-bind:id_almacen="id_almacen" :key="id_almacen" v-bind:referencia="referencia" v-bind:fecha="fecha"></ajuste-positivo>
                </div>
                <div aria-labelledby="nav-profile-tab" class="tab-pane fade" id="nav-profile" role="tabpanel">
                    <ajuste-negativo v-bind:id_almacen="id_almacen" :key="id_almacen" v-bind:referencia="referencia" v-bind:fecha="fecha"></ajuste-negativo>
                </div>
                <div aria-labelledby="nav-contact-tab" class="tab-pane fade" id="nav-contact" role="tabpanel" style="display:block;">
                    <nuevo-lote v-bind:id_almacen="id_almacen" :key="id_almacen" v-bind:referencia="referencia" v-bind:fecha="fecha"></nuevo-lote>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import AjusteNegativo from "./ajuste-negativo/Create";
    import AjustePositivo from "./ajuste-positivo/Create";
    import NuevoLote from "./nuevo-lote/Create";
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "ajuste-create",
        components: {AjusteNegativo, AjustePositivo, NuevoLote, datepicker},
        data() {
            return {
                es: es,
                cargando: false,
                id_almacen: '',
                referencia: '',
                fecha: '',
                almacenes: [],
            }
        },
        mounted(){
            this.getAlmacen();
            this.fecha = new Date();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD-MM-YYYY');
            },
            getAlmacen() {
                this.almacenes = [];
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {
                        scope: ['tipoMaterialYHerramienta'],
                        sort: 'descripcion',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.almacenes = data.data;
                    })
            },
        }
    }
</script>

<style scoped>

</style>

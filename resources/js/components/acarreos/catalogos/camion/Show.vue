<template>
    <span>
        <div v-if="cargando">
            <div class="row">
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row justify-content-end">
                <historico v-bind:historicos="camion.historicos.data" v-bind:id="id" v-if="camion" />
            </div>
             <div class="row">
                 <div class="col-md-12">
                     <br>
                 </div>
             </div>
            <div class="row">
                <div class="offset-md-8 col-md-4">
                    <span class="pull-right">
                        <div class="card">
                            <div class="card-header">
                                <h5>Camión: <b>{{camion.economico}} [{{camion.placa}}]</b></h5>
                            </div>
                        </div>
                    </span>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Sindicato:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           id="sindicato"
                                           name="sindicato"
                                           class="form-control"
                                           v-model="camion.sindicato" />
                                </div>
                                <label class="col-md-1 col-form-label">Empresa:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           name="empresa"
                                           id="empresa"
                                           class="form-control"
                                           v-model="camion.empresa" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Propietario:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           name="propietario"
                                           id="propietario"
                                           class="form-control"
                                           v-model="camion.propietario" />
                                </div>
                                <label class="col-md-1 col-form-label">Operador:</label>
                                <div class="col-md-5">
                                    <input disabled="true"
                                           type="text"
                                           id="operador"
                                           name="operador"
                                           class="form-control"
                                           v-model="camion.operador" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Económico:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           id="economico"
                                           name="economico"
                                           class="form-control"
                                           v-model="camion.economico" />
                                </div>
                                <label class="col-md-1 col-form-label">Placas Camión:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="placa"
                                           id="placa"
                                           class="form-control"
                                           v-model="camion.placa" />
                                </div>
                                <label class="col-md-1 col-form-label">Placas Caja:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="placa_caja"
                                           id="placa_caja"
                                           class="form-control"
                                           v-model="camion.placa_caja" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Marca:</label>
                                <div class="col-md-4">
                                    <input disabled="true"
                                           type="text"
                                           id="marca"
                                           name="marca"
                                           class="form-control"
                                           v-model="camion.marca" />
                                </div>
                                <label class="col-md-1 col-form-label">Modelo:</label>
                                <div class="col-md-4">
                                    <input disabled="true"
                                           type="text"
                                           name="modelo"
                                           id="modelo"
                                           class="form-control"
                                           v-model="camion.modelo" />
                                </div>
                                <label class="col-md-1 col-form-label">Imágenes:</label>
                                <div class="col-md-1">
                                    <button @click="getImagenes()" class="btn btn-primary pull-center" :disabled="tieneImagen">
                                        <i class="fa fa-file-image-o"></i> Ver Imágenes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información de Seguro</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Aseguradora:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           id="aseguradora"
                                           name="aseguradora"
                                           class="form-control"
                                           v-model="camion.aseguradora" />
                                </div>
                                <label class="col-md-1 col-form-label">Poliza:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="poliza_seguro"
                                           id="poliza_seguro"
                                           class="form-control"
                                           v-model="camion.poliza_seguro" />
                                </div>
                                <label class="col-md-1 col-form-label">Vigencia:</label>
                                <div class="col-md-3">
                                    <input disabled="true"
                                           type="text"
                                           name="vigencia_poliza"
                                           id="vigencia_poliza"
                                           class="form-control"
                                           v-model="camion.vigencia_poliza" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Información de Cubicación</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Ancho:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           id="ancho"
                                           name="ancho"
                                           class="form-control"
                                           v-model="camion.ancho" />
                                </div>
                                <label class="col-md-1 col-form-label">Largo:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           name="largo"
                                           id="largo"
                                           class="form-control"
                                           v-model="camion.largo" />
                                </div>
                                <label class="col-md-1 col-form-label">Alto:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           name="alto"
                                           id="alto"
                                           class="form-control"
                                           v-model="camion.alto" />
                                </div>
                                <label class="col-md-1 col-form-label">Gato:</label>
                                <div class="col-md-2">
                                    <input disabled="true"
                                           type="text"
                                           name="estapacio_gato"
                                           id="estapacio_gato"
                                           class="form-control"
                                           v-model="camion.estapacio_gato" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                 </div>
            </div>
            <div class="modal fade" ref="modalImagen" tabindex="-1" role="dialog" aria-labelledby="modal">
                <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" id="mdialTamanio">
                    <div class="modal-content">
                        <Imagen v-bind:imagenes="camion.imagenes.data" v-bind:id="camion.id"></Imagen>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Historico from './ShowHistorico';
    import Imagen from '../../../globals/archivos/Imagen';
    export default {
        name: "camion-show",
        props: ['id'],
        components: {Imagen, Historico},
        data() {
            return {
                cargando : true,
                tieneImagen : false
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            salir() {
                this.$router.push({name: 'camion'});
            },
            find() {
                this.$store.commit('acarreos/camion/SET_CAMION', null);
                return this.$store.dispatch('acarreos/camion/find', {
                    id: this.id,
                    params: {include: ['imagenes', 'historicos']}
                }).then(data => {
                    this.$store.commit('acarreos/camion/SET_CAMION', data);
                    if(data.imagenes.data.length == 0)
                    {
                        this.tieneImagen = true
                    }
                }).finally(() => {
                    this.cargando = false;
                })
            },
            getImagenes() {
                $(this.$refs.modalImagen).appendTo('body');
                $(this.$refs.modalImagen).modal('show');
            },
        },
        computed: {
            camion() {
                return this.$store.getters['acarreos/camion/currentCamion']
            }
        }
    }
</script>

<style scoped>

</style>

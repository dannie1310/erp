<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="concurso">Fecha:</label>
                           <datepicker v-model = "fecha"
                                       id="fecha"
                                       name = "fecha"
                                       :format = "formatoFecha"
                                       :language = "es"
                                       :bootstrap-styling = "true"
                                       class = "form-control"
                                       v-validate="{required: true}"
                                       :disabled-dates="fechasDeshabilitadas"
                                       :class="{'is-invalid': errors.has('fecha')}"
                           ></datepicker>
                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group error-content">
                            <label for="concurso">Número de Licitación:</label>
                            <input class="form-control"
                                   type="text"
                                   placeholder="Número de Licitación"
                                   name="numero_licitacion"
                                   id="numero_licitacion"
                                   data-vv-as="'Número de Licitación'"
                                   v-validate="{required: true, max: 255}"
                                   v-model="numero_licitacion"
                                   :class="{'is-invalid': errors.has('numero_licitacion')}">
                            <div class="invalid-feedback" v-show="errors.has('numero_licitacion')">{{ errors.first('numero_licitacion') }}</div>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-group error-content">
                            <label for="concurso">Entidad Licitante:</label>
                            <input class="form-control"
                                   type="text"
                                   placeholder="Entidad Licitante"
                                   name="entidad_licitante"
                                   id="entidad_licitante"
                                   data-vv-as="'Entidad Licitante'"
                                   v-validate="{required: true, max: 255}"
                                   v-model="entidad_licitante"
                                   :class="{'is-invalid': errors.has('entidad_licitante')}">
                            <div class="invalid-feedback" v-show="errors.has('entidad_licitante')">{{ errors.first('entidad_licitante') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group error-content">
                            <label for="concurso">Nombre del Concurso:</label>
                            <input class="form-control"
                                   type="text"
                                   placeholder="Nombre del Concurso"
                                   name="concurso"
                                   id="concurso"
                                   data-vv-as="'Nombre del concurso'"
                                   v-validate="{required: true, max: 40}"
                                   v-model="concurso"
                                   :class="{'is-invalid': errors.has('concurso')}">
                            <div class="invalid-feedback" v-show="errors.has('concurso')">{{ errors.first('concurso') }}</div>
                        </div>

                    </div>

                </div>


                <br />
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="regresar">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" @click="validateConcurso" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "create",
        components: {Datepicker},
        mounted() {
            this.fecha = new Date();
            this.$validator.reset();
            this.$validator.errors.clear();
            this.fechasDeshabilitadas.from = new Date();
        },
        data(){
            return {
                fechasDeshabilitadas :{},
                cargando : false,
                es:es,
                fecha : '',
                numero_licitacion : '',
                entidad_licitante : '',
                concurso : '',
                participantes : [],
                participante :
                {
                    'nombre' : '',
                    'monto' : 0,
                    'es_hermes' : false
                },
                es_hermes_seleccionado : 0
            }
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            validateConcurso() {
                this.$validator.detach("monto");
                this.$validator.validate().then(result => {
                    if (result){
                        this.store();
                    }
                });
            },
            iniciar(){
                this.fecha = '';
                this.numero_licitacion = '';
                this.entidad_licitante = '';
                this.concurso = '';
                this.participantes = [];
                this.participante =
                {
                    'nombre' : '',
                    'monto' : 0,
                    'es_hermes' : false
                };
                this.es_hermes_seleccionado = 0;
            },
            agregar(){
                $(this.$refs.modal1).appendTo('body')
                $(this.$refs.modal1).modal('show');
            },
            quitar(index){
                if(this.participantes[index].es_hermes ==  true)
                {
                    this.es_hermes_seleccionado = 0;
                }
                this.participantes.splice(index, 1);
            },
            regresar() {
                this.iniciar();
                this.$router.push({name: 'concursos'});
            },
            cerrar(){
                this.$validator.reset();
                this.$validator.errors.clear();
                this.participante = {
                    'nombre' : '',
                    'monto' : 0,
                    'es_hermes' : false
                }
                $(this.$refs.modal1).modal('hide');
            },
            store() {
                this.$store.dispatch('concursos/concurso/store', {
                    fecha : this.fecha,
                    numero_licitacion : this.numero_licitacion,
                    entidad_licitante : this.entidad_licitante,
                    concurso: this.concurso,
                    participantes: this.participantes
                })
                    .then(data=> {
                        this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                        this.$router.push({name: 'concurso-edit', params: {id: this.concurso_registrado.id}});

                    })
                    .catch(error => {
                    })
                    .finally(() => {

                    })
			},
        },
        computed: {
            concurso_registrado() {
                return this.$store.getters['concursos/concurso/currentConcurso'];
            }
        }
    }
</script>

<style scoped>

</style>

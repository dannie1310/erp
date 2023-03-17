<template>
    <span>
        <nav>
            <div  v-if="this.cargando || this.concurso_store == null">
                <div class="card">
                    <div class="card-body">
                        <div class="row" >
                            <div class="col-md-12">
                                <div class="spinner-border text-success" role="status">
                                   <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-else>
                <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha">Fecha:</label>
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
                                    <label for="numero_licitacion">Número de Licitación:</label>
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
                                    <label for="entidad_licitante">Entidad Licitante:</label>
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
                                           v-validate="{required: true, max: 255}"
                                           v-model="concurso"
                                           :class="{'is-invalid': errors.has('concurso')}">
                                    <div class="invalid-feedback" v-show="errors.has('concurso')">{{ errors.first('concurso') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" >
                                <button type="button" @click="validateConcurso" class="btn btn-primary pull-right">
                                    <i class="fa fa-save"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>

                        <br />
<!-- INICIA TABLA DE PARTICIPANTES -->
                        <div class="row">
                            <div  class="col-12">
                                <div class="table-responsive">
                                    <table class="table  table-sm table-bordered" style="font-size: 11px">
                                        <thead>
                                            <tr>
                                                <th class="encabezado index_corto">
                                                    #
                                                </th>
                                                <th class="encabezado">
                                                    Participante
                                                </th>
                                                <th class="encabezado c90">
                                                    Monto
                                                </th>
                                                <th class="encabezado c50">
                                                    Es Hermes
                                                </th>
                                                <th class="encabezado c80">
                                                    <button type="button" class="btn btn-sm btn-outline-success" v-on:click="agregar()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(participante, i) in this.concurso_store.participantes.data">
                                                <td>{{i+1}}</td>
                                                <td>
                                                {{participante.nombre}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{parseFloat(participante.monto).formatMoney(2,'.',',')}}
                                                </td>
                                                <td style="text-align: center">
                                                    <check-es-hermes v-bind:id="concurso_store.id" v-bind:participante="participante" ></check-es-hermes>
                                                </td>
                                                <td style="text-align: center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" v-on:click="editarParticipante(i)">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" v-on:click="quitarParticipante(i)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="regresar">
                                <i class="fa fa-angle-left"></i>
                                Regresar</button>
                            <cierre-concurso v-bind:id="this.concurso_store.id" v-bind:texto="'Cerrar'"></cierre-concurso>
                        </div>
                    </div>
                </div>
<!--MODAL PARA AGREGAR PARTICIPANTE -->
                <div class="modal fade" ref="modal1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i>&nbsp;AGREGAR PARTICIPANTE</h5>
                                <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="nombre">Nombre Participante:</label>
                                                <input class="form-control"
                                                        placeholder="Nombre del Participante"
                                                        name="nombre"
                                                        id="nombre"
                                                        v-validate="{max: 255}"
                                                        data-vv-as="Nombre del Participante"
                                                        v-model="participante.nombre"
                                                        :class="{'is-invalid': errors.has('nombre')}">
                                                <div class="invalid-feedback" v-show="errors.has('nombre')">{{ errors.first('nombre') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="nombre">Monto:</label>
                                                <input
                                                        name="monto"
                                                        v-model="participante.monto"
                                                        data-vv-as="Monto"
                                                        v-validate="{min_value: 0.1, regex: /^[0-9]\d*(\.\d{0,2})?$/}"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has(`monto`)}"
                                                        style="text-align: right"
                                                        id="monto"
                                                        placeholder="Monto">
                                                <div class="invalid-feedback"
                                                        v-show="errors.has(`monto`)">{{ errors.first(`monto`) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" >
                                        <div class="col-md-6" >
                                            <div class="form-group">
                                                <input type="checkbox" id="es_empresa_hermes" v-model="participante.es_empresa_hermes" >
                                                <label for="nombre">&nbsp;Es Hermes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6" >
                                            <div class="form-group">
                                                <input type="checkbox" id="notificar" v-model="participante.notificar" >
                                                <label for="notificar">&nbsp;Notificar <i class="fa fa-comment"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="cerrarModal"><i class="fa fa-close"></i>Cerrar</button>
                                <button type="button" @click="guardaParticipante" class="btn btn-primary">
                                    <i class="fa fa-save"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
<!--MODAL PARA EDITAR PARTICIPANTE -->
                <div class="modal fade" ref="modal2" role="dialog">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil-alt"></i>&nbsp;EDITAR PARTICIPANTE</h5>
                                <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="nombre">Nombre Participante:</label>
                                                <input class="form-control"
                                                       placeholder="Nombre del Participante"
                                                       name="nombre"
                                                       id="nombre"
                                                       v-validate="{max: 255}"
                                                       data-vv-as="Nombre del Participante"
                                                       v-model="participante.nombre"
                                                       :class="{'is-invalid': errors.has('nombre')}">
                                                <div class="invalid-feedback" v-show="errors.has('nombre')">{{ errors.first('nombre') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="monto">Monto:</label>
                                                <input
                                                    type="number"
                                                    name="monto"
                                                    v-model="participante.monto"
                                                    data-vv-as="Monto"
                                                    v-validate="{min_value: 0.1, regex: /^[0-9]\d*(\.\d{0,2})?$/}"
                                                    class="form-control"
                                                    :class="{'is-invalid': errors.has(`monto`)}"
                                                    style="text-align: right"
                                                    id="monto"
                                                    placeholder="Monto">
                                                <div class="invalid-feedback"
                                                     v-show="errors.has(`monto`)">{{ errors.first(`monto`) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" >
                                        <div class="col-md-6" >
                                            <div class="form-group">
                                                <input type="checkbox" id="es_empresa_hermes" v-model="participante.es_empresa_hermes" >
                                                <label for="nombre">&nbsp;Es Hermes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6" >
                                            <div class="form-group">
                                                <input type="checkbox" id="notificar" v-model="participante.notificar" >
                                                <label for="notificar">&nbsp;Notificar <i class="fa fa-comment"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="cerrarModal"><i class="fa fa-close"></i>Cerrar</button>
                                <button type="button" @click="updateParticipante" class="btn btn-primary">
                                    <i class="fa fa-save"></i>Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>
</span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import CierreConcurso from "./CierreConcurso.vue";
    import CheckEsHermes from "./partials/CheckEsHermes.vue";

    export default {
        name: "edit-concurso",
        components: {CheckEsHermes, CierreConcurso, Datepicker},
        props: ['id'],
        data(){
            return {
                es:es,
                cargando: false,
                participante :
                {
                    'nombre' : '',
                    'monto' : 0,
                    'es_empresa_hermes' : false,
                    'id_concurso' : '',
                    'notificar' : true,
                },
                concurso: '',
                numero_licitacion: '',
                fecha:'',
                entidad_licitante : '',
                es_hermes_seleccionado : 0,
                fechasDeshabilitadas :{},
            }
        },
        mounted() {
            this.find();
            this.$validator.reset();
            this.fechasDeshabilitadas.from = new Date();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            validateConcurso() {
                this.$validator.detach("monto");
                this.$validator.validate().then(result => {
                    if (result){
                        this.update();
                    }
                });
            },
            find() {
                if(this.concurso_store == null || this.concurso_store.id != this.id)
                {
                    this.cargando = true;
                    this.$store.dispatch('concursos/concurso/find', {
                        id: this.id,
                        params:{include: []}
                    }).then(data => {
                        this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                        this.checar_participantes_hermes();
                        this.concurso = this.concurso_store.nombre;
                        this.entidad_licitante = this.concurso_store.entidad_licitante;
                        this.fecha = this.concurso_store.fecha;
                        this.numero_licitacion = this.concurso_store.numero_licitacion;
                        this.cargando = false
                    }).catch(error => {
                    })
                }else{
                    this.concurso = this.concurso_store.nombre;
                    this.entidad_licitante = this.concurso_store.entidad_licitante;
                    this.fecha = this.concurso_store.fecha;
                    this.numero_licitacion = this.concurso_store.numero_licitacion;
                }
            },
            checar_participantes_hermes()
            {
                for (var key in this.concurso_store.participantes.data) {
                    var obj = this.concurso_store.participantes.data[key];
                    if(obj.es_empresa_hermes == true) {
                        this.es_hermes_seleccionado = 1;
                    }
                }
            },
            agregar(){
                this.iniciar();
                $(this.$refs.modal1).appendTo('body')
                $(this.$refs.modal1).modal('show');
            },
            quitarParticipante(index){
                return this.$store.dispatch('concursos/concurso/quitaParticipante', {
                    id: this.concurso_store.id,
                    id_participante: this.concurso_store.participantes.data[index].id,
                })
                    .then(data => {
                        this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                    })
                    .catch(error => {
                    })
                    .finally(() => {
                        this.cerrarModal();
                    })
            },
            editarParticipante(index){
                let _self = this;
                this.$store.dispatch('concursos/concurso/findParticipante', {
                    id: this.id,
                    id_participante: this.concurso_store.participantes.data[index].id,
                    params:{include: []}
                }).then(data => {
                    this.$store.commit('concursos/concurso/SET_PARTICIPANTE', data);
                    this.participante = {
                        'nombre' : _self.participante_store.nombre,
                        'monto' :  _self.participante_store.monto,
                        'es_empresa_hermes' : (_self.participante_store.es_empresa_hermes == 0 || _self.participante_store.es_empresa_hermes == false)?false:true,
                        'id' : _self.participante_store.id,
                        'notificar' : true,
                    };
                    this.$validator.reset();
                    this.$validator.errors.clear();
                    $(this.$refs.modal2).appendTo('body')
                    $(this.$refs.modal2).modal('show');
                })
                    .catch(error => {
                    })
            },
            iniciar(){
                this.$validator.reset();
                this.$validator.errors.clear();
                this.participante = {
                    'nombre' : '',
                    'monto' : 0,
                    'es_empresa_hermes' : false,
                    'id_concurso' : '',
                    'notificar' : true,
                }
            },
            cerrarModal(){
                this.iniciar();
                $(this.$refs.modal1).modal('hide');
                $(this.$refs.modal2).modal('hide');
            },
            guardaParticipante() {
                if(this.participante.nombre == '')
                {
                    swal('¡Error!', 'Debe agregar un nombre del participante.', 'error')
                }
                else if(this.participante.monto <= 0)
                {
                    swal('¡Error!', 'Debe agregar un monto.', 'error')
                }
                else{
                    this.participante.id_concurso = this.concurso_store.id;
                    return this.$store.dispatch('concursos/concurso/guardaParticipante', {
                        id: this.id,
                        data: this.participante,
                    })
                    .then(data => {
                        this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                        this.cerrarModal();
                    })
                    .catch(error => {
                    })
                    .finally(() => {

                    })
                }
            },
            updateParticipante() {
                if(this.participante.nombre == '')
                {
                    swal('¡Error!', 'Debe agregar un nombre del participante.', 'error')
                }
                else if(this.participante.monto <= 0)
                {
                    swal('¡Error!', 'Debe agregar un monto.', 'error')
                }
                else{
                    return this.$store.dispatch('concursos/concurso/updateParticipante', {
                        id: this.id,
                        id_participante: this.participante.id,
                        data: this.participante
                    })
                    .then(data => {
                        this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                    })
                    .catch(error => {
                    })
                    .finally(() => {
                        this.cerrarModal();
                    })
                }

            },
            update() {
                return this.$store.dispatch('concursos/concurso/update', {
                    id: this.id,
                    data: {
                        fecha : this.fecha,
                        entidad_licitante : this.entidad_licitante,
                        numero_licitacion : this.numero_licitacion,
                        nombre : this.concurso
                    }
                })
                .then(data => {
                    this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                })
                .catch(error => {
                })
			},
            regresar() {
                this.iniciar();
                this.$router.push({name: 'concursos'});
            },
        },
        computed: {
            concurso_store() {
                return this.$store.getters['concursos/concurso/currentConcurso'];
            },
            participante_store() {
                return this.$store.getters['concursos/concurso/currentParticipante'];
            }

        }
    }
</script>

<style scoped>

</style>

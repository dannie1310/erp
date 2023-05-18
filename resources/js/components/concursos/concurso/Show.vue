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
                            <div class="col-md-12">
                                <h3>{{concurso}}</h3>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2 " >
                                <label for="fecha">Fecha de Apetura:</label>
                            </div>
                            <div class="col-md-1" >
                                {{concurso_store.fecha_format}}
                            </div>
                            <div class="col-md-2 " >
                                <label for="fecha">Estado de Apetura:</label>
                            </div>
                            <div class="col-md-1" >
                                {{concurso_store.estado_apertura}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2" >
                                <label for="fecha">Fecha de Fallo:</label>
                            </div>
                            <div class="col-md-1" >
                                {{concurso_store.fecha_fallo_format}}
                            </div>
                            <div class="col-md-2 " >
                                <label for="fecha">Estado de Fallo:</label>
                            </div>
                            <div class="col-md-1" >
                                {{concurso_store.estado_fallo}}
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group error-content">
                                    <label for="numero_licitacion">Número de Licitación:</label>
                                    <div>
                                        {{numero_licitacion}}
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="entidad_licitante">Entidad Licitante:</label>
                                    <div>
                                        {{entidad_licitante}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group error-content">
                                    <label for="concurso">Nombre del Concurso:</label>
                                    <div>
                                        {{concurso}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <label >Resultado Apetura:</label>
                            </div>
                            <div class="col-md-6">
                                {{concurso_store.resultado_apertura}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label >Resultado Fallo:</label>
                            </div>
                            <div class="col-md-6">
                                {{concurso_store.resultado_fallo}}
                            </div>
                        </div>

                        <br />
                        <!-- INICIA TABLA DE PARTICIPANTES -->
                        <div class="row">
                            <div  class="col-12">
                                <div class="table-responsive">
                                    <table class="table  table-sm table-bordered" style="font-size: 15px">
                                        <thead>
                                            <tr>
                                                <th class="encabezado index_corto">
                                                    #
                                                </th>
                                                <th class="encabezado c30">
                                                    G
                                                </th>
                                                <th class="encabezado">
                                                    Participante
                                                </th>
                                                <th class="encabezado c90">
                                                    Monto Sin IVA
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(participante, i) in this.concurso_store.participantes.data"
                                                :style="participante.es_empresa_hermes?`background-color:#7DB646`:``"
                                            >
                                                <td>{{i+1}}</td>
                                                <td style="text-align: center">
                                                    <i class="fa fa-check" v-if="participante.es_ganador" />
                                                </td>
                                                <td>
                                                {{participante.nombre}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{parseFloat(participante.monto).formatMoney(2,'.',',')}}
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
                            <button type="button" class="btn btn-secondary" v-on:click="regresar" :disabled="actualizando">
                                <i class="fa fa-angle-left"></i>
                                Regresar</button>
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
import CheckEsHermes from "./partials/CheckEsHermes.vue";
import CheckEsGanador from "./partials/CheckEsGanador.vue";

export default {
    name: "show-concurso",
    components: {CheckEsGanador, CheckEsHermes, Datepicker},
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
            fecha_fallo:'',
            entidad_licitante : '',
            es_hermes_seleccionado : 0,
            fechasDeshabilitadas :{},
        }
    },
    mounted() {
        this.fecha_fallo = new Date();
        this.find();
        this.$validator.reset();
        this.fechasDeshabilitadas.from = new Date();
    },
    methods: {
        formatea(participante){
            let monto_formateado = 0;
            monto_formateado = participante.monto.formatearkeyUp();
            participante.monto = monto_formateado;
        },
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
                    this.fecha = this.concurso_store.fecha_format;
                    this.fecha_fallo = this.concurso_store.fecha_fallo_format;
                    this.numero_licitacion = this.concurso_store.numero_licitacion;
                    this.cargando = false
                }).catch(error => {
                })
            }else{
                this.concurso = this.concurso_store.nombre;
                this.entidad_licitante = this.concurso_store.entidad_licitante;
                this.fecha = this.concurso_store.fecha_format;
                this.fecha_fallo = this.concurso_store.fecha_fallo_format;
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
                    'monto' :  _self.participante_store.monto_format,
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
                'monto' : '',
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
            this.participante.monto = this.participante.monto.replace(/,/g, '');
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
            this.participante.monto = this.participante.monto.replace(/,/g, '');
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
            this.$store.commit('concursos/concurso/SET_ACTUALIZANDO', true);
            return this.$store.dispatch('concursos/concurso/setFallo', {
                id: this.id,
                data: {
                    fecha_fallo : this.fecha_fallo,
                }
            })
                .then(data => {
                    this.$store.commit('concursos/concurso/SET_CONCURSO', data);
                    this.$store.commit('concursos/concurso/SET_ACTUALIZANDO', false);
                    this.regresar();
                })
                .catch(error => {
                    this.$store.commit('concursos/concurso/SET_ACTUALIZANDO', false);
                }).finally(() => {
                    this.$store.commit('concursos/concurso/SET_ACTUALIZANDO', false);
                });
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
        },
        actualizando() {
            return this.$store.getters['concursos/concurso/actualizando'];
        },

    }
}
</script>

<style scoped>

div.row {
    font-size: 15px !important;
}

</style>

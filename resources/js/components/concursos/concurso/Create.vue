<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="concurso">Nombre del Concurso:</label>
                            <input class="form-control"
                                    type="text"
                                    placeholder="Nombre del Concurso"
                                    name="concurso"
                                    id="concurso"
                                    data-vv-as="Nombre del concurso"
                                    v-validate="{required: true, max: 255}"
                                    v-model="concurso"
                                    :class="{'is-invalid': errors.has('concurso')}">    
                            <div class="invalid-feedback" v-show="errors.has('concurso')">{{ errors.first('concurso') }}</div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div  class="col-12">
                        <div class="table-responsive">
                            <table class="table  table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th class="encabezado index_corto">
                                            #
                                        </th>
                                        <th class="encabezado">
                                            Participante
                                        </th>
                                        <th class="encabezado c100">
                                            Monto
                                        </th>
                                        <th class="encabezado">
                                            Es Hermes
                                        </th>
                                        <th class="encabezado icono">
                                            <button type="button" class="btn btn-sm btn-outline-success" v-on:click="agregar()">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(participante, i) in this.participantes">
                                        <td>{{i+1}}</td>
                                        <td>
                                           {{participante.nombre}}
                                        </td>
                                        <td style="text-align: right: inherit;">
                                            {{parseFloat(participante.monto).formatMoney(2,'.',',')}}
                                        </td>
                                        <td style="text-align: center">
                                            <input type="checkbox" id="es_hermes" v-model="participante.es_hermes" disabled>
                                        </td>
                                        <td style="text-align: center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" v-on:click="quitar(i)">
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
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" @click="store" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-plus"></i>&nbsp;AGREGAR PARTICIPANTE</h5>
                        <button type="button" class="close" @click="cerrar" aria-label="Close">
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
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="nombre">Monto:</label>
                                        <input
                                                type="text"
                                                name="monto"
                                                v-model="participante.monto"
                                                data-vv-as="Monto"
                                                v-validate="{min_value: 0.1}"
                                                class="form-control"
                                                :class="{'is-invalid': errors.has(`monto`)}"
                                                id="monto"
                                                placeholder="Monto">
                                        <div class="invalid-feedback"
                                                v-show="errors.has(`monto`)">{{ errors.first(`monto`) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="this.es_hermes_seleccionado == 0">
                                <div class="col-md-12" style="text-align: center;">
                                    <div class="form-group">
                                        <label for="nombre">Es Hermes:</label>
                                        <input type="checkbox" id="es_hermes" v-model="participante.es_hermes">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrar"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="button" @click="guardar_participante" class="btn btn-primary">
                            <i class="fa fa-plus"></i>Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "create",
        data(){
            return {
                cargando: false,
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
            iniciar(){
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
            salir() {
                this.iniciar();
                this.$router.go(-1);
            },
            guardar_participante()
            {
                if(this.participante.nombre == '')
                {
                   swal('¡Error!', 'Debe agregar un nombre del participante.', 'error') 
                }
                else if(this.participante.monto <= 0)
                {
                   swal('¡Error!', 'Debe agregar un monto.', 'error') 
                }
                else{
                    if(this.participante.es_hermes == true)
                    {
                        this.es_hermes_seleccionado = 1;
                    }
                    this.participantes.push(this.participante);
                    this.cerrar();
                }
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
                if(this.concurso == '') 
                {
                   swal('¡Error!', 'Debe colocar el nombre del concurso.', 'error') 
                }
                else if(this.participantes.length == 0) 
                {
                   swal('¡Error!', 'Debe agregar al menos un participante.', 'error') 
                }
                else {
                    return this.$store.dispatch('concursos/concurso/store', {
                    concurso: this.concurso,
                    participantes: this.participantes
                    })
                    .then(data=> {
                        this.salir();
                    })
                }
			},
        }
    }
</script>

<style scoped>

</style>

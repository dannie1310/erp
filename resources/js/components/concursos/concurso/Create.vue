<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group error-content">
                            <div class="form-group">
                                <label for="nombre">Nombre Concurso:</label>
                                <input class="form-control"
                                        placeholder="Nombre del Concurso"
                                        name="nombre"
                                        id="nombre"
                                        data-vv-as="Nombre del concurso"
                                        v-validate="{required: true}"
                                        v-model="nombre"
                                        :class="{'is-invalid': errors.has('nombre')}">
                                <div class="invalid-feedback" v-show="errors.has('nombre')">{{ errors.first('nombre') }}</div>
                            </div>
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
                                        <th class="encabezado c350">
                                            Participante
                                        </th>
                                        <th class="encabezado">
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
                                            <input class="form-control"
                                                placeholder="Nombre del Concurso"
                                                name="nombre"
                                                id="nombre"
                                                data-vv-as="Nombre del concurso"
                                                v-validate="{required: true}"
                                                v-model="participante.nombre"
                                                :class="{'is-invalid': errors.has('nombre')}">
                                            <div class="invalid-feedback" v-show="errors.has('nombre')">{{ errors.first('nombre') }}</div>
                                        </td>
                                        <td>
                                            <input
                                                    type="text"
                                                    :name="`monto[${i}]`"
                                                    v-model="participante.monto"
                                                    data-vv-as="Monto"
                                                    v-validate="{required: true,min_value: 0.1}"
                                                    class="form-control"
                                                    :class="{'is-invalid': errors.has(`monto[${i}]`)}"
                                                    id="monto"
                                                    placeholder="'Monto'.${$i}.'">
                                            <div class="invalid-feedback"
                                                    v-show="errors.has(`monto[${i}]`)">{{ errors.first(`monto[${i}]`) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="es_hermes" v-model="participante.es_hermes">
                                            </div>
                                            
                                        </td>
                                        <td style="text-align: center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" v-on:click="quitar(i)" :disabled="participantes.length == 1" >
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
                    <button type="button" @click="validate" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
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
                nombre : '',
                participantes : [
                    {
                        'nombre' : '',
                        'monto' : 0,
                        'es_hermes' : true                    
                    }
                ],
            }
        },
        methods: {
            agregar(){
                console.log(this.participantes)
                var array = {
                    'nombre' : '',
                    'monto' : 0,
                    'es_hermes' : 1
                }
                this.participantes.push(array);
            },
            quitar(index){
                this.participantes.splice(index, 1);
            },
            salir() {
                this.$router.go(-1);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>

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
                        <!-- md-->
                        <div class="row d-none d-sm-block">
                            <div class="col-md-12">
                                <h3>{{concurso_store.nombre}}</h3>
                            </div>
                        </div>
                        <!-- xs-->
                        <div class="row d-block d-sm-none">
                            <div class="col-md-12">
                                <h6><strong>{{concurso_store.nombre}}</strong></h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row d-block d-sm-none">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6" >
                                        <label>Fecha de Apetura:</label>
                                    </div>
                                    <div class="col-6" >
                                        <label>Estado de Apetura:</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6" >
                                        {{concurso_store.fecha_format}}
                                    </div>
                                    <div class="col-6" >
                                        <span class="badge" :style="{'background-color': concurso_store.color_estado_apertura}" style=" font-size: 15px">
                                            {{concurso_store.estado_apertura}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- xs -->
                         <div class="row d-block d-sm-none">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6" >
                                        <label>Fecha de Fallo:</label>
                                    </div>
                                    <div class="col-6" >
                                        <label>Estado de Fallo:</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6" >
                                        {{concurso_store.fecha_fallo_format}}
                                    </div>
                                    <div class="col-6" >
                                        <span class="badge" :style="{'background-color': concurso_store.color_estado_fallo}" style=" font-size: 15px">
                                            {{concurso_store.estado_fallo}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- md -->
                        <div class="d-none d-md-block d-lg-none">
                            <div class="row ">
                                <div class="col-md-3 " >
                                    <label >Fecha de Apetura:</label>
                                </div>
                                <div class="col-md-2" >
                                    {{concurso_store.fecha_format}}
                                </div>
                                <div class="col-md-3 " >
                                    <label for="fecha">Estado de Apetura:</label>
                                </div>
                                <div class="col-md-2" >
                                    <span class="badge" :style="{'background-color': concurso_store.color_estado_apertura}" style=" font-size: 15px">
                                        {{concurso_store.estado_apertura}}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3" >
                                    <label for="fecha">Fecha de Fallo:</label>
                                </div>
                                <div class="col-md-2" >
                                    {{concurso_store.fecha_fallo_format}}
                                </div>
                                <div class="col-md-3 " >
                                    <label for="fecha">Estado de Fallo:</label>
                                </div>
                                <div class="col-md-2" >
                                    <span class="badge" :style="{'background-color': concurso_store.color_estado_fallo}" style=" font-size: 15px">
                                        {{concurso_store.estado_fallo}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- lg -->
                        <div class="d-none d-lg-block d-xl-none">
                            <div class="row ">
                                <div class="col-lg-2 " >
                                    <label >Fecha de Apetura:</label>
                                </div>
                                <div class="col-lg-2" >
                                    {{concurso_store.fecha_format}}
                                </div>
                                <div class="col-lg-2 " >
                                    <label for="fecha">Estado de Apetura:</label>
                                </div>
                                <div class="col-lg-1" >
                                    <span class="badge" :style="{'background-color': concurso_store.color_estado_apertura}" style=" font-size: 15px">
                                        {{concurso_store.estado_apertura}}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2" >
                                    <label for="fecha">Fecha de Fallo:</label>
                                </div>
                                <div class="col-lg-2" >
                                    {{concurso_store.fecha_fallo_format}}
                                </div>
                                <div class="col-lg-2 " >
                                    <label for="fecha">Estado de Fallo:</label>
                                </div>
                                <div class="col-lg-1" >
                                    <span class="badge" :style="{'background-color': concurso_store.color_estado_fallo}" style=" font-size: 15px">
                                        {{concurso_store.estado_fallo}}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- xl -->
                        <div class="d-none d-xl-block">
                            <div class="row ">
                                <div class="col-lg-2 " >
                                    <label >Fecha de Apetura:</label>
                                </div>
                                <div class="col-lg-2" >
                                    {{concurso_store.fecha_format}}
                                </div>
                                <div class="col-lg-2 " >
                                    <label for="fecha">Estado de Apetura:</label>
                                </div>
                                <div class="col-lg-1" >
                                    <span class="badge" :style="{'background-color': concurso_store.color_estado_apertura}" style=" font-size: 15px">
                                        {{concurso_store.estado_apertura}}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2" >
                                    <label for="fecha">Fecha de Fallo:</label>
                                </div>
                                <div class="col-lg-2" >
                                    {{concurso_store.fecha_fallo_format}}
                                </div>
                                <div class="col-lg-2 " >
                                    <label for="fecha">Estado de Fallo:</label>
                                </div>
                                <div class="col-lg-1" >
                                    <span class="badge" :style="{'background-color': concurso_store.color_estado_fallo}" style=" font-size: 15px">
                                        {{concurso_store.estado_fallo}}
                                    </span>
                                </div>
                            </div>
                        </div>



                        <br>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group error-content">
                                    <label for="numero_licitacion">Número de Licitación:</label>
                                    <div>
                                        {{concurso_store.numero_licitacion}}
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="entidad_licitante">Entidad Licitante:</label>
                                    <div>
                                        {{concurso_store.entidad_licitante}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group error-content">
                                    <label for="concurso">Nombre del Concurso:</label>
                                    <div>
                                        {{concurso_store.nombre}}
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
                                                <th class="encabezado index_corto" rowspan="2">
                                                    #
                                                </th>
                                                <th class="encabezado c30" rowspan="2">
                                                    G
                                                </th>
                                                <th class="encabezado" rowspan="2">
                                                    Participante
                                                </th>
                                                <th class="encabezado c90" rowspan="2">
                                                    Monto Sin IVA
                                                </th>
                                                 <th class="encabezado" colspan="5">
                                                    % en relación a
                                                </th>

                                            </tr>
                                            <tr>
                                                <th>1er Lugar</th>
                                                <th>Promedio</th>
                                                <th>Hermes</th>
                                                <th>Ganador</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(participante, i) in this.concurso_store.participantes_informe.data"
                                                :style="participante.es_empresa_hermes?`background-color:#7DB646`:participante.nombre =='PROMEDIO'?`background-color:#f9cb98`:``"
                                            >
                                                <td >{{participante.i}}</td>
                                                <td style="text-align: center">
                                                    <i class="fa fa-trophy" v-if="participante.es_ganador == 'X'" />
                                                </td>
                                                <td :style="participante.nombre =='PROMEDIO'?`text-align:right`:``">
                                                {{participante.nombre}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{parseFloat(participante.monto).formatMoney(2,'.',',')}}
                                                </td>

                                                <td style="text-align: right;">
                                                    {{participante.porcentaje_vs_primer_lugar}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{participante.porcentaje_vs_promedio}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{participante.porcentaje_vs_hermes}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{participante.porcentaje_vs_ganador}}
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

export default {
    name: "show-concurso",
    props: ['id'],
    data(){
        return {
            cargando: false,
            participante :
                {
                    'nombre' : '',
                    'monto' : 0,
                    'es_empresa_hermes' : false,
                    'id_concurso' : '',
                    'notificar' : true,
                },
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            if(this.concurso_store == null || this.concurso_store.id != this.id)
            {
                this.cargando = true;
                this.$store.dispatch('concursos/concurso/find', {
                    id: this.id,
                    params:{include: 'participantes_informe'}
                }).then(data => {
                    this.$store.commit('concursos/concurso/SET_CONCURSO', data);

                }).catch(error => {
                })
                .finally(()=>{
                    this.cargando=false;
                });
            }
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

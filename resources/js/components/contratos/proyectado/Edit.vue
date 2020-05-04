<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" title="Editar Contrato Proyectado">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> EDITAR CONTRATO PROYECTADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group error-content">
                                            <label class="col-form-label">Fecha:</label>
                                            <datepicker 
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            />
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <!-- <hr> -->
                                <div class="row">
                                        <div class="col-12">
                                            <h6><b>Fechas Límite</b></h6>
                                        </div>
                                    </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cotizacion">Cotización</label>
                                        <input
                                                type="date"
                                                name="cotizacion"
                                                id="cotizacion"
                                                class="form-control"
                                                v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                data-vv-as="Cotización"
                                                :class="{'is-invalid': errors.has('cotizacion')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('cotizacion')">{{ errors.first('cotizacion') }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="contratacion">Contratación</label>
                                        <input
                                                type="date"
                                                name="contratacion"
                                                id="contratacion"
                                                class="form-control"
                                                v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                data-vv-as="Contratación"
                                                :class="{'is-invalid': errors.has('contratacion')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('contratacion')">{{ errors.first('contratacion') }}</div>
                                    </div>
                                </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                               <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="descripcion" class="col-sm-2 col-form-label">Descripción:</label>
                                        <div class="col-sm-12">
                                            <input
                                                type="text"
                                                name="descripcion"
                                                data-vv-as="Descripcion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="Descripcion"
                                                :class="{'is-invalid': errors.has('descripcion')}">
                                            <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 ">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </span>
</template>
<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
export default {
    
    name: "contrato-proyectado-editar",
    components: {Datepicker, es},
    props: ['id'],
    data() {
        return {
            cargando: false,
            es:es,            
        }
    },
    methods: {
        save() {
            if(this.service.descripcion == this.res.descripcion && this.service.tipo == this.res.nivel_padre && this.service.numero_parte == this.res.numero_parte && this.service.unidad == this.res.unidad)
            {
                swal('¡Error!', 'Favor de ingresar datos actualizados.', 'error')
            }else{

                return this.$store.dispatch('cadeco/material/update', {
                id: this.id,
                data: this.service,
            })
                .then(() => {
                   return this.$store.dispatch('cadeco/material/paginate', { params: {scope:['servicios','insumos'], sort: 'descripcion', order: 'asc'}})
                    .then(data => {
                        this.$store.commit('cadeco/material/SET_MATERIALES', data.data);
                        this.$store.commit('cadeco/material/SET_META', data.meta);
                    })
                   }).finally( ()=>{
                       $(this.$refs.modal).modal('hide');
                   });
            }
        },   
        formatoFecha(date)
        {
                return moment(date).format('DD/MM/YYYY');
        },    
        find()
        {
            console.log('Comienza a buscar');
            this.cargando = true;
            // this.res = '';
            // // this.id = servicio;    

            //     this.$store.commit('cadeco/unidad/SET_UNIDAD', null);
            //     return this.$store.dispatch('cadeco/material/find', {
            //         id: servicio,
            //         params: {scope: 'servicios'}
            //     }).then(data => {

            //         this.$store.commit('cadeco/material/SET_MATERIAL', data);
            //         this.res = data;
            //         this.service.tipo = this.res.nivel_padre;
            //         this.service.descripcion = this.res.descripcion;
            //         this.service.unidad = this.res.unidad;
            //         this.service.numero_parte = this.res.numero_parte;                                        
                    $(this.$refs.modal).appendTo('body');
                    $(this.$refs.modal).modal('show');
            //     }).finally(() => {
                    this.cargando = false;
            //     })
        },
        getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index', {
                    params: {sort: 'unidad',  order: 'asc'}
                })
                    .then(data => {
                        this.unidades= data.data;
                    })
        },
         getFamiliasMOyS(){
                return this.$store.dispatch('cadeco/familia/index', {
                    params: {sort: 'descripcion',  order: 'asc', scope:'tipo:2'}
                })
                    .then(data => {
                        this.familias_moys= data.data;                        
                    })
        },
        validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
            },
    },
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>
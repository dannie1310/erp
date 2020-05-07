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
                                            <label class="col-form-label">Fecha</label>
                                            <datepicker v-model="contrato.fecha_date"
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
                                <hr>
                                <div class="row">
                                        <div class="col-12">
                                            <h6><b>Fechas Límite</b></h6>
                                        </div>
                                    </div>
                                <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cotizacion">Cotización</label>
                                        <input  v-model="contrato.cumplimiento"
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
                                        <input  v-model="contrato.vencimiento"
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
                                        <label for="referencia" class="col-sm-2 col-form-label">Referencia</label>
                                        <div class="col-sm-12">
                                            <input
                                                v-model="contrato.referencia"
                                                type="text"
                                                name="referencia"
                                                data-vv-as="Referencia"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="referencia"
                                                placeholder="Referencia"
                                                :class="{'is-invalid': errors.has('referencia')}">
                                            <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
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
            contrato: [],
            fecha: '',
            vencimiento: '',
            cumplimiento: '',
            referencia: ''

        }
    },
    methods: {       
        
        save() {
            
            if(this.contrato.fecha_date == this.fecha && this.contrato.vencimiento == this.vencimiento && this.contrato.cumplimiento == this.cumplimiento && this.contrato.referencia == this.referencia)
            {
                swal('¡Error!', 'Favor de ingresar datos actualizados.', 'error')
            }else{

                return this.$store.dispatch('contratos/contrato-proyectado/update', {
                id: this.id,
                data: this.contrato,
            })
                .then(() => {
                   return this.$store.dispatch('contratos/contrato-proyectado/paginate', { params: {include: 'areasSubcontratantes', sort: 'numero_folio', order: 'DESC'}})
                    .then(data => {
                        this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO_PROYECTADO', data.data);
                        this.$store.commit('contratos/contrato-proyectado/SET_META', data.meta);
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
            this.cargando = true;
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO_PROYECTADOS', null);
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: this.id
                }).then(data => {
                    this.contrato = data;
                    this.fecha = data.fecha_date;
                    this.referencia = data.referencia;
                    this.cumplimiento = data.cumplimiento;
                    this.vencimiento = data.vencimiento;
                    $(this.$refs.modal).appendTo('body');
                    $(this.$refs.modal).modal('show');
                }).finally(() => {
                    this.cargando = false;                    
                })
        },
        validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
            },
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>
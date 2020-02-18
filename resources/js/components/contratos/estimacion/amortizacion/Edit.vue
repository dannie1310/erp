<template>
    <span>
          <button type="button" @click="find()" class="btn btn-primary float-right" :disabled="estado !== 0" v-if="$root.can('actualizar_amortizacion_anticipo')"  title="Editar">
                    Amortización de Anticipo
                </button>
             <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalAmortizacion" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-edit"></i> EDITAR AMORTIZACIÓN DE ANTICIPO</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="update">
                                <div class="modal-body">
                                    <div class="row">

                                            <div class="form-group row error-content col-md-12">
                                                <label for="campo" class="col-sm-5 col-form-label">Amortización de Anticipo </label>
                                                 <label for="campo" class="col-sm-3 col-form-label">{{anticipo}}</label>

                                                <div class="col-sm-4" style="text-align:right;">
                                                    <input
                                                        type="number"
                                                        step="0.0001"
                                                        name="campo"
                                                        v-model="campo"
                                                        data-vv-as="Anticipo"
                                                        v-validate="{required: true, min_value:0}"
                                                        class="form-control"
                                                        id="campo"
                                                        placeholder="Anticipo"
                                                        :class="{'is-invalid': errors.has('campo')}">
                                                </div>
                                            </div>

                                        <!-- </div> -->
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 ">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
export default {
    name: "amortizacion-edit",
    props: ['id', 'estimacion_anticipo'],
    data() {
        return {
            descuentos:[],
            cargando:false,
            campo:'',
            anticipo:'',
            estado:''
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
            $(this.$refs.modalAmortizacion).modal('hide');
        },
        update() {
            if(this.campo == this.estimacion_anticipo.estimacion.monto_anticipo_aplicado)
            {
                swal('Atención', 'El valor de la Amortización de Anticipo es el mismo.', 'warning');
            }
            else{
                return this.$store.dispatch('contratos/estimacion/update', {
                    id: this.id,
                    params: this.$data
                })
                    .then(data => {
                        $(this.$refs.modalAmortizacion).modal('hide');
                         this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                         return this.$store.dispatch('contratos/estimacion/showEstimacionTable', {
                         id: this.id,
                    }).then(data => {

                        this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);
                        this.cargando = false;
                        this.estado = data.estimacion.estado;

                })
                    })
            }
        },

        find() {
            this.anticipo = this.estimacion_anticipo.estimacion.anticipo;
            this.campo = this.estimacion_anticipo.estimacion.monto_anticipo_aplicado;


                    $(this.$refs.modalAmortizacion).modal('show');
        },
    }

}
</script>

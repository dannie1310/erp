<template>
    <span>
        <button @click="init()" v-if="$root.can('rechazar_variacion_volumen')" class="btn btn-warning">
            <i class="fa fa-thumbs-down"></i>
            Rechazar
        </button>
       <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-dialog-centered modal-md" role="document">
               <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCuenta">Rechazar Solicitud de Variación de Volumen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="solicitud">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Tipo de Solicitud de Cambio: </b></label>
                                        {{ solicitud.tipo_orden }}
                                        </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Número de Folio: </b></label>
                                        # {{ solicitud.numero_folio }}
                                        </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Usuario que Solicita: </b></label>
                                        {{ solicitud.usuario.nombre }}
                                        </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Fecha de solicitud: </b></label>
                                        {{ solicitud.fecha_solicitud }}
                                        </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Area Solicitante: </b></label>
                                        {{ solicitud.area_solicitante }}
                                        </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Motivo:</b></label>
                                        {{ solicitud.motivo  }}
                                        </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                            <label><b>Estatus:</b></label>
                                        {{ solicitud.estatus  }}
                                        </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="motivo" >Motivo de Rechazo:</label>
                                </div>
                            </div>
                            <div>
                                <div class="col-md-12">
                                    <textarea
                                        name="motivo"
                                        id="motivo"
                                        class="form-control"
                                        v-model="motivo"
                                        v-validate="{required: true}"
                                        data-vv-as="Motivo"
                                        :class="{'is-invalid': errors.has('motivo')}"
                                    ></textarea>
                                    <div class="error-label" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i>Cerrar</button>
                                <button type="submit" class="btn btn-warning"><i class="fa fa-thumbs-down"></i>Rechazar</button>
                         </div>
                   </form>
               </div>
           </div>
       </div>
    </span>
</template>

<script>
export default {
     name: "rechazar-variacion-volumen",
    props: ['id'],
    data() {
        return {
            motivo:'',
            cargando:false,
        }
    },
    methods: {
        init() {
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
            this.$validator.reset();
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.rechazar();
                }
            });
        },
        rechazar(){
            return this.$store.dispatch('control-presupuesto/variacion-volumen/rechazar', {
                id: this.id,
                params: {data:[this.$data.motivo]}
            }).then(data => {
                this.$emit('created', data);
                    $(this.$refs.modal).modal('hide');
            }) .finally(() => {
                this.cargando = false;
            })
        }

    },
    computed: {
        solicitud() {
            return this.$store.getters['control-presupuesto/variacion-volumen/currentVariacion']
        },
    },

}
</script>

<style>

</style>

<template>
    <span>
        <button @click="iniciar()" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR PRESUPUESTO CONTRATISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <presupuesto-proveedor-partial-show v-bind:id="this.id" />
                                </div>
                            </div>
                            <br />
                            <div class="row" v-if="presupuesto">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-md-2 col-form-label">Motivo de eliminación:</label>
                                        <div class="col-md-10">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                class="form-control"
                                                v-model="motivo"
                                                v-validate="{required: true}"
                                                data-vv-as="Motivo"
                                                :class="{'is-invalid': errors.has('motivo')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0"><i class="fa fa-trash"></i>Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import PresupuestoProveedorPartialShow from './partials/PartialShow'
    export default {
    name: "presupuesto-eliminar",
    props: ['id'],
    components : {PresupuestoProveedorPartialShow},
    data() {
        return {
            cargando: false,
            presupuesto: [],
            invitacion: [],
            motivo: ''
        }
    },
    methods: {
        iniciar() {
            this.cargando = true;
            this.motivo = '';
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show')
            this.cargando = false;
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.destroy()
                }
            });
        },
        destroy() {
            return this.$store.dispatch('contratos/presupuesto/deletePresupuestoProveedor', {
                id: this.id,
                params: {data: this.motivo}
            })
            .then(() => {
                $(this.$refs.modal).modal('hide');
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/paginate', {
                    params: {include: ['transaccion','cotizacion'], scope: ['cotizacionRealizada','invitadoAutenticado']},
                }).then(data => {
                    this.$store.commit('padronProveedores/invitacion/SET_INVITACIONES', data.data);
                    this.$store.commit('padronProveedores/invitacion/SET_META', data.meta);
                    this.cargando = false;
                })
            })
        },
    }
}
</script>

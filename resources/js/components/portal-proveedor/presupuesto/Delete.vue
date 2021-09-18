<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form role="form" @submit.prevent="validate">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <presupuesto-proveedor-partial-show v-bind:id="id"  @created="iniciar" />
                                </div>
                            </div>
                            <br />
                            <div class="row">
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
                        <div class="card-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                                <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || fin_carga == 0"><i class="fa fa-trash"></i>Eliminar</button>
                            </div>
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
            motivo: '',
            fin_carga: 0
        }
    },
    methods: {
        iniciar() {
            this.fin_carga = 1
        },
        salir() {
            this.$router.push({name: 'cotizacion-proveedor'});
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
                this.salir();
            })
        },
    }
}
</script>

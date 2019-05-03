<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE TIPO CUENTA CONTABLE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="tipo" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Descripción de Tipo Cuenta Contable</label>
                                    <div class="col-sm-8">
                                        <p class="form-control">{{ tipo.descripcion }}</p>
                                    </div>
                                </div>
                                <div class="form-group row error-content">
                                    <label for="id_naturaleza_poliza" class="col-sm-4 col-form-label">Naturaleza de Cuenta</label>
                                    <div class="col-sm-8">
                                        <select
                                                name="id_naturaleza_poliza"
                                                id="id_naturaleza_poliza"
                                                data-vv-as="Naturaleza"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                :value="tipo.id_naturaleza_poliza"
                                                @input="updateAttribute"
                                                :class="{'is-invalid': errors.has('id_naturaleza_poliza')}"
                                        >
                                            <option value>-- Seleccione --</option>
                                            <option v-for="item in naturalezas" :value="item.id">{{ item.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_naturaleza_poliza')">{{ errors.first('id_naturaleza_poliza') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "tipo-cuenta-contable-edit",
        props: ['id'],
        data() {
            return {
                cargando: false
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            tipo() {
                return this.$store.getters['contabilidad/tipo-cuenta-contable/currentTipo']
            },

            naturalezas() {
                return this.$store.getters['contabilidad/naturaleza-poliza/naturalezas'];
            },
        },

        methods: {
            find(id) {
                this.$store.commit('contabilidad/tipo-cuenta-contable/SET_TIPO', null)
                this.cargando = true;
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/find', {
                    id: id,
                    params : {include: ['naturaleza']}
                })
                    .then(data => {
                        this.$store.commit('contabilidad/tipo-cuenta-contable/SET_TIPO', data)
                        if(data.naturaleza){
                            this.id_naturaleza_poliza = data.naturaleza.id_naturaleza_poliza
                        }
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            update() {
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/update', {
                    id: this.id,
                    data: this.tipo,
                    params : {include: ['naturaleza', 'usuario']}
                })
                    .then(data => {
                        this.$store.commit('contabilidad/tipo-cuenta-contable/UPDATE_TIPO', data);
                        $(this.$refs.modal).modal('hide');
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            updateAttribute(e) {
                return this.$store.commit('contabilidad/tipo-cuenta-contable/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>
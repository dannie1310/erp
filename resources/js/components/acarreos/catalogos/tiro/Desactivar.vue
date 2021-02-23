<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-close"></i> DESACTIVAR TIRO</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="tiro">
                        <div class="card">
                           <div class="card-body">
                               <div class="row">
                                   <div class="col-md-2">
                                       <div class="form-group">
                                           <h6><b>Tiro:</b></h6>
                                       </div>
                                   </div>
                                   <div class="col-md-4">
                                       <div class="form-group">
                                           <h6>{{tiro.descripcion}}</h6>
                                       </div>
                                   </div>
                                   <div class="col-md-2">
                                       <div class="form-group">
                                           <h6><b>Estado:</b></h6>
                                       </div>
                                   </div>
                                   <div class="col-md-4">
                                       <div class="form-group">
                                           <span class="badge" :style="{'background-color': tiro.estado_color}">{{ tiro.estado_format }}</span>
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-3" v-if="tiro.concepto">
                                       <div class="form-group">
                                           <h6><b>Concepto:</b></h6>
                                       </div>
                                   </div>
                                   <div class="col-md-9" v-if="tiro.concepto">
                                       <div class="form-group">
                                           <h6 :title="tiro.path_concepto">{{tiro.concepto.descripcion}}</h6>
                                       </div>
                                   </div>
                               </div>
                           </div>
                        </div>
                        <div class="card" v-if="tiro.estado == 1">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="desactivar" :disabled="errors.count() > 0" v-if="tiro">
                            <i class="fa fa-save"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    export default {
        name: "desactivar_tiro",
        props: ['id'],
        data() {
            return {
                motivo : '',
                carga : false
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.motivo = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/tiro/SET_TIRO', null);
                return this.$store.dispatch('acarreos/tiro/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/tiro/SET_TIRO', data);
                    this.carga = true;
                })
            },
            desactivar() {
                return this.$store.dispatch('acarreos/tiro/desactivar', {
                    id: this.id,
                    params: {motivo: this.motivo}})
                    .then((data) => {
                        this.$store.commit('acarreos/tiro/UPDATE_TIRO', data);
                        $(this.$refs.modal).modal('hide');
                    })
            }
        },
        computed: {
            tiro() {
                return this.$store.getters['acarreos/tiro/currentTiro']
            }
        }
    }
</script>

<style scoped>

</style>

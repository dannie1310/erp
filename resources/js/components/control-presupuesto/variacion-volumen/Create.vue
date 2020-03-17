<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Seleccionar Tarjeta
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_almacen" class="col-sm-2 col-form-label">tarjetas: </label>
                                        <div class="col-sm-10">
                                            <model-list-select
                                                :disabled="cargando"
                                                :onchange="changeSelect()"
                                                name="id_tarjeta"
                                                v-model="id_tarjeta"
                                                option-value="id"
                                                option-text="descripcion"
                                                :list="tarjetas"
                                                :placeholder="!cargando?'Seleccionar o buscar tarjeta por descripcion':'Cargando...'"
                                                :isError="errors.has(`id_tarjeta`)">
                                            </model-list-select>
                                            <div class="invalid-feedback" v-show="errors.has('id_tarjeta')">{{ errors.first('id_tarjeta') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "variacion-volumen-create",
    components: {ModelListSelect},
    props: [],
    data() {
        return {
            cargando: false,
            id_tarjeta: '',
        }
    },
    methods: {
        getTarjetas(){
            this.cargando = true;
            return this.$store.dispatch('control-presupuesto/tarjeta/index', {
                params: {}
            })
            .then(data => {
                this.$store.commit('control-presupuesto/tarjeta/SET_TARJETAS', data);
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        changeSelect(){
            
        },
    },
    computed: {
        tarjetas(){
            return this.$store.getters['control-presupuesto/tarjeta/tarjetas'];
        },
    },
    mounted() {
        this.getTarjetas();
    }
}
</script>

<style>

</style>
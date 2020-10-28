<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="cargando">
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
                <span v-else>
                    <div class="row" v-if="conceptos.length>0" >
                        <div class="col-md-12">
                            <div class="table-responsive">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="index_corto"></th>
                                            <th class="index_corto"></th>
                                            <th class="th_c200" >Clave Concepto</th>
                                            <th >Descripción</th>
                                            <th >Cantidad</th>
                                            <th >Unidad</th>
                                            <th >Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(concepto, i) in conceptos" >
                                        <tr >
                                            <td>

                                                <small class="label bg-secondary" style="padding: 3px 2px 3px 5px">
                                                    <i class="fa fa-plus"></i>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="label bg-success" v-if="concepto.activo == 1" style="padding: 3px 2px 3px 5px">
                                                    <i class="fa fa-check"></i>
                                                </small>
                                                <small class="label bg-danger" v-else-if="concepto.activo == 0" style="padding: 2px 2px 2px 5px">
                                                    <i class="fa fa-times"></i>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="form-group error-content">
                                                    <input
                                                        maxlength="140"
                                                        type="text"
                                                        data-vv-as="Clave de Concepto"
                                                        v-validate="{required:true}"
                                                        class="form-control"
                                                        :name="`clave_concepto[${i}]`"
                                                        placeholder="Clave de Concepto"
                                                        v-model="concepto.clave_concepto"
                                                        :class="{'is-invalid': errors.has(`clave_concepto[${i}]`)}"
                                                        >
                                                    <div class="invalid-feedback" v-show="errors.has(`clave_concepto[${i}]`)">{{ errors.first(`clave_concepto[${i}]`) }}</div>
                                                </div>

                                            </td>
                                            <td>{{concepto.descripcion}}</td>
                                            <td>{{concepto.cantidad_presupuestada}}</td>
                                            <td>{{concepto.unidad}}</td>
                                            <td></td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-else >
                        No hay conceptos cargados.
                    </div>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "index-concepto",
    data(){
        return{
            conceptos_listados:[],
            cargando: false,
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('presupuesto/concepto/list', {
                nivel_padre: '',
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando = false;
            })
        },
    },
    computed: {
        conceptos(){
            return this.$store.getters['presupuesto/concepto/conceptos'];
        },
    }
}
</script>

<style scoped>

</style>

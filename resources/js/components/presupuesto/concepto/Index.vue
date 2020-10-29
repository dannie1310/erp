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
                                 <form role="form" @submit.prevent="store">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="index_corto"></th>
                                                <th class="index_corto"></th>
                                                <th class="th_c200" >Clave Concepto</th>
                                                <th >Descripción</th>
                                                <th >Cantidad</th>
                                                <th >Unidad</th>
                                                <th ></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <template v-for="(concepto, i) in conceptos" >
                                            <tr v-if="concepto.visible==1">
                                                <td>
                                                    <span v-if="concepto.tiene_hijos">
                                                        <button @click="cargaHijos(concepto.id)" :disabled="cargando_hijos" v-if="concepto.expandido == 0 && concepto.hijos_cargados == 0" type="button" class="btn btn-sm-sp btn-secondary">
                                                            <i class="fa fa-spin fa-spinner" v-if="cargando_hijos"></i>
                                                            <i class="fa fa-plus" v-else></i>
                                                        </button>
                                                        <button @click="muestraHijos(concepto)" :disabled="cargando_hijos" v-else-if="concepto.expandido == 0 && concepto.hijos_cargados == 1" type="button" class="btn btn-sm-sp btn-outline-secondary">
                                                            <i class="fa fa-spin fa-spinner" v-if="cargando_hijos"></i>
                                                            <i class="fa fa-plus" v-else></i>
                                                        </button>
                                                        <button @click="ocultaHijos(concepto)" :disabled="cargando_hijos" v-else type="button" class="btn btn-sm-sp btn-outline-secondary">
                                                            <i class="fa fa-spin fa-spinner" v-if="cargando_hijos"></i>
                                                            <i class="fa fa-minus" v-else></i>
                                                        </button>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button @click="toggleActivo(concepto.id)" :disabled="cargando_hijos" v-if="concepto.activo == 1" type="button" class="btn btn-sm-sp btn-success">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <button @click="toggleActivo(concepto.id)" :disabled="cargando_hijos" v-else type="button" class="btn btn-sm-sp btn-danger">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <input type="hidden" :value="concepto.id" :name="`id_concepto[${i}]`" >
                                                    <input
                                                        :value="concepto.clave_concepto"
                                                        @input="actualizaClave($event,concepto.id)"
                                                        maxlength="140"
                                                        type="text"
                                                        data-vv-as="Clave de Concepto"
                                                        class="form-control"
                                                        :name="`clave_concepto[${i}]`"
                                                        placeholder="Clave de Concepto"
                                                        :class="{'is-invalid': errors.has(`clave_concepto[${i}]`)}"
                                                    >
                                                </td>
                                                <td>{{concepto.anidacion}}{{concepto.descripcion}}</td>
                                                <td style="text-align: right">{{concepto.cantidad_presupuestada}}</td>
                                                <td>{{concepto.unidad}}</td>
                                                <td></td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
                                    <button type="submit" class="btn btn-secondary pull-right" >
                                        <span v-if="cargando">
                                            <i class="fa fa-spin fa-spinner"></i>
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-save"></i>Actualizar
                                        </span>
                                    </button>
                                </form>
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
            datos_store : {},
            cargando: false,
            cargando_hijos: false,
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('presupuesto/concepto/getRaiz', {
                id_padre: '0',
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando = false;
            })
        },
        cargaHijos(id) {
            this.cargando_hijos = true;
            return this.$store.dispatch('presupuesto/concepto/getHijos', {
                id_padre: id,
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando_hijos = false;
            })
        },
        toggleActivo(id) {
            this.cargando_hijos = true;
            return this.$store.dispatch('presupuesto/concepto/toggleActivo', {
                id: id,
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando_hijos = false;
            })
        },
        ocultaHijos(concepto) {
            this.cargando_hijos = true;
            return this.$store.dispatch('presupuesto/concepto/ocultaHijos', {
                id_padre: concepto.id,
                nivel: concepto.nivel,
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando_hijos = false;
            })
        },
        muestraHijos(concepto) {
            this.cargando_hijos = true;
            return this.$store.dispatch('presupuesto/concepto/muestraHijos', {
                id_padre: concepto.id,
                nivel: concepto.nivel,
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando_hijos = false;
            })
        },
        actualizaClave(e,id){
            this.$store.commit('presupuesto/concepto/UPDATE_CONCEPTO', {clave_concepto : e.target.value, id : id})
        },
        store() {
            return this.$store.dispatch('presupuesto/concepto/actualizaClaves', {
                data : this.$store.getters['presupuesto/concepto/conceptos']
            })
            .then((data) => {
            }).finally(()=> {
            });
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
.btn-sm-sp, .btn-group-sm > .btn {
    padding: 2px 2px 2px 5px;
    font-size: 0.7rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}
.table th, .table td {
    padding: 2px 2px 2px 2px;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

</style>

<template>
    <span>
        <div class="col-12" v-if="suministrados" style="height:350px;">
            <div class="invoice p-3 mb-3">
                <div class="row" v-if="suministrados">
                    <div class="table-responsive col-12">
                        <table class="table table-striped table-fixed">
                            <thead>
                                <tr>
                                    <th style="width:10%;">#</th>
                                    <th style="width:80%;">Material</th>
                                    <th style="width:10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(material, i) in suministrados">
                                    <td style="width:10%;">{{i+1}}</td>
                                    <td style="width:80%; text-align: left">{{material.material.descripcion}}</td>
                                    <td style="width:10%;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="eliminar(material)" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-10">
                        <MaterialSelect
                            :scope="scope"
                            :name="material"
                            v-model="material"
                            data-vv-as="Material"
                            v-validate="{required: true}"
                            ref="MaterialSelect"
                            :disableBranchNodes="false"/>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" @click="registrarMaterial()">Registrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import MaterialSelect from "../../../../cadeco/material/SelectAutocomplete"
export default {
    name: "proveedor-contratista-material-tab",
    components: {MaterialSelect},
    props: ['id_empresa'],
    data(){
        return {
            materiales:[],
            material:[],
            scope: ['tipos:1'],
        }
    },
    methods: {
        eliminar(material){
            return this.$store.dispatch('cadeco/suministrado/delete', {
                id:material.id_empresa,
                params: {data: {id_empresa:material.id_empresa,id_material:material.id_material}}
            })
            .then(() => {
                this.$store.commit('cadeco/suministrado/DELETE_SUMINISTRADO', material.id_material)
            })
        },
        registrarMaterial(){
            return this.$store.dispatch('cadeco/suministrado/store', this.suministrado())
            .then((data) => {
                this.$store.commit('cadeco/suministrado/INSERT_SUMINISTRADO', data);
            });
        },
        suministrado(){
            return {
                id_empresa:this.id_empresa,
                id_material:this.material.id
            }
        },
    },
    computed: {
        suministrados(){
            return this.$store.getters['cadeco/suministrado/suministrados'];
        }
    },

}
</script>

<style>
.align{
    text-align: left;
}
.table-fixed tbody {
    display:block;
    height:218px;
    overflow:auto;
}
.table-fixed thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: start;
}
</style>
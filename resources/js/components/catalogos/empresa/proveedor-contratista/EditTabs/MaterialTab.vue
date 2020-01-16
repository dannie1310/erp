<template>
    <span>
        <div class="col-12" v-if="suministrados" style="height:360px;">
            <div class="invoice p-3 mb-3">
                <div class="row" v-if="suministrados">
                   
                        <div class="col-md-10" v-if="$root.can('registrar_material_proveedor')">
                            <MaterialSelect
                                :scope="scope"
                                :name="material"
                                v-model="material"
                                data-vv-as="Material"
                                v-validate="{required: true}"
                                ref="MaterialSelect"
                                :disableBranchNodes="false"/>
                        </div>
                        <div class="col-md-2" v-if="$root.can('registrar_material_proveedor')">
                            <button type="submit" class="btn btn-primary float-right" @click="registrarMaterial()" :disabled="material.length == 0"><i class="fa fa-plus"></i>  Registrar</button>
                        </div>
                    
                    <div class="table-responsive col-12"><br>
                        <table class="table table-striped table-fixed">
                            <thead>
                                <tr>
                                    <th style="width:10%;">#</th>
                                    <th style="width:80%;">Material</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(material, i) in suministrados">
                                    <td style="width:10%;">{{i+1}}</td>
                                    <td style="width:100%; text-align: left">{{material.material.descripcion}}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="eliminar(material)" title="Eliminar" v-if="$root.can('eliminar_material_proveedor')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
            material:[],
            scope: ['insumos', 'suministrables'],
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
            if(this.suministrados.findIndex(x => parseInt(x.id_material) === this.material.id) === -1){
                return this.$store.dispatch('cadeco/suministrado/store', this.suministrado())
                .then((data) => {
                    this.$store.commit('cadeco/suministrado/INSERT_SUMINISTRADO', data);
                    this.material = [];
                });
            }else{
                swal("Material registrado previamente.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            text: 'Aceptar',
                            closeModal: true,
                        }
                    }
                })
            }
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
    height:210px;
    overflow:auto;
}
.table-fixed thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: start;
}
</style>
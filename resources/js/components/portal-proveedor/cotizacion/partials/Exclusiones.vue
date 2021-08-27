<template>
    <span>
        <br>
        <div class="card-header">
            <label ><i class="fa fa-th-list icon"></i>Exclusiones</label>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="bg-gray-light index_corto">#</th>
                    <th class="bg-gray-light">Nombre(s)</th>
                    <th class="bg-gray-light">Apellido Paterno</th>
                    <th class="bg-gray-light">Apellido Materno</th>
                    <th class="bg-gray-light">Nacionalidad</th>
                    <th class="bg-gray-light">CURP ó Número de Identificación</th>
                    <th class="bg-gray-light icono">
                        <button type="button" class="btn btn-sm btn-outline-success" @click="agregarRepresentanteLegal" :disabled="cargando">
                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                            <i class="fa fa-plus" v-else></i>
                        </button>
                    </th>
                </tr>
                </thead>
                <tbody>
                <!--
                <tr v-for="(representante_legal, i) in representantes_legales.data" >
                    <td class="index_corto">{{ i + 1 }}</td>
                    <td>
                        <input class="form-control"
                               :name="`nombre_rl[${i}]`"
                               :data-vv-as="`'Nombre ${i + 1}'`"
                               v-model="representante_legal.nombre"
                               :class="{'is-invalid': errors.has(`nombre_rl[${i}]`)}"
                               v-validate="{ required: true, min:3 }"
                               :id="`nombre_rl[${i}]`"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has(`nombre_rl[${i}]`)">{{ errors.first(`nombre_rl[${i}]`) }}</div>
                    </td>
                    <td>
                        <input class="form-control"
                               :name="`apellido_paterno[${i}]`"
                               :data-vv-as="`'Apellido Paterno ${i + 1}'`"
                               v-model="representante_legal.apellido_paterno"
                               :class="{'is-invalid': errors.has(`apellido_paterno[${i}]`)}"
                               v-validate="{ required: true, min:2 }"
                               :id="`apellido_paterno[${i}]`" :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has(`apellido_paterno[${i}]`)">{{ errors.first(`apellido_paterno[${i}]`) }}</div>
                    </td>
                    <td>
                        <input class="form-control"
                               :name="`apellido_materno[${i}]`"
                               :data-vv-as="`'Apellido Materno ${i + 1}'`"
                               v-model="representante_legal.apellido_materno"
                               :class="{'is-invalid': errors.has(`apellido_materno[${i}]`)}"
                               v-validate="{ required: true, min:2 }"
                               :id="`apellido_materno[${i}]`"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has(`apellido_materno[${i}]`)">{{ errors.first(`apellido_materno[${i}]`) }}</div>
                    </td>
                    <td v-if="representante_legal.id">
                        {{representante_legal.es_nacional == 0 ? 'Nacional' : 'Extranjero'}}
                    </td>
                    <td v-else>
                        <input type="radio" :id="`nacional[${i}]`" value="0" v-model="representante_legal.es_nacional">
                        <label :for="`nacional[${i}]`">Nacional</label>
                        <br>
                        <input type="radio" :id="`extranjero[${i}]`" value="1" v-model="representante_legal.es_nacional">
                        <label :for="`extranjero[${i}]`">Extranjero</label>
                        <br>
                    </td>
                    <td v-if="representante_legal.id">{{representante_legal.curp}}</td>
                    <td v-else-if="representante_legal.es_nacional == 0">
                        <input class="form-control"
                               :name="`curp[${i}]`"
                               :data-vv-as="`'CURP ${i + 1}'`"
                               v-model="representante_legal.curp"
                               :class="{'is-invalid': errors.has(`curp[${i}]`)}"
                               v-validate="{ required: true, min: 18, regex: /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/}"
                               :id="`curp[${i}]`"
                               :maxlength="18"/>
                        <div class="invalid-feedback" v-show="errors.has(`curp[${i}]`)">{{ errors.first(`curp[${i}]`) }}</div>
                    </td>
                    <td v-else-if="representante_legal.es_nacional == 1">
                        <input class="form-control"
                               :name="`identificacion[${i}]`"
                               :data-vv-as="`'Número de Identificación ${i + 1}'`"
                               v-model="representante_legal.curp"
                               :class="{'is-invalid': errors.has(`identificacion[${i}]`)}"
                               v-validate="{ required: true}"
                               :id="`identificacion[${i}]`"
                               :maxlength="18"/>
                        <div class="invalid-feedback" v-show="errors.has(`identificacion[${i}]`)">{{ errors.first(`identificacion[${i}]`) }}</div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarRepresentanteLegal(i)" :disabled="representantes_legales.data.length == 1" >
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                -->
                </tbody>
            </table>
        </div>
        <br>
    </span>
</template>

<script>
    export default {
        name: "Exclusiones"
    }
</script>

<style scoped>

</style>

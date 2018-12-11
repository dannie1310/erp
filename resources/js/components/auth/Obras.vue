<template>
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group">
                <span v-for="(grupo, i) in obrasAgrupadas">
                    <li class="list-group-item disabled"><i class="fa fa-fw fa-database"></i>{{ i }}</li>
                        <a v-for="obra in grupo" href="#" class="list-group-item" @click="setContext(i, obra.id_obra)">
                        {{ obra.nombre }}
                    </a>
                </span>
            </ul>
        </div>
    </div>

</template>

<script>
    import { mapActions } from 'vuex'

    export default {
        name: "Obras",

        computed: {
            obrasAgrupadas() {
                return this.$store.getters['obras/obrasAgrupadas']
            }
        },

        mounted() {
            this.fetch();
        },

        methods: {
            ...mapActions({
                fetch: 'obras/fetch'
            }),
            setContext(database, id_obra) {
                axios.post('/api/auth/setContext', {database: database, id_obra: id_obra})
                    .then(res => {
                        this.$session.set('jwt', res.data.access_token)
                        this.$router.push({name: 'home'})
                    })
                    .catch(err => {
                        alert(err);
                    })
            }
        }
    }
</script>

<style scoped>

</style>
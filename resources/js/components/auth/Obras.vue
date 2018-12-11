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

                return new Promise((res, rej) => {
                    axios.post('/api/auth/setContext', {database: database, id_obra: id_obra})
                        .then(response => {
                            res(response.data);
                        })
                        .catch(err => {
                            rej(err)
                        });
                })
                    .then(res => {
                        this.$session.set('jwt', res.access_token)
                        this.$session.set('obra', res.obra)

                        this.$store.commit("auth/setObra", res)
                        this.$router.push({name: 'home'})
                    })
                    .catch(error => {
                        this.$store.commit("auth/loginFailed", {error});
                    })
            }
        }
    }
</script>
<template>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"
            v-for="(breadcrumb, idx) in breadcrumbList"
            :key="idx"
            @click="routeTo(idx)"
            :class="{'active': !!breadcrumb.link}">
            {{ breadcrumb.name }}
        </li>
    </ol>
</template>

<script>
    export default {
        name: 'app-breadcrumb',
        data () {
            return {
                breadcrumbList: []
            }
        },
        mounted () { this.updateList() },
        watch: { '$route' () { this.updateList() } },
        methods: {
            routeTo (pRouteTo) {
                if (this.breadcrumbList[pRouteTo].link) this.$router.push(this.breadcrumbList[pRouteTo].link)
            },
            updateList () { this.breadcrumbList = this.$route.meta.breadcrumb }
        }
    }
</script>
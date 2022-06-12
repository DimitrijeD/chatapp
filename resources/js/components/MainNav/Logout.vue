<template>
    <button @click.prevent="logout">Logout</button>
</template>

<script>
export default {
    methods: {
        logout(){
            axios.get('/api/logout').then(()=>{
                this.$store.dispatch("logout")
                this.$router.push({ path: '/login' })
            });
        },
    },
}
</script>

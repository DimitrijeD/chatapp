<template>
    <button @click.prevent="logout">Logout</button>
</template>

<script>
export default {
    methods: {
        logout(){
            axios.get('/api/logout').then(()=>{
                this.$store.dispatch("LogOut");
                this.$router.push({ path: '/' })
            });
        },
    },
}
</script>

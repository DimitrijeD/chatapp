<template>
    <button @click.prevent="logout">Logout</button>
</template>

<script>
export default {
    methods: {
        logout(){
            axios.post('/api/logout').then(()=>{
                this.$router.push({ path: '/' })
            });
        },
    },
}
</script>

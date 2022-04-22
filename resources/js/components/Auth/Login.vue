<template>
    <div class="flex h-screen justify-center items-center">
        <div class="w-8/12">
            <form v-on:submit.prevent="login">
                <div class="my-3">
                    <div v-if="errors.email">
                        <span class="w-full text-red-500" v-for="(emailError, index) in errors.email" :key="index">
                            {{ emailError }}
                            <br>
                        </span>
                    </div>
                    <input 
                        class="shadow-inner tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3" 
                        placeholder="Email" 
                        type="email" 
                        v-model="form.email">
                </div>
                <div class="my-3">
                    <div v-if="errors.password">
                        <span class=" w-full text-red-500" v-for="(passwordError, index) in errors.password" :key="index">
                            {{ passwordError }}
                            <br>
                        </span>
                    </div>
                    <input 
                        class="tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3" 
                        placeholder="Password" 
                        type="password" 
                        v-model="form.password" 
                        name="password">
                </div>
                     
                <button 
                    type="submit"
                    class="tnx-hver w-full text-center text-lg text-white bg-blue-500 border-0 py-3 focus:outline-none hover:bg-blue-600 rounded"
                >Login</button>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    data(){
        return{
            form:{
                email: '',
                password: ''
            },
            errors: {}
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
        }),
    },

    created(){
        // if there are data in store.user while user is inside login page, code is shit
        // console.log(this.user)
    },

    methods:
    {
        login()
        {
            axios.post('/api/login', this.form).then((res) => {
                this.$store.dispatch('storeUser', res.data)
                this.$router.push({ path: '/profile' });
            }).catch((error) =>{
                this.errors = error.response.data.errors;
            })
        },

    }
}
</script>

<style>
.tnx-hver {

}

.tnx-hver:hover {
    /* font-size: 1.16rem; */
    -webkit-transform: scale(1.006);
}
</style>
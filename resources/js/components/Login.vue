<template>
    <div class="flex flex-wrap w-full justify-center items-center pt-56">
        <div class="flex flex-wrap max-w-xl">
            <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Login to your account</h1></div>
            <div class="p-2 w-full mb-2">
                <label>Your e-mail</label>
                <div v-if="errors.email">
                    <span class="w-full text-red-500" v-for="emailError in errors.email">
                        {{ emailError }}
                        <br>
                    </span>
                </div>
                <input class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2" placeholder="Email" type="email" v-model="form.email">
            </div>
            <div class="p-2 w-full mb-2 ">
                <label>Password</label>
                <div v-if="errors.password">
                    <span class="w-full text-red-500" v-for="passwordError in errors.password">
                        {{ passwordError }}
                        <br>
                    </span>
                </div>
                <input class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2" placeholder="Password" type="password" v-model="form.password" name="password">
            </div>
            <div class="p-2 w-full mt-3">
                <button @click.prevent="loginUser" type="submit" class="flex text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Login</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            form:{
                email: '',
                password: ''
            },
            errors: []
        }
    },
    methods:{
        loginUser(){
            axios.post('/api/login', this.form).then(() =>{
                this.$router.push({ path: '/profile' });
            }).catch((error) =>{
                this.errors = error.response.data.errors;
            })
        }
    }
}
</script>

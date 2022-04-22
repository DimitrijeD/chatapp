<template>
    <div class="flex h-screen justify-center items-center">
        <div class="w-8/12">
            <div class="my-3">
                <div v-if="errors.first_name">
                    <span class="w-full text-red-500" v-for="(error, index) in errors.first_name" :key="index">
                        {{ error }}
                        <br>
                    </span>
                </div>
                <input
                    class="shadow-inner tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3"
                    placeholder="First name"
                    type="text"
                    v-model="form.first_name"
                    required
                >
            </div>

            <div class="my-3">
                <div v-if="errors.last_name">
                    <span class="w-full text-red-500" v-for="(error, index) in errors.last_name" :key="index">
                        {{ error }}
                        <br>
                    </span>
                </div>
                <input
                    class="tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3"
                    placeholder="Last name"
                    type="text"
                    v-model="form.last_name"
                    required
                >
            </div>

            <div class="my-3">
                <div v-if="errors.email">
                    <span class="w-full text-red-500" v-for="(error, index) in errors.email" :key="index">
                        {{ error }}
                        <br>
                    </span>
                </div>
                <input
                    class="tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3"
                    placeholder="Email"
                    type="email"
                    v-model="form.email"
                    required
                >
            </div>

            <div class="my-3">
                <div v-if="errors.password">
                    <span class="w-full text-red-500" v-for="(error, index) in errors.password" :key="index">
                        {{ error }}
                        <br>
                    </span>
                </div>
                <input
                    class="tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3"
                    placeholder="Password"
                    type="password"
                    v-model="form.password"
                    name="password"
                    required
                >
            </div>

            <div class="my-3">
                <input
                    class="tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3"
                    placeholder="Confirm Password"
                    type="password"
                    v-model="form.password_confirmation"
                    name="password_confirmation"
                    required
                >
            </div>

            <div class="my-3">
                <div v-if="errors.profilePicture">
                    <span class="w-full text-red-500" v-for="(error, index) in errors.profilePicture" :key="index">
                        {{ index }}
                        <br>
                    </span>
                </div>
                <input
                    type="file"
                    @change="onProfilePictureSelected"
                    name="profilePictureName"
                    class="tnx-hver w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 text-lg pl-3 py-3"
                    required
                >
            </div>

            <div class="p-2 w-full mt-3">
                <button type="submit" class="tnx-hver w-full text-center text-lg text-white bg-blue-500 border-0 py-3 focus:outline-none hover:bg-blue-600 rounded">Register</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            form:{
                first_name: '',
                last_name: '',
                email: '',
                password:'',
                password_confirmation:'',
                profilePicture: null,
            },
            errors:[],
        }
    },

    methods:{
        register(){
            let userData = new FormData;
            userData.append('first_name', this.form.first_name);
            userData.append('last_name', this.form.last_name);
            userData.append('email', this.form.email);

            userData.append('password', this.form.password);
            userData.append('password_confirmation', this.form.password_confirmation);

            userData.append('profilePicture', this.form.profilePicture);

            axios.post('/api/register', userData)
                .then((res) =>{
                    this.$router.push({ path: '/email-verification/init' });
                }).catch((error) =>{
                    this.errors = error.response.data.errors;
                });
        },

        onProfilePictureSelected(event){
            this.form.profilePicture = event.target.files[0];
        }
    },

    mounted() {

    },
}
</script>

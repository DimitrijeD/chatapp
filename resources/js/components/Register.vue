<template>
    <div class="flex flex-wrap w-full justify-center mt-3">

        <form @submit.prevent="register" enctype="multipart/form-data">
            <div class="flex flex-wrap max-w-xl">
                <div class="p-2 text-2xl text-gray-800 font-semibold"><h1>Register an account</h1></div>

                <div class="p-2 w-full mb-2">
                    <label class="w-full text-lg">First Name</label>
                    <div v-if="errors.first_name">
                        <span class="w-full text-red-500" v-for="first_nameError in errors.first_name">
                            {{ first_nameError }}
                            <br>
                        </span>
                    </div>
                    <input
                        class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2"
                        placeholder="First name"
                        type="text"
                        v-model="form.first_name"
                        required
                    >
                </div>

                <div class="p-2 w-full mb-2">
                    <label class="w-full text-lg">Last Name</label>
                    <div v-if="errors.last_name">
                        <span class="w-full text-red-500" v-for="last_nameError in errors.last_name">
                            {{ last_nameError }}
                            <br>
                        </span>
                    </div>
                    <input
                        class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2"
                        placeholder="Last name"
                        type="text"
                        v-model="form.last_name"
                        required
                    >
                </div>

                <div class="p-2 w-full mb-2">
                    <label class="w-full text-lg" >Your e-mail</label>
                    <div v-if="errors.email">
                        <span class="w-full text-red-500" v-for="emailError in errors.email">
                            {{ emailError }}
                            <br>
                        </span>
                    </div>
                    <input
                        class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2"
                        placeholder="Email"
                        type="email"
                        v-model="form.email"
                        required
                    >
                </div>

                <div class="p-2 w-full mb-2">
                    <label class="w-full text-lg">Password</label>
                    <div v-if="errors.password">
                        <span class="w-full text-red-500" v-for="passwordError in errors.password">
                            {{ passwordError }}
                            <br>
                        </span>
                    </div>
                    <input
                        class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2"
                        placeholder="Password"
                        type="password"
                        v-model="form.password"
                        name="password"
                        required
                    >
                </div>

                <div class="p-2 w-full mb-2">
                    <label class="w-full text-lg">Confirm Password</label>
                    <input
                        class="w-full bg-gray-100 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2"
                        placeholder="Confirm Password"
                        type="password"
                        v-model="form.password_confirmation"
                        name="password_confirmation"
                        required
                    >
                </div>

                <div class="p-2 w-full mb-2">
                    <label class="w-full text-lg">Profile Picture</label>
                    <div v-if="errors.profilePicture">
                        <span class="w-full text-red-500" v-for="profilePictureError in errors.profilePicture">
                            {{ profilePictureError }}
                            <br>
                        </span>
                    </div>
                    <input
                        type="file"
                        @change="onProfilePictureSelected"
                        name="profilePictureName"
                        class="w-full bg-gray-200 rounded border border-gray-400 focus:outline-none focus:border-indigo-500 text-base px-4 py-2"
                        required
                    >
                </div>

                <div class="p-2 w-full mt-3">
                    <button type="submit" class="flex text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Register</button>
                </div>
            </div>
        </form>

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

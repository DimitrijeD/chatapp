<template>
    <div class="grid place-items-center h-screen">
        <div class="w-3/4">

            <div class="text-center text-blue-500 bg-gradient-to-b from-gray-200 to-gray-100">
                <p class="px-3 py-8 text-2xl font-semibold">
                    One step away
                </p>
            </div>

            <div 
                class="text-center break-words text-lg py-16 shadow-inner"
                v-bind:class="{
                    'text-green-600': status_type,
                    'text-red-400':  !status_type,
                }"
            >
                <p class="m-3">{{ status }}</p>
            
                <p class="m-3 mt-5 text-green-700 font-bold">{{ email }}</p>
            </div>


            <div 
                class="p-3" 
                v-if="!status"
            >
                Before proceeding, please check your email for a verification link.
            </div>

            <button
                class="p-4 bg-blue-400 hover:bg-blue-500 text-gray-100 hover:text-white w-full text-lg"
                @click="resendEmailVerification"
            >
                Click here to request another.
            </button>
        </div>
    </div>
</template>

<script>
export default {

    data(){
        return {
            email: '',
            status: '',
            status_type: true,
        }
    },

    mounted(){
        // user is loggedin but email not verified, so we should get his email which is necessary for resending verification
        this.getUnAuthUser() 
    },

    methods: {
        getUnAuthUser()
        {
            axios.get('/api/get-user').then((res) => {
                this.$router.push({ path: '/profile' })
            }).catch((error) => {
                if(error.response.data?.email){
                    this.email = error.response.data.email
                    this.status = "Verification has been sent."
                    this.status_type = true
                } 
                // @todo catch error if user is accualy successfully verified but his page remained open. 
                // If user clicks resend again, redirect to profile page
            });
        },

        resendEmailVerification()
        {
            axios.post('/api/email-verification/create-or-update', {email: this.email})
                .then((res)=>{
                    this.status = 'Another email has been sent. Please check if you inserted correct email.'
                }).catch((error) =>{
                    if(error.response.status == 429){
                        this.status = "Please wait for 1 minute before requesting another email."
                        this.status_type = false
                    }
                });
        }

    }
}
</script>

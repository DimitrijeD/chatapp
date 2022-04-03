<template>
    <div class="flex h-screen">
        <div class="m-auto">
            <div class="card border-gray-250 border-2" >
                <div class="card-header text-center">Verify Your Email Address</div>

                <div class="card-body pt-4">
                    <div class="alert alert-success" role="alert">
                        {{ status }}
                    </div>

                    <div v-if="!status">Before proceeding, please check your email for a verification link.</div>

                    If you did not receive the email,

                    <button
                        class="btn btn-link p-0 m-0 align-baseline"
                        @click="resendEmailVerification"
                    >
                        click here to request another.
                    </button>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            user: null,
            status: '',
            validationResult: null,
        }
    },

    mounted() {

    },

    methods: {
        afterRegistration()
        {
            this.status = 'A fresh verification link has been sent to your email address.';
        },

        afterMailUrl(pathArray)
        {
            
            axios
                .post('/api/mail-verification-clicked', {slug: emailHashStr})
                .then((res) => {
                    this.status = res.data;
                    this.$router.push({ path: '/profile' });
                })
                .catch(error => {
                    //error;
                });
        },

        //@TODO somehow get user id or mail and send to method to resend mail
        resendEmailVerification()
        {
            axios
                .post('/api/mail-verification-resend', {})
                .then((res)=>{
                    this.test_data = res.data;
                });
        }

    }
}
</script>

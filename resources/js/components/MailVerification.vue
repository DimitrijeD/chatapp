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
        // if user clicked on link which he received in mail,
        // pathArray should have following structure:
        //  [0] => ""
        //  [1] => "mail-verification"
        //  [2] => "hisEmail-hash"
        let pathArray = window.location.pathname.split('/');

        switch (pathArray.length){
            case 2:
                // user was redirected to this page after registration
                this.afterRegistration();
                break;

            case 3:
                // user clicked link received in mail
                this.afterMailUrl(pathArray);
                break;

            default:
                console.log('something is wrong with url');
                console.log(pathArray);
        }

    },

    methods: {
        afterRegistration()
        {
            this.status = 'A fresh verification link has been sent to your email address.';
        },

        afterMailUrl(pathArray)
        {
            let emailHashStr = pathArray[2];

            let email = emailHashStr.split('-')[0];
            let hash  = emailHashStr.split('-')[1];

            // If email is valid and hash is 64 bytes, proceed with users email validation.
            if( this.validateEmail(email) && this.validateHash(hash) ) {
                axios
                    .post('/api/mail-verification-clicked', {slug: emailHashStr})
                    .then((res) => {
                        this.status = res.data;
                        // this.$router.push({ name: "Home" });
                    });
            } else {
                this.validationResult = 'Invalid link, please request another one';
            }
        },

        // anystring@anystring.anystring
        validateEmail(email)
        {
            let re = /\S+@\S+\.\S+/;
            return re.test(email);
        },

        validateHash(hash)
        {
            if(hash.length == 64){
                return true;
            }
            return false;
        },

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

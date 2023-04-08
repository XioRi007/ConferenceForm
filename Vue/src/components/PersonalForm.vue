<script setup>
    import { vMaska } from "maska"
    import { useStore } from 'vuex'

    import { onMounted, ref, reactive } from 'vue';
    const props = defineProps(['onNext']);

    const store = useStore();
    const error = ref('');
    const personalForm = reactive(store.getters['register/getPersonal']);
    //
    const countries = ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"];

    /**
     * При onMounted
     * Если в store есть id участника, и при этом сами поля пусты (на примере firstName)
     * Загружает персональную информацию
     */
    onMounted(async ()=>{
        if(personalForm.id != null && personalForm.firstName == ""){   
            try{
                await store.dispatch('register/loadPersonal');
            } 
            catch(err){
                error.value = err.message;
            }
        }
    });

    /**
     * Обработчик input
     */
    const setPersonal = (e) =>{
        store.commit('register/setPersonal', {
            name:e.target.name,
            value:e.target.value
        });
    }

    /**
     * Если в localStorage уже есть id - обновляем участника
     * Если нет - регистрируем
     * 
     */
    const nextClick = async(e) => {
        e.preventDefault();
        try{
            const userId = localStorage.getItem('user');
            if(userId!== null){
            await store.dispatch('register/updateParticipant');
            }else{
            await store.dispatch('register/registerParticipant');
            const user = store.getters['register/getId'];
            localStorage.setItem('user', user);
            }
            props.onNext();
        }catch(err){
            error.value = err.message;
        }
    }
    const todayDate = () => {
        let today = new Date();
        let year = today.getFullYear();
        let month = String(today.getMonth() + 1).padStart(2, '0');
        let day = String(today.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

</script>

<template>
    <form @submit.prevent="nextClick">
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="firstName">First Name</label>
        <div class="col-sm-9">
        <input id="firstName" name="firstName" class="form-control" type="text" :value="personalForm.firstName" @input="setPersonal" required/>
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="last-name">Last Name</label>
        <div class="col-sm-9">
        <input id="lastName" name="lastName" class="form-control" type="text" :value="personalForm.lastName" @input="setPersonal" required />
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="birthdate">Birthdate</label>
        <div class="col-sm-9">
        <input id="birthdate" name="birthdate" class="form-control" :max="todayDate()" type="date" :value="personalForm.birthdate" @input="setPersonal" required />
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="report-subject">Report Subject</label>
        <div class="col-sm-9">
        <input id="reportSubject" name="reportSubject" class="form-control" type="text" :value="personalForm.reportSubject" @input="setPersonal" required />
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="country">Country</label>
        <div class="col-sm-9">
        <select class="form-select" name="country" aria-label="Default select example" :value="personalForm.country" @input="setPersonal" >
            <option v-for="country in countries" :key="country">{{ country }}</option>
        </select>
        </div>
    </div>

    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="phone">Phone</label>
        <div class="col-sm-9">
        <input id="phone" name="phone" class="form-control" v-maska data-maska="+1(###)-###-####" :value="personalForm.phone" @input="setPersonal" required>
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="email">Email</label>
        <div class="col-sm-9">
        <input id="email" name="email" class="form-control" type="email" :value="personalForm.email" @input="setPersonal" required />
        </div>
    </div>
    <div class="row mb-3 text-danger" v-if="error != ''">
        <p>{{ error }}</p>
    </div>
    <div class="controls">
        <button class="btn btn-success" type="submit">
            Next
        </button>
    </div>
</form>
</template>



<style>
</style>

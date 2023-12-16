<script>
import { required, maxLength} from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
export default {
    props: ['deskId'],
    setup () {
        return {v$: useVuelidate()}
    },
    data() {
        return {
            name: null,
            desk_lists_name: null,
            errored: false,
            loading: true,
            desk_lists: true,
            desk_list_input_id: null,
            card_names: [],
        }
    },
    methods: {
        getDeskLists() {
            axios.get('/api/V1/desk-lists/', {
                params: {
                    desk_id: this.deskId
                }
            })
                .then(response=> {
                    this.desk_lists = response.data.data
                    this.desk_lists.forEach(el=>{
                        this.card_names[el.id] = ''
                    })
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        saveName() {
            this.v$.name.$touch()
            if (this.v$.$pending || this.v$.$error) {
                return;
            }
            axios.post('/api/V1/desks/'+this.deskId, {
                _method: 'PUT',
                name: this.name
                })
                .then(response => {
                    this.name = response.data.data.name;
                })
                .catch(error => {
                    console.error("Error saving name:", error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        addNewDeskList() {
            this.v$.$touch()
            if (this.v$.$pending || this.v$.$error) {
                return;
            }
            axios.post('/api/V1/desk-lists/', {
                name: this.desk_lists_name,
                desk_id: this.deskId,
            })
                .then(response => {
                    this.v$.$reset()
                    this.desk_lists_name = ''
                    this.desk_lists = []
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteDeskList(id) {
            axios.post('/api/V1/desk-lists/'+id, {
                _method: 'DELETE'
            })
                .then(response=>{
                    this.desk_lists = []
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        updateDeskList(id, name) {
            axios.post('/api/V1/desk-lists/'+id, {
                _method: 'PUT',
                name: name
            })
                .then(response=>{
                    this.desk_list_input_id = null
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        addNewCard(desk_list_id) {
            axios.post('/api/V1/cards', {
                name: this.card_names[desk_list_id],
                desk_list_id
            })
                .then(response => {
                    this.card_names[desk_list_id] = ''
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteCard(id) {
            axios.post('/api/V1/cards/' + id, {
                _method: 'DELETE'
            })
                .then(response=>{
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
        }
    },
    mounted() {
        axios.get('/api/V1/desks/'+this.deskId)
            .then(response=>{
                this.name = response.data.data.name
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            })
            .finally(() => {
                this.loading = false
            })
        this.getDeskLists()
    },
    validations () {
        return {
            name: {required, maxLength: maxLength(255)},
            desk_lists_name: {required, maxLength: maxLength(255)},
            card_names: {
                $each: {
                    required, maxLength: maxLength(255)
                }
            },
        }
    }
}
</script>

<template>
    <div class="container">
        <h1>{{name}}</h1>

        <div class="form-group">
            <input type="text" @blur="saveName" v-model="name" class="form-control mt-3" :class="{'is-invalid': v$.name.$pending || v$.name.$error}">
            <div v-if="v$.name.$pending">Loading...</div>
            <div class="invalid-feedback" v-if="!v$.name.required">
                Required row.
            </div>
            <div class="invalid-feedback" v-if="!v$.name.maxLength">
                Max length of elements - {{ v$.name.$params.maxLength.max }}.
            </div>
        </div>

        <form @submit.prevent="addNewDeskList">

            <div class="form-group">
                <input type="text" v-model="desk_lists_name" class="form-control mt-2"  placeholder="Enter name of list" :class="{'is-invalid': v$.desk_lists_name.$pending || v$.desk_lists_name.$error}">

                <div class="invalid-feedback" v-if="!v$.desk_lists_name.required">
                    Required row.
                </div>

                <div class="invalid-feedback" v-if="!v$.desk_lists_name.maxLength">
                    Max length of elements - {{ v$.desk_lists_name.$params.maxLength.max }}.
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add list</button>
        </form>

        <div class="row">
            <div class="col-lg-4" v-for="desk_list in desk_lists">
                <div class="card mt-3">
                    <div class="card-body">

                        <form @submit.prevent="updateDeskList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id" class="d-flex justify-content-between align-items-center">
                            <input type="text" v-model="desk_list.name" class="form-control mt-2"  placeholder="Enter name to change of list">
                            <button type="button" @click="desk_list_input_id = null" class="btn-close" aria-label="Close"></button>
                        </form>

                        <h4 v-else class="card-title d-flex justify-content-between align-items-center" style="cursor: pointer;" @click="desk_list_input_id = desk_list.id">{{desk_list.name}}<i class="fa-solid fa-pen" style="font-size: 15px;"></i></h4>

                    </div>
                    <button type="button" class="btn btn-danger m-2" @click="deleteDeskList(desk_list.id)">Delete</button>

                    <div class="card border-0 m-2" >

                        <div class="card-body bg-light mt-3" style="border: 1px solid lightgray" v-for="card in desk_list.cards" :key="card.id">
                            <h4 class="card-title d-flex justify-content-between align-items-center" style="cursor: pointer;">{{card.name}}</h4>
                            <button @click="deleteCard(card.id)" type="button" class="btn btn-secondary mt-3">Delete</button>
                        </div>

                    </div>

                    <form @submit.prevent="addNewCard(desk_list.id)" class="d-flex justify-content-between align-items-center mt-3">
                        <input type="text" v-model="card_names[desk_list.id]" class="form-control m-2" placeholder="Enter name of card">
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center">
            <div class="spinner-border" role="status" v-if="loading">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

    </div>
</template>

<style>
</style>

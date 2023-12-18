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
            current_card: [],
            show_card_name_input: false,
            new_task_name: '',
            task_input_name_id: null,
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
            this.v$.desk_lists_name.$touch()
            if (this.v$.desk_lists_name.$error) {
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
            if (confirm('Do you really want to delete the board ?')) {
                axios.post('/api/V1/desk-lists/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
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
            }
        },
        updateDeskList(id, name) {
            this.desk_list_input_id = null
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
        },
        getCard(id) {
            this.show_card_name_input = false;
            axios.get('/api/V1/cards/'+ id)
                .then(response=> {
                    this.current_card = response.data.data

                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        updateCardName() {
            this.v$.current_card.name.$touch()
            if (this.v$.current_card.$error) {
                return;
            }
            this.show_card_name_input = false
            axios.post('/api/V1/cards/' + this.current_card.id,{
                _method: 'PATCH',
                name: this.current_card.name,
                desk_list_id: this.current_card.desk_list_id
            })
                .then(response => {
                    this.current_card = [];
                    this.show_card_name_input = false;
                    this.current_card = response.data.data;
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error);
                    this.errored = true;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        addNewTask() {
            this.v$.new_task_name.$touch()
            if (this.v$.new_task_name.$error) {
                return;
            }
            axios.post('/api/V1/tasks', {
                name: this.new_task_name,
                card_id: this.current_card.id,
            })
                .then(response => {
                    this.new_task_name = '';
                    this.v$.$reset()
                    this.getCard(this.current_card.id)
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteTask(id) {
            axios.post('/api/V1/tasks/' + id, {
                _method: 'DELETE',

            })
                .then(response => {
                    this.v$.$reset()
                    this.getCard(this.current_card.id)
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
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
            current_card: {
                name: {required, maxLength: maxLength(255)}
            },
            desk_lists: {
                name: {required, maxLength: maxLength(255)}
            },
            new_task_name: {required, maxLength: maxLength(255)}
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

                <div class="invalid-feedback" v-if="!v$.desk_lists_name.required">Required row.</div>

                <div class="invalid-feedback" v-if="!v$.desk_lists_name.maxLength">Max length of elements - {{ v$.desk_lists_name.$params.maxLength.max }}.</div>

            </div>
            <button type="submit" class="btn btn-primary mt-2">Add list</button>
        </form>

        <div class="row">
            <div class="col-lg-4" v-for="desk_list in desk_lists">
                <div class="card mt-3">
                    <div class="card-body">

                        <form @submit.prevent="updateDeskList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id" class="d-flex justify-content-between align-items-center mb-2">
                            <input type="text" v-model="desk_list.name" class="form-control mt-2"  placeholder="Enter name to change of list" :class="{'is-invalid': v$.desk_lists.name.$error}">
                            <button type="button" @click="desk_list_input_id = null" class="btn-close" aria-label="Close"></button>
                            <div class="invalid-feedback" v-if="!v$.desk_lists.required">
                                Required row.
                            </div>
                        </form>

                        <h4 v-else class="card-title mb-3 d-flex justify-content-between align-items-center" style="cursor: pointer;" @click="desk_list_input_id = desk_list.id">{{desk_list.name}}<i class="fa-solid fa-pen" style="font-size: 15px;"></i></h4>
                        <button type="button" class="btn btn-danger w-100" @click="deleteDeskList(desk_list.id)">Delete</button>

                        <div class="card-body bg-light mt-3" style="border: 1px solid lightgray" v-for="card in desk_list.cards" :key="card.id">
                            <h4 class="card-title d-flex justify-content-between align-items-center" style="cursor: pointer;">{{card.name}}</h4>
                            <button @click="getCard(card.id)" type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modal-card">Open</button>
                            <button @click="deleteCard(card.id)" type="button" class="btn btn-secondary">Delete</button>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-card" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <form @submit.prevent="updateCardName" v-if="show_card_name_input" class="d-flex justify-content-between align-items-center mb-2">
                                            <input type="text" v-model="current_card.name" class="form-control mt-2"  placeholder="Enter name to change of card" :class="{'is-invalid': v$.current_card.name.$error}">
                                            <button type="button" class="btn-close ms-3" aria-label="Close" @click="updateCardName"></button>
                                            <div class="invalid-feedback" v-if="!v$.current_card.required">Required row.</div>
                                        </form>
                                        <h5 class="modal-title" v-if="!show_card_name_input"  id="exampleModalLabel" style="cursor: pointer;" @click="show_card_name_input = true">{{current_card.name}}<i class="fa-solid fa-pen ms-3" style="font-size: 15px;"></i></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="form-check" v-for="task in current_card.tasks" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <div style="flex-grow: 1;">
                                                <input class="form-check-input" type="checkbox" :id="'inlineCheckbox_' + task.id" :value="task.id">
                                                <label class="form-check-label" :for="'inlineCheckbox_' + task.id">{{ task.name }}</label>
                                            </div>
                                            <button type="button" @click="deleteTask(task.id)" class="btn-close" aria-label="Close"></button>
                                        </div>

                                        <form @submit.prevent="addNewTask" class="mt-3">

                                            <div class="form-group" >
                                                <input v-model="new_task_name" :class="{'is-invalid': v$.new_task_name.$error}" type="text" placeholder="Enter name of task">
                                            </div>

                                            <div class="invalid-feedback" v-if="!v$.new_task_name.required">Required row.</div>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="addNewCard(desk_list.id)" class="d-flex justify-content-between align-items-center mt-3">
                            <input type="text" v-model="card_names[desk_list.id]" class="form-control" placeholder="Enter name of card">
                        </form>

                    </div>

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

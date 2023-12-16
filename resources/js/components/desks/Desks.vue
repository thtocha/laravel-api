<script>
export default {
    data(){
        return {
            desks: [],
            errored: false,
            loading: true,
            name: ''
        }
    },
    mounted() {
        this.getAllDesks()
    },
    methods: {
        getAllDesks() {
            axios.get('/api/V1/desks')
                .then(response=>{
                    this.desks = response.data.data
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteDesk(id) {
            if (confirm('Do you really want to delete the board ?')) {
                axios.post('/api/V1/desks/'+id, {
                    _method: 'DELETE'
                })
                    .then(response=>{
                        this.desks = []
                        this.getAllDesks()
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
        addNewDesk() {
            axios.post('/api/V1/desks', {
                name: this.name
            })
                .then(response => {
                    this.name = ''
                    this.desks = []
                    this.getAllDesks()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        }
    }
}
</script>
<template>
    <div class="container">
        <h1>Desks</h1>
        <form @submit.prevent="addNewDesk">
            <div class="form-group">
                <input type="text" v-model="name" class="form-control" placeholder="Enter name of desk">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add desk</button>
        </form>
        <div class="row">
            <div class="col-lg-4" v-for="desk in desks">
                <div class="card mt-3">
                    <router-link class="card-body" :to="{name: 'showDesk', params: {deskId: desk.id}}">
                        <h4 class="card-title">{{desk.name}}</h4>
                    </router-link>
                    <button type="button" class="btn btn-danger" @click="deleteDesk(desk.id)">Delete</button>
                </div>
            </div>
        </div>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Download error
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

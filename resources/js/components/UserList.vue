<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Users</h1>
                    </div>
                    <div class="col-sm-6"></div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">User List
                                <button style="float: right;"  v-if=accessAdd type="button" class="btn btn-primary pull-right" v-on:click="addModal()">
                                    <i class="fas fa-user-plus"></i> Add User</button>
                            </div>

                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>IP address</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr v-for="user in users" :key="user.id">
                                            <td>{{ user.name}}</td>
                                            <td>{{ user.email}}</td>
                                            <td><code>{{ user.ip_address}}</code></td>
                                            <td>{{ user.created_at | dateFormat}}</td>
                                            <td>

                                                <b-button  v-if=accessEdit v-on:click="editModal(user)" variant="primary" class="mb-2"><b-icon icon="pencil-square" aria-hidden="true"></b-icon> Edit
                                                </b-button>

                                                <b-button   v-if="user.id != $auth.user().id && accessDelete"  v-on:click="deleteUser(user.id)" variant="danger" class="mb-2"><b-icon icon="trash" aria-hidden="true"></b-icon> Delete {{  }}
                                                </b-button>

                                            </td>
                                        </tr>                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLabel" v-if="editMode">Edit User</h5>
                            <h5 class="modal-title" id="userModalLabel" v-else >Add User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editMode ? updateUser() : createUser()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <!-- Alert -->
                                <alert-error :form="form"></alert-error>

                                <div class="form-group">
                                    <input v-model="form.name" type="text" name="name" id="name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Name">
                                    <has-error :form="form" field="name"></has-error>
                                </div>

                                <div class="form-group">
                                    <input v-model="form.email" type="email" name="email" id="email"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email" autocomplete="off">
                                    <has-error :form="form" field="email"></has-error>
                                </div>


                                <div class="form-group" v-if="addMode" >
                                    <input v-model="form.password" type="password" name="password" id="password"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Password" autocomplete="new-password">
                                    <has-error :form="form" field="password"></has-error>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" v-if="editMode" >Update</button>
                                <button type="submit" class="btn btn-primary" v-else>Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal End /-->

        </div>
    </div>
</template>

<script>
    export default {

        mounted() {
          this.getUsers()
        },

        created() {
        },
        data() {
            return {
                baseUrl: process.env.MIX_APP_URL,
                users:{},
                options:{},
                editMode:false,
                addMode:false,
                accessAdd : false,
                accessEdit : true,
                accessDelete : true,
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                }),
            }
        },

        methods: {

            addModal () {
                this.form.reset();
                this.form.clear();
                this.editMode = false;
                this.addMode = true;
                this.form.status =1;
                $("#userModal").modal('show');
            },

            editModal (data) {
                // Make a request for a get Users 
                this.form.reset();
                this.form.clear();
                this.form.fill(data);
                this.editMode = true;
                this.addMode = false;
                $("#userModal").modal('show');
            },

            createUser (event) {
                // Submit the form via a POST request
                this.$Progress.start();

                this.form.post(this.baseUrl+'/api/users')
                .then(({ data }) => { 
                    this.getUsers();
                    Toast.fire({ icon: 'success', title: 'User created successfully' });
                    $("#userModal").modal('hide');
                    this.$Progress.finish()
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.$Progress.finish()
                });
            },

            updateUser (event) {
                this.$Progress.start();  
                var URL = this.baseUrl+'/api/users/' + this.form.id; 
                this.form.put(URL)
                .then(({ data }) => { 
                    this.getUsers();
                    Toast.fire({ icon: 'success', title: 'User updated successfully' });
                    $("#userModal").modal('hide');
                    this.$Progress.finish()
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.$Progress.finish()
                });
                
            },

            deleteUser (id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete(this.baseUrl+'/api/users/'+id).then(({data})=>{
                            this.getUsers();
                            Swal.fire('Deleted!', 'User has been deleted.', 'success')
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },

            async getUsers () {
             let res =  await this.$http({ url: `users`,  method: 'GET'});
             this.users = res.data.users || {}; 
            },
        }
    }
</script>

<template >
    <div class="modal fade" tabindex="-1" role="dialog" id="editTaskModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" class="card-body text-left" v-on:submit.prevent="editTaskForm">
                        <input type="hidden" name="id" id="id" :value="task_obj.id">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" v-model="task_obj.name">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"
                                      v-model="task_obj.description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Start date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   v-model="task_obj.start_date">
                        </div>
                        <div class="form-group">
                            <label for="due_date">Due date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control"
                                   v-model="task_obj.due_date">
                        </div>

                        <div class="form-group">
                            <label for="pipeline_id">Pipeline</label>
                            <select name="pipeline_id" id="pipeline_id" class="form-control"
                                    v-model="task_obj.pipeline_id">
                                <option value="null" selected disabled>Please select an option</option>
                                <option v-for="pipeline in pipelines" :value="pipeline.id">{{pipeline.name}}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">Assign</label>
                            <select name="user_id" id="user_id" class="form-control" v-model="task_obj.user_id">
                                <option value="null" selected disabled>Please select an option</option>
                                <option v-for="user in users" :value="user.id">{{user.name}}
                                </option>
                            </select>
                        </div>
                        <button>Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'edit-task-modal',
        data: function () {
            return {
                count: 0,
                pipelines,
                users
            }
        },
        props: {
            task_obj: {},
        },
        mounted() {
              // load pipelines and users
            axios.get('/api/pipelines').then(({data}) => {
                this.pipelines = [];
                data.pipelines.forEach(pipeline => {
                    this.pipelines.push(pipeline);
                });
            });
            axios.get('/api/users').then(({data}) => {
                this.users = [];
                data.users.forEach(pipeline => {
                    this.users.push(pipeline);
                });
            });
        },
        methods: {
            editTaskForm() {
                this.cleanErrorsForm();
                let vue_scope = this;
                $.ajax({
                    url: window.location.origin + '/api/task',
                    type: 'post',
                    data: vue_scope.task_obj,
                    success: function (data) {
                        Vue.notify({
                            group: 'notification-template',
                            title: 'Success',
                            text: 'Task updated!',
                            type: 'success',
                            duration: '3500'
                        });
                        let task = $("#task_" + vue_scope.task_obj.id);
                        let wrapper = task.parent();
                        task.data('start_date', vue_scope.task_obj.start_date);
                        task.data('due_date', vue_scope.task_obj.due_date);
                        wrapper.data('start_date', vue_scope.task_obj.start_date);
                        wrapper.data('due_date', vue_scope.task_obj.due_date);
                        $("#editTaskModal").modal('hide');

                        vue_scope.$emit('success_callback',data.payload);
                    },
                    error: function (error) {
                        let errors = $.parseJSON(error.responseText);
                        Vue.notify({
                            group: 'notification-template',
                            title: 'Error',
                            text: errors.message,
                            type: 'error',
                            duration: '3500'
                        });
                        $.each(errors['errors'], function (key, value) {
                            console.log('key ' + key);
                            console.log('value ' + value);
                            $("#" + key).after('<label class="error">' + value + '</label>');

                        });
                        this.error_callback();
                    }
                })
            },
            cleanErrorsForm() {
                $("form label.error").each(function (index, item) {
                    item.remove();
                })
            }
        }
    }
</script>

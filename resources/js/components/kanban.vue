<template>
    <div class="card-scene component-wrapper">
        <Container
            orientation="horizontal"
            @drop="onColumnDrop($event)"
            drag-handle-selector=".column-drag-handle"
            @drag-start="dragStart">
            <Draggable v-for="column in scene.children" :key="column.id">
                <div :class="column.props.className">
                    <div class="card-column-header">
                        <span class="column-drag-handle">&#x2630;</span>
                        {{ column.name }}
                    </div>
                    <Container
                        group-name="col"
                        @drop="(e) => onCardDrop(column.id, e)"
                        :get-child-payload="getCardPayload(column.id)"
                        drag-class="card-ghost"
                        drop-class="card-ghost-drop"
                    >
                        <Draggable v-for="card in column.children" :key="card.id">
                            <div :class="card.props.className" :style="card.props.style">
                                <div class="d-flex">
                                    <div class="left">
                                        <div class="profile-pic" :style="'background-image: url('+card.props.user.profile_picture+')'"
                                             data-toggle="tooltip" :title="card.props.user.name">
                                        </div>
                                    </div>
                                    <div class="right">
                                        <p @click="taskClick" :data-id="card.props.id">{{ card.data }}</p>
                                    </div>
                                </div>
                            </div>
                        </Draggable>
                    </Container>
                </div>
            </Draggable>
        </Container>
        <edit-task-modal :task_obj="this.task_obj" v-on:success_callback="successEditTask($event)"></edit-task-modal>
    </div>
</template>

<script>
    import {Container, Draggable} from 'vue-smooth-dnd'
    import {applyDrag} from './helpers'
    import editTaskModal from './edit-task-modal'

    const scene = {
        type: 'container',
        props: {
            orientation: 'horizontal'
        },
        children: [],
    };
    export default {
        name: 'Cards',
        components: {
            Container,
            Draggable,
            'edit-task-modal': editTaskModal,
        },
        data() {
            return {
                scene,
                task_obj: {}
            }
        },
        mounted() {
            console.log('Component mounted. kanban');
            this.loadTasks();
        },
        methods: {
            loadTasks() {
                console.log('load tasks');
                const vue_scope = this;
                this.filter_user_id = $(".component-wrapper").data('filter_user_id');
                let url = '';
                if (this.filter_user_id > 0)
                    url = window.location.origin + '/api/pipelines/?user_id=' + this.filter_user_id;
                else
                    url = window.location.origin + '/api/pipelines/';

                axios.get(url).then(({data}) => {
                    vue_scope.scene.children = [];
                    data.pipelines.forEach(pipeline => {
                        let new_pipeline = {
                            id: 'pipeline_' + pipeline.id,
                            type: 'container',
                            name: pipeline.name + ' ' + pipeline.id,
                            props: {
                                id: pipeline.id,
                                orientation: 'vertical',
                                className: 'card-container'
                            },
                        };
                        new_pipeline.children = [];
                        pipeline.tasks.forEach(task => {
                            new_pipeline.children.push({
                                type: 'draggable',
                                id: 'task_' + task.id,
                                props: {
                                    id: task.id,
                                    name: task.name,
                                    category_id: pipeline.id,
                                    className: 'card',
                                    style: {backgroundColor: 'white'},
                                    user: {
                                        name: task.users[0].name,
                                        profile_picture: task.users[0].profile_picture
                                    }
                                },
                                data: task.name
                            });
                        });

                        vue_scope.scene.children.push(new_pipeline);
                    });


                }).then(() => {
                    $('[data-toggle="tooltip"]').tooltip()
                });
            },
            onColumnDrop(dropResult) {
                const scene = Object.assign({}, this.scene);
                scene.children = applyDrag(scene.children, dropResult);
                this.scene = scene
            },
            onCardDrop(columnId, dropResult) {
                if (dropResult.removedIndex !== null || dropResult.addedIndex !== null) {
                    const scene = Object.assign({}, this.scene);
                    const column = scene.children.filter(p => p.id === columnId)[0];
                    const columnIndex = scene.children.indexOf(column);
                    const newColumn = Object.assign({}, column);
                    newColumn.children = applyDrag(newColumn.children, dropResult);
                    scene.children.splice(columnIndex, 1, newColumn);
                    console.log('column id ' + newColumn.props.id);
                    console.log('props ', dropResult);

                    if (dropResult.removedIndex !== null) {
                        console.log('if removedIndex');
                        let task = dropResult.payload.props;
                        this.saveTaskPipelineChange({
                            'id': task.id,
                            'name': task.name
                        });
                    }

                    if (dropResult.addedIndex !== null) {
                        console.log('if addedIndex');
                        let task = dropResult.payload.props;
                        let data = {
                            'id': task.id,
                            'pipeline_position': dropResult.addedIndex,
                            'name': task.name,
                            'pipeline_id': newColumn.props.id,
                        };
                        console.log('data',data);
                        this.saveTaskPipelineChange(data);
                    }

                    this.scene = scene;
                }
            },
            getCardPayload(columnId) {
                return index => {
                    return this.scene.children.filter(p => p.id === columnId)[0].children[index]
                }
            },
            dragStart() {

            },
            log(...params) {
                console.log(...params)
            },
            saveTaskPipelineChange(data) {
                axios.post('/api/task/', data).then(function (response) {
                    console.log(response);
                }).catch(function (error) {
                    console.log(error);
                });
            },
            taskClick(event) {

                let task_el = $(event.target);
                let task_id = task_el.data('id');
                if (task_id === undefined)
                    return;

                axios.get('/api/tasks/' + task_id).then(({data}) => {
                    if (Object.keys(data.payload).length > 0) {

                        this.task_obj = {
                            'id': data.payload.id,
                            'name': data.payload.name,
                            'description': data.payload.description,
                            'start_date': data.payload.start_date.substring(0, 10),
                            'due_date': data.payload.due_date.substring(0, 10),
                            'pipeline_id': data.payload.pipeline_id,
                            'user_id': data.payload.users[0].id
                        };
                        console.log('task obj '+this.task_obj);
                    } else {
                        console.log('not payload at all');
                    }
                });

                $("#editTaskModal").modal('show');
            },
            successEditTask(task) {
                console.log('success edit task', task);
                this.loadTasks();
                // alert('success task');

            }
        },
    }
</script>
<style lang="scss">
    .draggable-item {
        height: 50px;
        line-height: 50px;
        text-align: center;
        width: 100%;
        display: block;
        background-color: #fff;
        outline: 0;
        border: 1px solid rgba(0, 0, 0, .125);
        margin-bottom: 2px;
        margin-top: 2px;
        cursor: default;
        user-select: none;
    }

    .draggable-item-horizontal {
        height: 300px;
        padding: 10px;
        line-height: 100px;
        text-align: center;
        /* width : 200px; */
        display: block;
        background-color: #fff;
        outline: 0;
        border: 1px solid rgba(0, 0, 0, .125);
        margin-right: 4px;
        cursor: default;
    }

    .dragging {
        background-color: yellow;
    }

    .card-scene {
        text-align: left;
    }

    .card-container {
        border-radius: 5px;
        font-size: 14px;
        width: 240px;
        margin: 5px;
        background-color: #f3f3f3;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.24);
    }

    .card {
        margin: 5px;
        /* border: 1px solid #ccc; */
        background-color: white;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.24);
        padding: 5px;
        .left {
            margin-right: 5px;
            .profile-pic {
                width: 50px;
                height: 50px;
                background-origin: center center;
                -webkit-background-size: cover;
                background-size: cover;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
            }
        }
    }

    .card-column-header {
        font-size: 18px;
        margin: 5px;
    }

    .column-drag-handle {
        cursor: move;
    }

    .card-ghost {
        transition: transform 0.18s ease;
        transform: rotateZ(5deg)
    }

    .card-ghost-drop {
        transition: transform 0.18s ease-in-out;
        transform: rotateZ(0deg)
    }

</style>

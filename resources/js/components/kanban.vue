<template>
    <div class="card-scene">
        <Container
                orientation="horizontal"
                @drop="onColumnDrop($event)"
                drag-handle-selector=".column-drag-handle"
                @drag-start="dragStart"
        >
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
                                <p>{{ card.data }}</p>
                            </div>
                        </Draggable>
                    </Container>
                </div>
            </Draggable>
        </Container>
    </div>
</template>

<script>
    import { Container, Draggable } from 'vue-smooth-dnd'
    import { applyDrag } from './helpers'

    const scene = {
        type: 'container',
        props: {
            orientation: 'horizontal'
        },
        children: [],
    };
    export default {
        name: 'Cards',
        components: {Container, Draggable},
        data () {
            return {
                scene
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
                axios.get(window.location.origin + '/api/pipelines/').then(({data}) => {
                    data.pipelines.forEach(pipeline => {
                        let new_pipeline = {
                            id: 'pipeline_'+pipeline.id,
                            type: 'container',
                            name: pipeline.name,
                            props: {
                                orientation: 'vertical',
                                className: 'card-container'
                            },
                        };
                        new_pipeline.children = [];
                        pipeline.tasks.forEach(task => {
                           new_pipeline.children.push({
                               type: 'draggable',
                               id: 'task_'+task.id,
                               props: {
                                   id: task.id,
                                   className: 'card',
                                   style: {backgroundColor: 'white'}
                               },
                               data: task.name
                           });
                        });

                        vue_scope.scene.children.push(new_pipeline);
                    });


                }).then(() => {

                });
            },
            onColumnDrop (dropResult) {
                const scene = Object.assign({}, this.scene);
                scene.children = applyDrag(scene.children, dropResult);
                this.scene = scene
            },
            onCardDrop (columnId, dropResult) {
                if (dropResult.removedIndex !== null || dropResult.addedIndex !== null) {
                    const scene = Object.assign({}, this.scene);
                    const column = scene.children.filter(p => p.id === columnId)[0];
                    const columnIndex = scene.children.indexOf(column);
                    const newColumn = Object.assign({}, column);
                    newColumn.children = applyDrag(newColumn.children, dropResult);
                    scene.children.splice(columnIndex, 1, newColumn);
                    console.log('columnIndex',columnIndex);
                    console.log('new column',newColumn);
                    console.log('drop result ',dropResult);
                    this.scene = scene;
                }
            },
            getCardPayload (columnId) {
                return index => {
                    return this.scene.children.filter(p => p.id === columnId)[0].children[index]
                }
            },
            dragStart () {

            },
            log (...params) {
                console.log(...params)
            }
        }
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
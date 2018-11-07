<template>
    <li :data-start_date="task.start_date" :data-due_date="task.due_date"
        :key="task.id" class="resize-container">
        <div class="d-flex">
            <div @mouseover="taskSegmentHover" @mouseout="taskSegmentHoverOut" @click="taskSegmentClick"
                 class="task resize-drag" :data-id="task.id" :data-name="task.name" :data-description="task.description"
                 :data-start_date="task.start_date" :data-due_date="task.due_date" :id="'task_'+task.id">{{task.name}}
            </div>
        </div>
    </li>
</template>

<script>
    export default {
        name: 'gantt-segment',
        data: function () {
            return {
                count: 0
            }
        },
        props: {
            task: {}
        },
        methods: {
            taskSegmentHover(event) {
                event.target.classList.add('hovered');
            },
            taskSegmentHoverOut(event) {
                event.target.classList.remove('hovered');
            },
            taskSegmentClick(event) {

                $("#segments-list li div.task .tooltip-wrapper").css('display', 'none');
                let segment = $(event.target);

                if (!segment.is('div.task'))
                    return;

                let tooltip = $(".tooltip-wrapper");
                tooltip.css('display', 'block');
                let width = segment.width();
                let element_width = 68;
                tooltip.css('top', event.layerY - 50);
                tooltip.css('left', event.layerX - (element_width / 2) - segment.data('x'));

                try {
                    segment.prepend(tooltip);
                }
                catch (err) {
                    console.log('error', err);
                }
            },
        }
    }
</script>
